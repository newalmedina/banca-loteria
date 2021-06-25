#!/bin/bash


echo "Comprobando PHPStan - PHP Static Analysis Tool"
echo "https://github.com/phpstan/phpstan"

# Core
#./vendor/bin/phpstan analyse ./app -c ./dev-tools/phpstan.neon --level=4 --no-progress -vvv
php -d memory_limit=-1 ./vendor/bin/phpstan analyse ./app -c ./dev-tools/phpstan.neon --level=1 --memory-limit=4000M -vvv
# php -d memory_limit=-1 ./vendor/bin/phpstan analyse ./packages/clavel/basic -c ./dev-tools/phpstan.neon --level=1 --memory-limit=4000M -vvv
