<?php

namespace Clavel\Calculator;

use App\Providers\BaseServiceProvider;
use Illuminate\Filesystem\Filesystem;

class CalculatorServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->module = "calculator";

        $this->init(__DIR__, __NAMESPACE__);

        $this->registerViews(__DIR__);

        $this->publish(__DIR__, "calculator");
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews($resources_dir)
    {
        $viewPath = base_path('resources/views/clavel/'.$this->module);

        $sourcePath = $resources_dir.'/Views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        if ($this->files->isDirectory($sourcePath)) {
            $this->loadViewsFrom(array_merge(array_map(function ($path) {
                return $path . '/clavel/'.$this->module;
            }, \Config::get('view.paths')), [$sourcePath]), $this->module);
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
        $this->app->make('Clavel\Calculator\Controllers\CalculatorController');
    }
}
