#!/bin/bash


echo "Instalando....."

echo "Composer ...."
rm -Rf vendor
COMPOSER_MEMORY_LIMIT=-1 composer install

echo "Packages"
rm -Rf node_modules
rm package-lock.json

# En Windows utilizar --no-bin-links y en mac linux no
npm install --no-bin-links
npm update --no-bin-links

echo "Instalamos las dependencias"
npm install --global cross-env

echo "Plantillas admin"
npm run da

echo "Plantillas front"
npm run df
