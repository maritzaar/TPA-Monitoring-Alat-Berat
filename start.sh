#!/bin/bash

# Jalankan migrasi database (harus dengan --force untuk environment production)
echo "Running database migrations..."
php artisan migrate --force

# Menghapus cache config lama dan buat yang baru
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
apache2-foreground
