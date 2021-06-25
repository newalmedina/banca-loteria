#!/bin/bash


echo "Comprobando codigo duplicado - PHP Copy/Paste Detector (PHPCPD)"
echo "https://github.com/sebastianbergmann/phpcpd"

# Core
./dev-tools/phpcpd-4.1.0.phar --fuzzy --exclude=*/tests/*,*/Views/*,*/Translations/*,*/database/*,*/config/*,*/resources/*,* ./app ./packages
