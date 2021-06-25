# Clavel CMS Package - SMS/EMAIL/WHATSAPP API

Very basic **SMS/EMAIL/WHATSAPP API**. 


# TODO

# Depende del paquete
* 


## Instalación
### Añadir en el composer.json
```
"require": {
    "php": ">=7.1.0",   
    "laravel/framework": "5.7.*",
    ...
    "clavel/notificationbroker": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/notificationbroker/"

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
$ php artisan vendor:publish --provider="Clavel\NotificationBroker\NotificationBrokerServiceProvider"
```

para que encuentre las nuevas clases de seed añadidas.
```
composer dumpauto
```


Creamos las tablas de la base de datos 
```
php artisan migrate
```

y añadimos datos

para ello primero lanzamos

Obligatorios
```
php artisan db:seed --class=BounceTypes
php artisan db:seed --class=BlacklistsPermissionSeeder
php artisan db:seed --class=BouncedEmailsPermissionSeeder
php artisan db:seed --class=BounceTypesPermissionSeeder
php artisan db:seed --class=NotificationsBrokerGroupSeeder
php artisan db:seed --class=NotificationsBrokerSeeder
php artisan db:seed --class=NotificationsTemplatesSeeder
```
o bien si no hemos subido la base de datos a producción podriamos añadirlo a /seeds/DatabaseSeeder.php
```
$this->call(BounceTypes::class);
$this->call(BlacklistsPermissionSeeder::class);
$this->call(BouncedEmailsPermissionSeeder::class);
$this->call(BounceTypesPermissionSeeder::class);
$this->call(NotificationsBrokerGroupSeeder::class);
$this->call(NotificationsBrokerSeeder::class);
$this->call(NotificationsTemplatesSeeder::class);
```

# Añadir el disk template a Storage
En config/filesystems
añadir
```
'disks' => [

        ...
        'templates' => [
            'driver' => 'local',
            'root' => base_path('packages/clavel/notificationbroker/src/Views/notifications'),
        ],
```
Para despues ser usado en las llamadas
```
Storage::disk('templates')
```
## API
Añadir en config/l5-swagger.php
```
/*
|--------------------------------------------------------------------------
| Absolute path to directory containing the swagger annotations are stored.
|--------------------------------------------------------------------------
*/

'annotations' => [
    base_path('app'),
    base_path('packages'),

],
```
Ejecutar desde la linea de comando
```
php artisan l5-swagger:generate
```
Para verificar si hay errores

Si sale este error

 Key path "file:///var/www/html/clavel-cms-2019/web/storage/oauth-private.key" does not exist or is not readable
 
Se tienen que instalar las keys de passport
Instalación claves OAuth iniciales de Passport
php artisan passport:install
php artisan passport:client --personal


## Informacion de interes
Si sale este error en la verificación de Bounces
```
IMAP error: Can not authenticate to IMAP server: [AUTHENTICATIONFAILED] Invalid credentials (Failure)
```
Hay que ir a la cuenta y bajar la seguridad permitiendo el acceso a aplicaciones menos seguras.
