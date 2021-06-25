# Clavel CMS Translator Manager
Paquete para soporte en la traducción de idiomas

basado en https://github.com/barryvdh/laravel-translation-manager

## Instalación
# Instalacion directa con script
```
COMPOSER_MEMORY_LIMIT=-1 composer update
php artisan migrate:fresh --seed
./packages/clavel/translator-manager/tools/install.sh
```

# Añadir en el composer.json
```
"require": {
    "php": ">=7.1.0",   
    "laravel/framework": "5.7.*",
    ...
    "clavel/translator-manager": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/translator-manager/"

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
$ php artisan vendor:publish --provider="Clavel\TranslatorManager\TranslatorManagerServiceProvider"
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
php artisan db:seed --class=TranslatorManagerPermissionSeeder
```

## Auto translate
https://cloud.google.com/translate/
https://console.developers.google.com
