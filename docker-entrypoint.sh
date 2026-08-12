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

# The Dockerfile's chown only touches the image's own layer at build time —
# it has no effect on a Railway Volume mounted over storage/app/public at
# container start, which brings its own (often root-owned) permissions and
# silently shadows the build-time chown. Re-apply it here, every start, so
# uploads (profile photos, requirement docs, proof of payment) can actually
# write into the mounted volume instead of failing with
# "Unable to create a directory".
mkdir -p /var/www/html/storage/app/public
chown -R www-data:www-data /var/www/html/storage/app/public

# Defense-in-depth: uploaded files are validated (mimes: jpg/jpeg/png/webp/pdf,
# content-checked, not just by extension) and images are fully re-encoded
# through GD before being written here, which already strips embedded
# payloads (polyglot files, appended script code). This .htaccess is a second
# layer in case a future validation bug ever lets something dangerous
# through: even if a .php file somehow landed in this directory, Apache
# would refuse to execute it and just serve it as an inert download instead.
# Written here (not baked into the image) because the Railway Volume mounted
# over this exact path at container start would otherwise silently hide
# anything placed here at build time — same reason the chown above is here
# and not only in the Dockerfile.
cat > /var/www/html/storage/app/public/.htaccess <<'HTACCESS'
<FilesMatch "\.(php|phtml|php[0-9]|phar|pl|py|jsp|asp|aspx|sh|cgi|exe)$">
    Require all denied
</FilesMatch>
HTACCESS

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

# Runs the Laravel scheduler once a minute in the background — this is what
# actually fires scheduled jobs (e.g. tuition due-date reminders, registered
# in routes/console.php). There's no separate worker/cron service in this
# single-container deployment, so this loop is it. Started with & before
# exec so it survives as a child process once exec replaces this shell.
( while true; do php artisan schedule:run >> /var/www/html/storage/logs/schedule.log 2>&1; sleep 60; done ) &

exec "$@"
