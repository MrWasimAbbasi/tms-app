#!/bin/bash

sleep 5  # Adjust the time as necessary (in seconds)

php artisan migrate:fresh --seed

apache2-foreground
