# Clavel CMS Package Recognition
Paquete de reconocimiento de imagenes 



## Instalación
# Instalacion directa con script
```
COMPOSER_MEMORY_LIMIT=-1 composer update
php artisan migrate:fresh --seed
./packages/clavel/recognition/tools/install.sh
```

# Añadir en el composer.json
```
"require": {
    "php": "^7.2",
    "illuminate/support": "^6.0",
    ...
    "clavel/recognition": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/recognition/"

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
```
COMPOSER_MEMORY_LIMIT=-1 composer update
```

## Publicación de los contenidos

```
$ php artisan vendor:publish --provider="Clavel\Recognition\RecognitionServiceProvider"
```


