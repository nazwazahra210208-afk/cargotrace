FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libxml2-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo_mysql mbstring xml curl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && composer install --no-dev --optimize-autoloader --no-interaction

CMD ["sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]