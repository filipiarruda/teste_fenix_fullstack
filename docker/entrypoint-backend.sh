#!/bin/sh
set -e

cd /var/www/html

# Generate .env from environment variables so Laravel can bootstrap correctly
cat > .env <<EOF
APP_NAME="${APP_NAME:-Fenix Educação}"
APP_ENV=${APP_ENV:-local}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-true}
APP_URL=${APP_URL:-http://localhost:8000}

LOG_CHANNEL=${LOG_CHANNEL:-stderr}
LOG_LEVEL=debug

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-postgres}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-fenix_db}
DB_USERNAME=${DB_USERNAME:-fenix_user}
DB_PASSWORD=${DB_PASSWORD:-fenix_pass}

CACHE_STORE=${CACHE_STORE:-file}
SESSION_DRIVER=${SESSION_DRIVER:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}

REDIS_HOST=${REDIS_HOST:-redis}
REDIS_PASSWORD=${REDIS_PASSWORD:-null}
REDIS_PORT=${REDIS_PORT:-6379}
EOF

php artisan package:discover --ansi || true

# Run migrations
php artisan migrate --force || true

# Clear caches to pick up env variables
php artisan config:clear || true
php artisan route:clear || true

exec php artisan serve --host=0.0.0.0 --port=8000
