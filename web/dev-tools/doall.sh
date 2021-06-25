#!/bin/bash

echo "Todas"

./dev-tools/phpunit.sh

./dev-tools/cbf.sh

./dev-tools/cf.sh

./dev-tools/cs.sh

./dev-tools/cpd.sh

./dev-tools/phpstan.sh

./dev-tools/security-checker.sh

./dev-tools/phpmd.sh

./dev-tools/phploc.sh

