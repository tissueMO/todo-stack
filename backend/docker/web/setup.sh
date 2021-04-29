#!/bin/sh

touch /var/run/php-fpm/php-fpm.sock
chown -R www-data:www-data /var/run/php-fpm

nginx -g "daemon off;"
