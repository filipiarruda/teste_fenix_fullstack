#!/bin/sh
set -e

cd /var/www/html
php artisan migrate --force || true
composer dump-autoload --ignore-platform-reqs 2>/dev/null || true
exec php artisan serve --host=0.0.0.0 --port=8000
