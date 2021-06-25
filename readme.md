# Clavel CMS 

## Arranque proyecto
Creamos la base de datos y configuramos el .env


```
 ./dev-tools/start.sh
```



```
COMPOSER_MEMORY_LIMIT=-1 composer update
php artisan migrate:fresh --seed
```


## Instalación y puesta en marcha
Seguir los pasos de instalación de Laravel


Instalamos los paquetes de node + herramientas 
```
npm install --no-bin-links
```

Si da el error

sh: 1: cross-env: not found

tenemos que instalar el cross-env
```
npm install --global cross-env
```

Si da error por todo es porque tenemos abierto VS Code y/o SourceTree. Los cerramos, borramos node_modules y volvemos ejectuarse


Si da fallos de que no encuentra directorios cerramos el Visual Studio o PHP Storm y ejecutamos
```
rm -rf node_modules
rm package-lock.json
npm i
```

a partir de aqui podemos compilar los recursos cargados con npm y que estan el el fichero webpack.mix.admin.js con
```
npm run da
```
con
```
npm run daw
```
para 'd'esarrollo 'a'dmin y
para 'd'esarrollo 'a'dmin 'w'atching

si cambiamos la 'a' por 'f' es de front

Atención porque esto deja en la carpeta css y js las versiones de desarrollo y no estan optimizadas. Si queremos la versión producción es
```
npm run pa
```
y para front
```
npm run pf
```

## Administracion + FronEnd
En /config/general tenemos la variable

'only_backoffice' => true,

Si sólo queremos administración lo dejamos a true, en la ruta / 

`
Route::get('/', 'Home\FrontHomeController@index')->name('home');
`

hay una redirección hacia la
ruta de /admin



## Verificación del código
Cómo buena practica mantener PSR-2. Para ello utilizar las herramientas que estan en la carpeta **dev-tools**

Para comprobar el estilo de código ejecutar
```
./dev-tools/cs.sh
```
Este fichero se puede editar y añadir o quitar aquellos modulos que se consideren

Para fijar los errores que estan marcados con una x como automatizables utilizar el siguiente comando
```
./dev-tools/cbf.sh
```

Para fijar los estilos 
```
./dev-tools/cf.sh
```

Para detectar vulnerabilidades
```
./dev-tools/phpstan.sh
```

Para detectar copy pastes
```
./dev-tools/cpd.sh
```

Para detectar vulnerabilidades en paquetes
```
./dev-tools/security-checker.sh
```


Si sale este error en cualquiera de los dos anteriores

> bash: ./dev-tools/cs.sh: /bin/bash^M: bad interpreter: No such file or directory

Utilizar la siguiente solución
> To fix, open your script with vi or vim and enter in vi command mode (key Esc), then type this:
>   
>   :set fileformat=unix
>   Finally save it
>   
>   :x! or :wq!
o bien entramos en el Visual Studio Code y en la parte inferior derecha que pone CRLF lo presionamos y cambiamos a LF




# Varios
Si sale este error

 Key path "file:///var/www/html/clavel-cms-2019/web/storage/oauth-private.key" does not exist or is not readable
 
Se tienen que instalar las keys de passport
Instalación claves OAuth iniciales de Passport
php artisan passport:install
php artisan passport:client --personal


# php artisan
php artisan route:list --name=<nombre a buscar>


# PHP unit

https://medium.com/@mscherrenberg/unit-testing-your-api-in-laravel-5-6-7172bcdc593d

./vendor/bin/phpunit

./vendor/bin/phpunit --coverage-html coverage/
o llama a
composer test-coverage

Ejecutar una clase 
./vendor/bin/phpunit --filter UserTest

Ejecutar un método
./vendor/bin/phpunit --filter test_as_user_cannot_update_profile_incorrect_password UserTest tests/Unit/Api/UserTest.php
./vendor/bin/phpunit --filter test_as_user_can_update_profile UserTest tests/Unit/Api/UserTest.php

Crear una prueba
php artisan make:test Api/PostTest --unit

Windows
cd proyectos\www\clavel-cms-2019\web\
D:\software\Sonar\sonar-scanner-4.3.0.2102-windows\bin\sonar-scanner -Dproject.settings=.\sonar-project.properties

Linux (Este tarda la vida)
sonar-scanner -Dproject.settings=./sonar-project.properties

php -dzend_extension=xdebug.so ./vendor/bin/phpunit --configuration phpunit.xml --coverage-clover phpunit.coverage.xml --log-junit phpunit.report.xml

