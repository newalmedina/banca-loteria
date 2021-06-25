#!/bin/bash

echo "Restaurando el sistema"

rm composer.lock
COMPOSER_MEMORY_LIMIT=-1 composer update
php artisan migrate:fresh --seed
