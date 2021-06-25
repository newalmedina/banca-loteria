#!/bin/bash


echo "Instalando....."

composer config repositories.recognition '{"type": "path", "url": "./packages/clavel/recognition/"}'

composer require clavel/recognition:@dev

echo "Composer ...."
COMPOSER_MEMORY_LIMIT=-1 composer update

echo "Instalando ficheros"
php artisan vendor:publish --provider="Clavel\Recognition\RecognitionServiceProvider" --force

echo "Composer ...."
composer dumpauto

