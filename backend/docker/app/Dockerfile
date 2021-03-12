FROM php:7.4-fpm-alpine

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurations
COPY docker/app/php.ini-development /usr/local/etc/php
COPY docker/app/nginx.conf /usr/local/etc/conf.d

WORKDIR /app
