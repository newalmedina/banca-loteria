#!/bin/bash


echo "Instalando....."

composer config repositories.timetracker '{"type": "path", "url": "./packages/clavel/timetracker/"}'

composer require clavel/timetracker:@dev --no-scripts --no-update

echo "Composer ...."
COMPOSER_MEMORY_LIMIT=-1 composer update

echo "Instalando ficheros"
php artisan vendor:publish --provider="Clavel\TimeTracker\TimeTrackerServiceProvider" --force

echo "Composer ...."
composer dumpauto

echo "Base de datos ...."
php artisan migrate

echo "Instalando seeds ..."
php artisan db:seed --class=TimeTrackerPermissionSeeder

