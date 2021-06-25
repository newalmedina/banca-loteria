#!/bin/bash


echo "Instalando....."

composer config repositories.crud-generator '{"type": "path", "url": "./packages/clavel/crud-generator/"}'

composer require clavel/crud-generator:@dev --no-scripts --no-update

echo "Composer ...."
COMPOSER_MEMORY_LIMIT=-1 composer update

echo "Instalando ficheros"
php artisan vendor:publish --provider="Clavel\CrudGenerator\CrudGeneratorServiceProvider" --force

echo "Composer ...."
composer dumpauto

echo "Base de datos ...."
php artisan migrate

echo "Instalando seeds ..."
php artisan db:seed --class=CrudGeneratorPermissionSeeder
php artisan db:seed --class=CrudGeneratorDataSeeder

#echo "Packages"
# npm install

#echo "Plantillas admin"
#cd ./resources/libraries/admin/
#bower install --allow-root
#bower update --allow-root

#echo "Gulp admin"
#gulp

#cd ../../../
