# Clavel CMS Package Calculator
Prueba de paquete mediante una calculadora

# Añadir en el composer.json
```
"require": {
    "php": ">=7.0.0",   
    "laravel/framework": "5.6.*",
    ...
    "clavel/calculator": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/calculator/"

    }
  ],
```

si no esta ponemos

```
"minimum-stability": "dev",
```

## Publicación de los contenidos

```
$ php artisan vendor:publish --provider="Clavel\Calculator\CalculatorServiceProvider"
```

Ahora podemos modificar las vistas ubicadas en

/resources/views/clavel/<nombre del paquete>

# Prueba
http://clavel.test/add/5/2
http://clavel.test/subtract/5/2