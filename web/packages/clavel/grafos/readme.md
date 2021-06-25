# Clavel CMS Grafos
Paquete para la creación de Grafos, VRP y TSP


# TODO

# Depende del paquete
* 

## Instalación
# Instalacion directa con script
```
COMPOSER_MEMORY_LIMIT=-1 composer update
php artisan migrate:fresh --seed
./packages/clavel/grafos/tools/install.sh
```


# Añadir en el composer.json
```
"require": {
    "php": ">=7.1.0",   
    "laravel/framework": "5.7.*",
    ...
    "clavel/grafos": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/grafos/"

    }
  ],
```

si no esta ponemos

```
"minimum-stability": "dev",
```

Llamamos a composer para que reconozca el paquete

```
composer update
COMPOSER_MEMORY_LIMIT=-1 composer update
```


## Publicación de los contenidos

```
$ php artisan vendor:publish --provider="Clavel\Grafos\GrafosServiceProvider"
```

Creamos las tablas de la base de datos 
```
php artisan migrate
```

y añadimos datos

para ello primero lanzamos un 
```
composer dumpauto
```
para que encuentre las clases de seed añadidas. Y luego ejecutamos.

Obligatorios
```
php artisan db:seed --class=GrafosPermissionSeeder
```
o bien si no hemos subido la base de datos a producción podriamos añadirlo a /seeds/DatabaseSeeder.php
```
$this->call(GrafosPermissionSeeder::class);
```
