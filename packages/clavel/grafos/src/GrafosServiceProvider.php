<?php

namespace Clavel\Grafos;

use Illuminate\Support\Facades\Config;
use App\Providers\BaseServiceProvider;
use Illuminate\Filesystem\Filesystem;

class GrafosServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->module = "grafos";

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
        $this->app->make('Clavel\Grafos\Controllers\GrafosController');
    }
}
