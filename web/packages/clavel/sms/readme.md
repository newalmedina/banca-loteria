# Clavel CMS Package - SMS API

Very basic **SMS API**. 



## Instalación
### Añadir en el composer.json
```
"require": {
    "php": ">=7.0.0",   
    "laravel/framework": "5.6.*",
    ...
    "clavel/Sms": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/Sms/"

    }
  ],
```

si no esta ponemos

```
"minimum-stability": "dev",
```

## Publicación de los contenidos

```
$ php artisan vendor:publish --provider="Clavel\Sms\SmsServiceProvider"
```

## Examples ##
```
    $defaultBroker = Illuminate\Support\Facades\Config::get('sms.default');

    $broker = Illuminate\Support\Facades\Config::get('sms.brokers.'.$defaultBroker);

    $sms_service = new \Clavel\Sms\Sms($broker);
    dd($sms_service->broker->send("667786621", "Esto es una prueba"));

```
