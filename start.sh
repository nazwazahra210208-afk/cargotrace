#!/usr/bin/env sh
echo "Starting CargoTrace on Railway..."
php artisan config:clear || true
php artisan route:clear || true
php artisan migrate --force || echo "Migration skipped or failed, continuing to web server..."
php artisan db:seed --force || echo "Seeding skipped or failed, continuing to web server..."

PORT="${PORT:-8080}"
echo "Listening on 0.0.0.0:${PORT}"
exec php -S "0.0.0.0:${PORT}" -t public