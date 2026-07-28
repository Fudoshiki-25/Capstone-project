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

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Apache should serve Laravel's /public as webroot, with .htaccess rewriting honored.
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf \
    && printf '<Directory /var/www/html/public>\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n' >> /etc/apache2/apache2.conf

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
