#!/bin/sh
set -e

php /var/www/html/artisan migrate --force --no-interaction

php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache

/usr/bin/supervisord -c /etc/supervisord.conf
