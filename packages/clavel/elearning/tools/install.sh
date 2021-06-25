#!/bin/bash


echo "Instalando....."

composer config repositories.elearning '{"type": "path", "url": "./packages/clavel/elearning/"}'

composer require clavel/elearning:@dev --no-scripts --no-update

echo "eliminamos las views generales"
rm -Rf ./resources/assets/front
rm -Rf ./resources/views/front
rm -Rf ./public/assets/front


echo "Composer ...."
COMPOSER_MEMORY_LIMIT=-1 composer update

echo "Instalando ficheros"
php artisan vendor:publish --provider="Clavel\Elearning\ElearningServiceProvider" --force

echo "Composer ...."
composer dumpauto

echo "Base de datos ...."
php artisan migrate

echo "Instalando seeds ..."
php artisan db:seed --class=TipoContenidoSeeder
php artisan db:seed --class=TipoModuloSeeder
php artisan db:seed --class=TiposPreguntas
php artisan db:seed --class=ElearningPermissionSeeder
php artisan db:seed --class=ForoPermissionSeeder
php artisan db:seed --class=ElearningFrontPermissionSeeder
php artisan db:seed --class=ElearningAsignaturaPermissionSeeder
php artisan db:seed --class=ElearningAlumnoPermissionSeeder

php artisan db:seed --class=ProvinciaSeeder
php artisan db:seed --class=MunicipioSeeder

echo "Cambiando configuración"
php ./packages/clavel/elearning/tools/install elearning:config

echo "Compilando recursos"
npm run da
npm run df
