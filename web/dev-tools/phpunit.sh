#!/bin/bash


echo "Pruebas unitarias con PHPUnit"
echo "https://phpunit.de/"

php -d short_open_tag=off ./vendor/bin/phpunit -v --colors=never --stderr --configuration phpunit.xml
