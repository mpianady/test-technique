# Stage 1: vendors
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts

# Stage 2: runtime
FROM php:8.3-apache

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends libzip-dev unzip git; \
    docker-php-ext-configure zip; \
    docker-php-ext-install zip; \
    a2enmod rewrite; \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . .
# Copie des vendors construits dans le stage composer
COPY --from=vendor /app/vendor ./vendor

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN chown -R www-data:www-data /var/www/html
COPY apache/vhost.conf /etc/apache2/sites-available/000-default.conf
