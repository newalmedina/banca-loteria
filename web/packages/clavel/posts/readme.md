# Clavel CMS Package Posts
Paquete de noticias del CMS

# Depende del paquete
* Basic

## Instalación
# Instalacion directa con script
```
COMPOSER_MEMORY_LIMIT=-1 composer update
php artisan migrate:fresh --seed
./packages/clavel/posts/tools/install.sh
```

# Añadir en el composer.json
```
"require": {
    "php": ">=7.1.0",   
    "laravel/framework": "5.7.*",
    ...
    "clavel/posts": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/posts/"

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
```

## Publicación de los contenidos

```
$ php artisan vendor:publish --provider="Clavel\Posts\PostServiceProvider"
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
php artisan db:seed --class=PostPermissionSeeder
php artisan db:seed --class=PostStatsPermissionSeeder
```

opcional si queremos paginas de ejemplo
```
php artisan db:seed --class=PostSeeder
```

Para que aparezca como modulo accesible en el punto de menu añadir en
**/config/modules**
el módulo
```
<?php

return  [
    'enable' => [
        ...,
        'posts' => [
            "name" => 'Noticias',
            "namespace" => 'Posts',
            "route" => 'posts'
        ]
    ]
];
```
