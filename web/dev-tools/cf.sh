#!/bin/bash


echo "Fijando estilos"
echo "https://github.com/FriendsOfPHP/PHP-CS-Fixer"
# Core
./vendor/bin/php-cs-fixer --rules=@PSR2,line_ending,full_opening_tag,indentation_type fix ./app
./vendor/bin/php-cs-fixer --rules=@PSR2,line_ending,full_opening_tag,indentation_type fix ./packages

