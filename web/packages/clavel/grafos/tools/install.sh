#!/bin/bash


echo "Instalando....."

composer config repositories.grafos '{"type": "path", "url": "./packages/clavel/grafos/"}'

composer require clavel/grafos:@dev --no-scripts --no-update

echo "Composer ...."
COMPOSER_MEMORY_LIMIT=-1 composer update

echo "Instalando ficheros"
php artisan vendor:publish --provider="Clavel\Grafos\GrafosServiceProvider" --force

echo "Composer ...."
composer dumpauto

echo "Base de datos ...."
php artisan migrate

echo "Instalando seeds ..."
php artisan db:seed --class=GrafosPermissionSeeder

#echo "Packages"
# npm install

#echo "Plantillas admin"
#cd ./resources/libraries/admin/
#bower install --allow-root
#bower update --allow-root

#echo "Gulp admin"
#gulp

#cd ../../../
