<?php

namespace Clavel\CrudGenerator;

use Illuminate\Support\Facades\Config;
use App\Providers\BaseServiceProvider;
use Illuminate\Filesystem\Filesystem;

class CrudGeneratorServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->module = "crud-generator";

        $this->init(__DIR__, __NAMESPACE__);

        $this->registerViews(__DIR__);

        $this->publish(__DIR__);
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
        $this->app->make('Clavel\CrudGenerator\Controllers\CrudGeneratorController');
    }
}
