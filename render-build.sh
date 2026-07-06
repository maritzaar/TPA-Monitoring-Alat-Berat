#!/usr/bin/env bash
# render-build.sh — Build script untuk Render.com
set -e  # stop jika ada error

echo "=== Installing PHP dependencies ==="
composer install --no-dev --optimize-autoloader --no-interaction

echo "=== Installing Node dependencies ==="
npm ci --ignore-scripts

echo "=== Building frontend assets ==="
npm run build

echo "=== Caching Laravel config ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Build complete! ==="
