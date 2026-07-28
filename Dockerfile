FROM php:8.2-apache

# System deps + PHP extensions this app actually uses:
# - pdo_mysql: MySQL connection
# - gd, exif: intervention/image (profile photos, requirement docs, proof of payment resizing)
# - mbstring, bcmath, zip: standard Laravel requirements
RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libzip-dev \
        libonig-dev \
        unzip \
        git \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo_mysql gd exif mbstring bcmath zip \
    && a2enmod rewrite \
    && (a2dismod mpm_event || true) \
    && (a2dismod mpm_worker || true) \
    && a2enmod mpm_prefork \
    && echo "=== enabled mpm modules ===" && ls -la /etc/apache2/mods-enabled/ | grep -i mpm \
    && rm -rf /var/lib/apt/lists/*

# Default 128M memory_limit isn't enough to decode a full-resolution photo
# before Intervention/Image can scale it down (ImageUploadStorer needs the
# whole bitmap in memory first) — a modern phone photo (e.g. 4000x3000) needs
# ~150MB+ just to decode, causing an uncatchable fatal OOM on profile photo,
# child photo, and proof-of-payment uploads. upload_max_filesize/post_max_size
# bumped too so PHP itself doesn't reject files right at the app's own
# 2MB/5MB validation limits.
RUN { \
        echo 'memory_limit = 256M'; \
        echo 'upload_max_filesize = 8M'; \
        echo 'post_max_size = 16M'; \
    } > /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Apache should serve Laravel's /public as webroot, with .htaccess rewriting
# honored, AND with symlinks followed — public/storage is a symlink to
# storage/app/public (created by `artisan storage:link`), and every uploaded
# file (profile photos, requirement docs, proof of payment) is served through
# it. Options is NOT a merging directive across multiple <Directory> blocks
# for the same path — whichever block Apache treats as authoritative wins
# outright, so this block repeats FollowSymLinks explicitly instead of
# relying on it being inherited from the sed-rewritten block above.
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf \
    && printf '<Directory /var/www/html/public>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n' >> /etc/apache2/apache2.conf

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
