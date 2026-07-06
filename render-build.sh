#!/usr/bin/env bash
# render-build.sh — Script build otomatis untuk Render.com
# Dijalankan setiap kali ada deploy baru

set -e  # Berhenti jika ada error

echo ">>> [1/5] Install PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo ">>> [2/5] Install & build frontend assets..."
npm ci --ignore-scripts
npm run build

echo ">>> [3/5] Cache Laravel (config, route, view)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">>> [4/5] Jalankan database migrations..."
php artisan migrate --force

echo ">>> [5/5] Selesai! Aplikasi siap dijalankan."
