#!/bin/sh
set -e

# Railway assigns a random $PORT at runtime — Apache must listen on it,
# not the hardcoded 80 baked in at build time.
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

if [ -z "${APP_KEY:-}" ]; then
    echo "WARNING: APP_KEY is not set. Set it in Railway's environment variables (generate one with 'php artisan key:generate --show')." >&2
fi

# Idempotent: skip if the symlink already exists (re-running on every deploy is fine either way).
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link || true
fi

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
