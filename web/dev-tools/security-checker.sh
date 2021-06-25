#!/bin/bash


echo "Comprobando Security Checker"
echo "https://github.com/sensiolabs/security-checker"

php ./dev-tools/security-checker.phar security:check ./composer.lock
