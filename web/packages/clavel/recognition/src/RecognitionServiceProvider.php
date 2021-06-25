<?php

namespace Clavel\Recognition;

use Illuminate\Filesystem\Filesystem;
use App\Providers\BaseServiceProvider;
use Illuminate\Support\Facades\Config;

class RecognitionServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->module = "recognition";

        $this->init(__DIR__, __NAMESPACE__);

        $this->registerViews(__DIR__);

        $this->publish(__DIR__, "recognition");
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews($resources_dir)
    {
        $viewPath = base_path('resources/views/clavel/'.$this->module);

        $sourcePath = $resources_dir . '/Views/modules';

        // La administración no la copiamos.
        /*
        $this->publishes([
            $sourcePath => $viewPath
        ]);
        */

        if ($this->files->isDirectory($sourcePath)) {
            $this->loadViewsFrom(array_merge(array_map(function ($path) {
                return $path . '/clavel/'.$this->module;
            }, Config::get('view.paths')), [$sourcePath]), $this->module);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->files = new Filesystem;


        // Register the controller
        $this->app->make('Clavel\Recognition\Controllers\RecognitionController');
    }
}
