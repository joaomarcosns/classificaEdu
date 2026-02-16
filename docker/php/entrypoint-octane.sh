#!/usr/bin/env sh
set -e

if [ ! -f artisan ]; then
  echo "artisan not found in /app. Ensure the Laravel app is mounted/copied." >&2
  exit 1
fi

if [ ! -d vendor/laravel/octane ]; then
  echo "laravel/octane not installed. Installing..." >&2
  composer require laravel/octane
  php artisan octane:install --server=frankenphp
fi

exec php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8000
