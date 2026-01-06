#!/bin/bash

# Laravel用ファイル権限の設定
if [ -d "/var/www/html/storage" ]; then
    chmod -R 775 /var/www/html/storage
    chown -R www-data:www-data /var/www/html/storage
fi

if [ -d "/var/www/html/bootstrap/cache" ]; then
    chmod -R 775 /var/www/html/bootstrap/cache
    chown -R www-data:www-data /var/www/html/bootstrap/cache
fi

# PHP-FPMを起動
exec php-fpm



