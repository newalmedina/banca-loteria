# Clavel CMS Package Elearning
Paquete de Elearning

# Depende del paquete
* Basic

# Instalacion directa con script
```
COMPOSER_MEMORY_LIMIT=-1 composer update
php artisan migrate:fresh --seed
./packages/clavel/elearning/tools/install.sh
```


Añadir en DatabaseSeeder.php

```
        $this->call(MenuSeeder::class);
        $this->call(BasicPermissionSeeder::class);

        $this->call(TipoContenidoSeeder::class);
        $this->call(TipoModuloSeeder::class);
        $this->call(TiposPreguntas::class);
        $this->call(ElearningPermissionSeeder::class);
        $this->call(ForoPermissionSeeder::class);
        $this->call(ElearningFrontPermissionSeeder::class);
        $this->call(ElearningAsignaturaPermissionSeeder::class);
        $this->call(ElearningAlumnoPermissionSeeder::class);

        $this->call(ProvinciaSeeder::class);
        $this->call(MunicipioSeeder::class);

```

Añadir MAIL_FROM_ADDRESS_CONTACT_US en .env


# Añadir en el composer.json
```
"require": {
    "php": ">=7.1.0",   
    "laravel/framework": "5.7.*",
    ...
    "clavel/elearning": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/elearning/"

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

Para cambiar la plantilla de front por defecto se debe entrar en 
/resources/assets/front 
y
/resources/views/front/includes
/resources/views/front/layouts
y borrar su contenido

```
$ php artisan vendor:publish --provider="Clavel\Elearning\ElearningServiceProvider"
```

primero lanzamos un 
```
composer dumpauto
```
para que encuentre las clases de seed añadidas.

Creamos las tablas de la base de datos 
```
php artisan migrate
```

y añadimos datos.

Obligatorios
```
php artisan db:seed --class=TipoContenidoSeeder
php artisan db:seed --class=TipoModuloSeeder
php artisan db:seed --class=TiposPreguntas
php artisan db:seed --class=ElearningPermissionSeeder
php artisan db:seed --class=ForoPermissionSeeder
php artisan db:seed --class=ElearningFrontPermissionSeeder
php artisan db:seed --class=ElearningAsignaturaPermissionSeeder
php artisan db:seed --class=ElearningAlumnoPermissionSeeder
```

o bien añadirlas a /seeds/DatabaseSeeder.php

```
$this->call(TipoContenidoSeeder::class);
$this->call(TipoModuloSeeder::class);
$this->call(TiposPreguntas::class);
$this->call(ElearningPermissionSeeder::class);
$this->call(ForoPermissionSeeder::class);
$this->call(ElearningFrontPermissionSeeder::class);
$this->call(ElearningAsignaturaPermissionSeeder::class);
$this->call(ElearningAlumnoPermissionSeeder::class);
```

### Seeders para el registro de usuarios
si la e-learning necesita de la información de provincia y municipio de cada usuario se debe ejecutar dos seeders en orden:
``` 
php artisan db:seed --class=ProvinciaSeeder
php artisan db:seed --class=MunicipioSeeder
```
o bien 
```
$this->call(ProvinciaSeeder::class);
$this->call(MunicipioSeeder::class);
```

## Visualización del front

Asegurar que en el fichero Gulp tenemos este copiado de recursos

``` 
function copyfilesFront(cb) {
    try {
        // Plantilla
        gulp.src(paths.resources_base_path + 'front/vendor/**')
            .pipe(gulp.dest(paths.public_path + 'front/vendor/'));

        // Css Genericos
        gulp.src(paths.resources_base_path + 'front/css/**')
            .pipe(gulp.dest(paths.public_path + 'front/css/'));

        // Js Genericos
        gulp.src(paths.resources_base_path + 'front/js/**')
            .pipe(gulp.dest(paths.public_path + 'front/js/'));

        // Js Genericos
        gulp.src(paths.resources_base_path + 'front/fonts/**')
            .pipe(gulp.dest(paths.public_path + 'front/fonts/'));

        cb();
    } catch (e) {
        cb(e);
    }
}
```


Cambiar "general.only_backoffice" a ```false```

Cambiar "general.home_if_authenticated" a ```'/asignaturas'```

Comentar a ruta ```Route::get('/', 'Home\FrontHomeController@index')->name('home')``` en routes/web.php


Ahora instalamos todos los recursos
```
bower install --allow-root
bower update --allow-root
```

y todas las herramientas
```
npm install
```

Compilamos recursos de admin
```
gulp
```

Compilamos recursos de front
```
gulp build
```

Para que el menu salga correctamente (salga en blanco) poner en /config/menus.php el tipo nav-pills
```
'navbar' => \Nwidart\Menus\Presenters\Bootstrap\NavPillsPresenter::class,
'navbar-right' => \Nwidart\Menus\Presenters\Bootstrap\NavPillsPresenter::class,
```

Verificar que en resources/views/includes/sidebar
```
<ul class="nav navbar-nav">
    {!! CustomMenu::render('navbar') !!}
</ul>
@include('front.includes.notifications')
<ul class="nav navbar-nav navbar-right">
    {!! CustomMenu::render('navbar-right') !!}
</ul>
```


Para crear un curso de pruebas ejecutar el seed
```
php artisan db:seed --class=CursoEjemploSeeder
```
y copiar la carpeta data dentro del package a storage. Ver que la ruta es storage ya.


### Desarrollo y estilos específicos del proyecto
todos los cambios de front se tienen que hacer sobre las vistas en ``` resources/views/clavel/elearning ```
