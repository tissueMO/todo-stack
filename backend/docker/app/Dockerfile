FROM php:7.4-fpm-buster AS development

# PECL
RUN apt-get -y update && \
    apt-get -y install git libssl-dev openssl ssl-cert && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurations
COPY docker/app/php.ini-development /usr/local/etc/php
COPY docker/app/www.conf /usr/local/etc/php-fpm.d/zzz-www.conf

WORKDIR /app/src

FROM development AS production

ADD src /app/src

WORKDIR /app/src

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_NO_INTERACTION 1

RUN composer install

COPY docker/app/php.ini-production /usr/local/etc/php
