FROM php:7.4-fpm-alpine

# PECL
RUN apk --update add --virtual build-dependencies build-base openssl-dev autoconf && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    apk del build-dependencies build-base openssl-dev autoconf && \
    rm -rf /var/cache/apk/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurations
COPY docker/app/php.ini-development /usr/local/etc/php
COPY docker/app/www.conf /usr/local/etc/php-fpm.d/zzz-www.conf

USER www-data

WORKDIR /app/src
