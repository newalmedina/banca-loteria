#!/bin/bash


echo "Instalando....."

composer config repositories.posts '{"type": "path", "url": "./packages/clavel/posts/"}'

composer require clavel/posts:@dev --no-scripts --no-update

echo "Composer ...."
COMPOSER_MEMORY_LIMIT=-1 composer update

echo "Instalando ficheros"
php artisan vendor:publish --provider="Clavel\Posts\PostServiceProvider" --force

echo "Composer ...."
composer dumpauto

echo "Base de datos ...."
php artisan migrate

echo "Instalando seeds ..."
php artisan db:seed --class=PostPermissionSeeder
