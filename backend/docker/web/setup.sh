#!/bin/sh

touch /var/run/php-fpm/php-fpm.sock
chown -R 82:82 /var/run/php-fpm

nginx -g "daemon off;"
