# Clavel CMS 2020
Clavel CMS basado en Laravel [Laravel website](http://laravel.com/docs).

## TODO

### Revisar




## Testing y validación
### Las herramientas son
####PHPUnit 
phpunit/phpunit
Para llamar a los test ejecutar el comando desde la línea de comandos

`
./vendor/bin/phpunit -v
`

Lanzar todas las pruebas
`
./vendor/bin/phpunit
`

Para llamar a una prueba concreta
`
./vendor/bin/phpunit --filter=<nombre de la prueba>
./vendor/bin/phpunit --filter=a_user_can_change_is_profile
`



####PHP_CodeSniffer (phpcs)
Detects violations of a specific code style. The debate about whether we should even use a code style is a heated one, but I think it’s a good idea and I don’t have time to explain why here. Configuration files I provided for this tool uses the PSR-2 code style, probably the most widespread code style within the PHP community.
composer require squizlabs/php_codesniffer --dev
https://github.com/squizlabs/PHP_CodeSniffer/wiki
```
./vendor/bin/phpcs -p --colors --standard=PSR2 --ignore=*/tests/*,*/Views/*,*/Translations/*  app 

```
Para autofijar el código con el estandar utilizar. Se puede hacer con todo un directorio pero prefiero solo aplicarlo a ficheros concretos.
```
./vendor/bin/phpcbf -p --colors --standard=PSR2 <nombre del fichero>
```

Ejemplos 
// -p show progress
// -w
./vendor/bin/phpcs -p --colors --standard=PSR2 --ignore=*/tests/*,*/Views/*,*/Translations/*,*/routes/*,*/database/*,*/config/*,*/resources/* ./app
./vendor/bin/phpcs -p --colors --standard=PSR2 --ignore=*/tests/*,*/Views/*,*/Translations/*,*/routes/*,*/database/*,*/config/*,*/resources/* ./packages
 
./vendor/bin/phpcbf -p --colors --standard=PSR2 --ignore=*/tests/*,*/Views/*,*/Translations/*,*/routes/*,*/database/*,*/config/*,*/resources/* ./app
./vendor/bin/phpcbf -p --colors --standard=PSR2 --ignore=*/tests/*,*/Views/*,*/Translations/*,*/routes/*,*/database/*,*/config/*,*/resources/* ./packages
./vendor/bin/phpcbf -p --colors --standard=PSR2 --ignore=*/tests/*,*/Views/*,*/Translations/*,*/routes/*,*/database/*,*/config/*,*/resources/* ./packages/clavel/timetracker/src

//time tracker
./vendor/bin/phpcs -p --colors --standard=PSR2 --ignore=*/tests/*,*/Views/*,*/Translations/*,*/routes/*,*/database/*,*/config/*,*/resources/* ./packages/clavel/timetracker/src
./vendor/bin/phpcbf -p --colors --standard=PSR2 --ignore=*/tests/*,*/Views/*,*/Translations/*,*/routes/*,*/database/*,*/config/*,*/resources/* ./packages/clavel/timetracker/src

####PHP Mess Detector (phpmd)
PHPMD – PHP Mess Detector
This provides some very good metrics of your code quality. I have a blog post explaining the metrics: NPath complexity and cyclomatic complexity explained.
phpmd/phpmd 
```
composer require --dev phpmd/phpmd
```

./vendor/bin/phpmd ./app/ text cleancode,controversial,codesize,design,naming,unusedcode --exclude=app/views,app/storage,app/tests,app/filters.php,app/routes.php,packages/,app/Providers/,app/Console/,app/services/,http/Middleware/,app/Exceptions/,app/Events/
./vendor/bin/phpmd ./packages/ text cleancode,controversial,codesize,design,naming,unusedcode --exclude=*/tests/*,*/Views/*,*/Translations/*,*/routes/*,*/database/*,*/config/*,*/resources/*

//time tracker
./vendor/bin/phpmd ./packages/clavel/timetracker/src text cleancode,controversial,codesize,design,naming,unusedcode --exclude=*/tests/*,*/Views/*,*/Translations/*,*/routes/*,*/database/*,*/config/*,*/resources/*

####Grumpphp
Herramienta que agrupa el resto de herramientas
https://github.com/phpro/grumphp
Para instalarlo

```
composer require --dev phpro/grumphp
```

Para ejecutarlo

```
$ ./vendor/bin/grumphp run
```

####PHP Copy/Paste Detector (phpcpd)
https://github.com/sebastianbergmann/phpcpd
Just as the name suggests, it tries to determine where you have duplicated code in your project.
sebastian/phpcpd

Para instalarlo

```
composer require --dev sebastian/phpcpd
```

Para ejecutarlo

```
$ ./vendor/bin/phpcpd app
```




phploc
Number of lines of code, number of classes, number of interfaces, etc. Nothing fancy, pretty straightforward metrics about your code. Do what you wish with it. What could be interesting is if your code is depending on global stuff, might be a code smell.
phploc/phploc 

pdepend
This tool shows you the quality of your design in the terms of extensibility, reusability and maintainability. This provides some very advanced metrics for your project. If you want to dive deeper I suggest going to the documentation for the software metrics.




PHP_CodeBrowser (phpcb)
Generates a browsable output for your project files. If you have done a good job of using phpdoc in your project, this will give you some good value.
mayflower/php-codebrowser

phpDox
Generates API documentation for your project.
theseer/phpdox:dev-master
 

 


