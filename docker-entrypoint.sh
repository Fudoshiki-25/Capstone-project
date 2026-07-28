#!/bin/sh
set -e

# Railway assigns a random $PORT at runtime — Apache must listen on it,
# not the hardcoded 80 baked in at build time. Matching [0-9]* (not a literal
# "80") makes this idempotent if the entrypoint ever runs twice against the
# same container filesystem (e.g. a restart that reuses the writable layer
# instead of rebuilding from the image) — re-running it is then a no-op
# instead of mangling an already-substituted port.
PORT="${PORT:-80}"
sed -i "s/Listen [0-9]*/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

if [ -z "${APP_KEY:-}" ]; then
    echo "FATAL: APP_KEY is not set. Generate one locally with 'php artisan key:generate --show' and set it in Railway's environment variables." >&2
    exit 1
fi

if [ "${DB_CONNECTION:-}" != "mysql" ]; then
    echo "FATAL: DB_CONNECTION is '${DB_CONNECTION:-unset}', not 'mysql'. Add a MySQL service in Railway and set DB_CONNECTION=mysql plus DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD from it — the migrations use MySQL-only syntax and will fail against sqlite." >&2
    exit 1
fi

# Idempotent: skip if the symlink already exists (re-running on every deploy is fine either way).
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link || true
fi

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Re-assert this at container start (not just build time) in case something
# in the runtime environment re-enables a conflicting MPM between build and
# start — cheap insurance, and the diagnostic output lets us compare the
# mods-enabled state at this exact moment against what the build produced.
a2dismod mpm_event >/dev/null 2>&1 || true
a2dismod mpm_worker >/dev/null 2>&1 || true
a2enmod mpm_prefork >/dev/null 2>&1 || true
echo "=== mods-enabled at container start ==="
ls -la /etc/apache2/mods-enabled/ | grep -i mpm
echo "=== apache2ctl -M at container start ==="
apache2ctl -M 2>&1 || true

exec "$@"
