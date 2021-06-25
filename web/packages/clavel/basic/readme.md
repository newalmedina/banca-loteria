# Clavel CMS Package Basic
Paquete de administración con menús front, páginas front y media files

## Instalación
# Instalacion directa con script
```
COMPOSER_MEMORY_LIMIT=-1 composer update
php artisan migrate:fresh --seed
./packages/clavel/basic/tools/install.sh
```

# Añadir en el composer.json
```
"require": {
    "php": "^7.2",
    "illuminate/support": "^6.0",
    ...
    "clavel/basic": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/basic/"

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
$ php artisan vendor:publish --provider="Clavel\Basic\BasicServiceProvider"
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
php artisan db:seed --class=MenuSeeder
php artisan db:seed --class=BasicPermissionSeeder
php artisan db:seed --PageBasicSeeder
```

o bien si no hemos subido la base de datos a producción podriamos añadirlo a /seeds/DatabaseSeeder.php
```
$this->call(MenuSeeder::class);
$this->call(BasicPermissionSeeder::class);
$this->call(PageBasicSeeder::class);
```

opcional si queremos paginas de ejemplo
```
php artisan db:seed --class=PageSeeder
```

## Menús
Para añadir menus en las páginas debemos poner en la plantilla el siguiente fragmento de código.
Previamente eliminaremos los menus manuales
```
<ul class="nav navbar-nav">
    {!! CustomMenu::render('navbar') !!}
</ul>

<ul class="nav navbar-nav navbar-right">
    {!! CustomMenu::render('navbar-right') !!}
 </ul>
```
Por ejemplo en **resources/views/front/includes/header.blade.php**

El menu se presenta siguiendo el estilo que hay definido en el fichero de configuración **/config/menus.php** que en este caso es
```
'navbar' => \Nwidart\Menus\Presenters\Bootstrap\NavbarPresenter::class,
```

Si queremos crear un estilo propio y diferente, creariamos un presenter nuevo en **app\Presenters**

Por ejemplo

```
<?php

namespace App\Presenters;

use Nwidart\Menus\Presenters\Bootstrap\NavbarPresenter;

class FrontMenuNavbarPresenter extends NavbarPresenter
{
    /**
     * {@inheritdoc }.
     */
    public function getOpenTagWrapper()
    {
        return PHP_EOL . '<ul class="nav nav-pills">' . PHP_EOL;
    }
}

```
y cambiariamos en el fichero de configuracion **/config/menus.php**  el estilo de 'navbar' por
```
'navbar' => \App\Presenters\FrontMenuNavbarPresenter::class,
```
