#!/bin/bash

sleep 5  # Adjust the time as necessary (in seconds)

cd /var/www/html

if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

composer install --no-interaction

chmod -R 777 /var/www/html/storage

php artisan migrate:fresh --seed

apache2-foreground

