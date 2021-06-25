#!/bin/bash


echo "Análisis de complejidad de código"
echo "https://github.com/sebastianbergmann/phploc"

./dev-tools/phploc.phar -vvv --exclude=*/tests/*,*/Views/*,*/Translations/*,*/database/*,*/config/*,*/resources/*,* ./app ./packages
