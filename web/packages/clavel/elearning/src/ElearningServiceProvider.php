<?php

namespace Clavel\Elearning;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use App\Providers\BaseServiceProvider;

class ElearningServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->module = "elearning";

        $this->init(__DIR__, __NAMESPACE__);

        $this->registerViews(__DIR__);

        $this->publish(__DIR__, "elearning");

        // Copiamos los datos genericos de la elearning a public como pueden ser plantillas de excel
        $this->publishes([
            __DIR__ . '/../data/storage/plantillas' => public_path('assets/data/'),
        ], 'public');

        // Compiamos el fichero de compilacion de recursos webpackmix
        $this->publishes([
            __DIR__ . '/../resources/webpack.mix.front.js' => base_path('webpack.mix.front.js'),
        ]);
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews($resources_dir)
    {
        // Modulos
        $viewPath = base_path('resources/views/clavel/' . $this->module);

        // Copiamos el punto de menú
        $this->registerMenuViews($resources_dir);

        // Seleccionamos la ruta dentro del paquete donde estan las vistas de los modulos e indicamos que sean
        // accesibles
        $sourcePath = $resources_dir . '/Views/modules';
        if ($this->files->isDirectory($sourcePath)) {
            $this->loadViewsFrom(array_merge(array_map(function ($path) {
                return $path ;
            }, Config::get('view.paths')), [$sourcePath]), $this->module);
        }

        // Template
        // Los recursos que son compilables los copiamos a la carpeta de recursos para
        // ser compilados con laravel mix
        $viewPath = base_path('resources/views/front');
        $sourcePath = $resources_dir . '/Views/template';
        $this->publishes([
            $sourcePath => $viewPath
        ]);
    }



    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->files = new Filesystem;

        // Cursos
        $this->app->make('Clavel\Elearning\Controllers\Cursos\AdminCursosController');

        // Register the controllers
        // Asignaturas
        $this->app->make('Clavel\Elearning\Controllers\Asignaturas\AdminAsignaturasController');
        $this->app->make('Clavel\Elearning\Controllers\Asignaturas\AdminConvocatoriasController');
        $this->app->make('Clavel\Elearning\Controllers\Asignaturas\FrontAsignaturasController');

        // Certificados
        $this->app->make('Clavel\Elearning\Controllers\Certificados\AdminCertificadosController');

        // Códigos
        $this->app->make('Clavel\Elearning\Controllers\Codigos\AdminCodigosController');

        // Contenidos
        $this->app->make('Clavel\Elearning\Controllers\Contenidos\AdminContenidosController');
        $this->app->make('Clavel\Elearning\Controllers\Contenidos\AdminEvaluacionController');
        $this->app->make('Clavel\Elearning\Controllers\Contenidos\AdminEvaluacionRespController');
        $this->app->make('Clavel\Elearning\Controllers\Contenidos\AdminEvaluacionWizardController');
        $this->app->make('Clavel\Elearning\Controllers\Contenidos\FrontContenidoController');


        // Grupos
        $this->app->make('Clavel\Elearning\Controllers\Grupos\AdminGruposController');

        // MisAsignaturas
        $this->app->make('Clavel\Elearning\Controllers\MisAsignaturas\FrontMisAsignaturasController');

        // Modulos
        $this->app->make('Clavel\Elearning\Controllers\Modulos\AdminModulosController');
        $this->app->make('Clavel\Elearning\Controllers\Modulos\AdminConvocatoriasModulosController');
        $this->app->make('Clavel\Elearning\Controllers\Modulos\FrontModulosController');


        // Profesores
        $this->app->make('Clavel\Elearning\Controllers\Cursos\AdminCursosController');

        // User
        $this->app->make('Clavel\Elearning\Controllers\User\FrontUserController');
    }
}
