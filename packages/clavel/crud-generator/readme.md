# Clavel CMS Crud Generator
Paquete para la creación de adminitraciones Crud

Inspirado en 
https://2019.quickadminpanel.com/login
flash.team@gmail.com
A39df%23

# TODO

# Depende del paquete
* 

## Instalación
# Instalacion directa con script
```
COMPOSER_MEMORY_LIMIT=-1 composer update
php artisan migrate:fresh --seed
./packages/clavel/crud-generator/tools/install.sh
```


# Añadir en el composer.json
```
"require": {
    "php": ">=7.1.0",   
    "laravel/framework": "5.7.*",
    ...
    "clavel/crud-generator": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/crud-generator/"

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
$ php artisan vendor:publish --provider="Clavel\CrudGenerator\CrudGeneratorServiceProvider"
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
php artisan db:seed --class=CrudGeneratorPermissionSeeder
php artisan db:seed --class=CrudGeneratorDataSeeder
```
o bien si no hemos subido la base de datos a producción podriamos añadirlo a /seeds/DatabaseSeeder.php
```
$this->call(CrudGeneratorPermissionSeeder::class);
$this->call(CrudGeneratorDataSeeder::class);
```


API
<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StorePromocioneRequest;
use App\Http\Requests\UpdatePromocioneRequest;
use App\Http\Resources\Admin\PromocioneResource;
use App\Promocione;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PromocionesApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('promocione_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PromocioneResource(Promocione::with(['provincia', 'multiples'])->get());
    }

    public function store(StorePromocioneRequest $request)
    {
        $promocione = Promocione::create($request->all());
        $promocione->multiples()->sync($request->input('multiples', []));

        return (new PromocioneResource($promocione))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Promocione $promocione)
    {
        abort_if(Gate::denies('promocione_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PromocioneResource($promocione->load(['provincia', 'multiples']));
    }

    public function update(UpdatePromocioneRequest $request, Promocione $promocione)
    {
        $promocione->update($request->all());
        $promocione->multiples()->sync($request->input('multiples', []));

        return (new PromocioneResource($promocione))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Promocione $promocione)
    {
        abort_if(Gate::denies('promocione_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promocione->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}


RESORUCE
<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PromocioneResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}


REGLAS
 public function rules()
    {
        return [
            'name'        => [
                'min:3',
                'max:5',
                'required'],
            'edad'        => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647'],
            'bithdate'    => [
                'date_format:' . config('panel.date_format'),
                'nullable'],
            'meeting'     => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable'],
            'horacomida'  => [
                'date_format:' . config('panel.time_format'),
                'nullable'],
            'multiples.*' => [
                'integer'],
            'multiples'   => [
                'array'],
        ];
    }
   
## API
Añadir en config/l5-swagger.php los paquetes
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
