#!/bin/bash


echo "Búsqueda de problemas potenciales en el código"
echo "https://phpmd.org"

#./vendor/bin/phpmd ./app text ./dev-tools/phpmd_ruleset.xml
#./vendor/bin/phpmd ./app ansi cleancode,codesize,design,naming,unusedcode --exclude=app/views,app/storage,app/tests,app/filters.php,app/routes.php,packages/,app/Providers/,app/Console/,app/services/,http/Middleware/,app/Exceptions/,app/Events/
./vendor/bin/phpmd ./app ansi ./dev-tools/phpmd_ruleset.xml --exclude=app/views,app/storage,app/tests,app/filters.php,app/routes.php,packages/,app/Providers/,app/Console/,app/services/,http/Middleware/,app/Exceptions/,app/Events/
