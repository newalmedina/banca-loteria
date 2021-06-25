<?php

namespace Clavel\TimeTracker;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use App\Providers\BaseServiceProvider;

class TimeTrackerServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->module = "timetracker";

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

        // Register the controllers

        // Clientes
        $this->app->make('Clavel\TimeTracker\Controllers\Customers\AdminCustomersController');

        // Proyectos
        $this->app->make('Clavel\TimeTracker\Controllers\Projects\AdminProjectsController');

        // Actividades
        $this->app->make('Clavel\TimeTracker\Controllers\Activities\AdminActivitiesController');

        // Hojas de tiempo
        $this->app->make('Clavel\TimeTracker\Controllers\TimeSheet\AdminTimeSheetController');

        // Mis tiempos
        $this->app->make('Clavel\TimeTracker\Controllers\MyTimes\AdminMyTimesController');
    }
}
