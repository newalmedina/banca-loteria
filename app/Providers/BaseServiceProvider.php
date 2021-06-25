<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

// Con este modulos registramos los modulos desarrollados por nosotros en app/Modules/ con toda sus estructura
class BaseServiceProvider extends ServiceProvider
{
    protected $files;
    protected $module = "";

    protected function init($resources_dir, $nameSpace)
    {
        // Allow routes to be cached
        if (!$this->app->routesAreCached()) {
            $route_files = [
                $resources_dir . '/routes.php',
                $resources_dir . '/routes/web.php',
                $resources_dir . '/routes/api.php',
            ];
            foreach ($route_files as $route_file) {
                if ($this->files->exists($route_file)) {
                    include $route_file;
                }
            }
        }


        $helper = $resources_dir.'/helper.php';
        $trans  = $resources_dir.'/Translations';
        $commands  = $resources_dir.'/Console/Commands';

        if ($this->files->exists($helper)) {
            include_once $helper;
        }

        if ($this->files->isDirectory($trans)) {
            $this->loadTranslationsFrom($trans, $this->module);
        }

        if ($this->app->runningInConsole() && $this->files->isDirectory($commands)) {
            $files = $this->files->files($commands);
            foreach ($files as $file) {
                $command = $nameSpace . '\\Console\\Commands\\'.$file->getBasename('.php');
                $this->commands($command);
            }
        }
    }

    /**
    * Register views.
    *
    * @return void
    */
    public function registerViews($resources_dir)
    {
        $sourcePath = $resources_dir . '/Views/modules';

        $this->registerMenuViews($resources_dir);

        if ($this->files->isDirectory($sourcePath)) {
            $this->loadViewsFrom(array_merge(array_map(function ($path) {
                return $path . '/clavel/'.$this->module;
            }, Config::get('view.paths')), [$sourcePath]), $this->module);
        }
    }

    protected function registerMenuViews($resources_dir)
    {
        // Modulos
        $viewPath = base_path('resources/views/admin');

        $sourcePath = $resources_dir . '/Views/admin';

        $this->publishes([
            $sourcePath => $viewPath
        ]);
    }

    protected function publish($resources_dir, $config_file_name = "")
    {
        // Publicaciones
        if (!empty($config_file_name)) {
            $this->publishes([
                $resources_dir.'/../config/'.$config_file_name.'.php' => config_path($config_file_name.'.php'),
            ]);
        }

        $this->publishes([
            $resources_dir.'/../resources/assets' => resource_path('assets'),
        ], 'assets');

        $this->publishes([
            $resources_dir.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            $resources_dir.'/../database/seeds' => database_path('seeds')
        ], 'seeds');
        $this->publishes([
            $resources_dir.'/../database/factories' => database_path('factories')
        ], 'factories');
    }
}
