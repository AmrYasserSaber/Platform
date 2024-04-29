#!/bin/bash

if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-interaction --no-progress --no-suggest
fi

if [ ! -f ".env" ]; then
    echo "Creating env file for env $APP_ENV "
    cp .env.example .env
else
    echo "Env file already exists"
fi

php artisan migrate
php artisan key:generate
php artisan cashe:clear
php artisan config:clear
php artisan route:clear

php artisan serve --port="$PORT" --host="0.0.0.0" --env=.env
exec docker-php-entrypoint "$@"

