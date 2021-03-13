FROM php:7.4-fpm-alpine

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurations
COPY docker/app/php.ini-development /usr/local/etc/php
COPY docker/app/www.conf /usr/local/etc/php-fpm.d/zzz-www.conf

USER www-data

WORKDIR /app/src
