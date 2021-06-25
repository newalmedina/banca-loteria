<?php

namespace Clavel\Basic;

use Clavel\Basic\Services\CustomMenu;
use Illuminate\Filesystem\Filesystem;
use App\Providers\BaseServiceProvider;
use Illuminate\Support\Facades\Config;

class BasicServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->module = "basic";

        $this->init(__DIR__, __NAMESPACE__);

        $this->registerViews(__DIR__);

        $this->publish(__DIR__, "menus");
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

        $this->registerMenuViews($resources_dir);

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


        // Register the controllers
        // Media
        // Register the controllers
        $this->app->make('Clavel\Basic\Controllers\Media\AdminMediaController');
        $this->app->make('Clavel\Basic\Controllers\Media\AdminMediaViewerController');
        $this->app->make('Clavel\Basic\Controllers\Media\MediaController');

        // Menus
        $this->app->make('Clavel\Basic\Controllers\Menu\AdminMenuController');
        $this->app->make('Clavel\Basic\Controllers\Menu\AdminMenuStructureController');
        // Pages
        $this->app->make('Clavel\Basic\Controllers\Pages\AdminPagesController');
        $this->app->make('Clavel\Basic\Controllers\Pages\FrontPagesController');

        // Publicamos el custom menu
        $this->app->singleton("customMenu", function () {
            return new CustomMenu();
        });
    }


    public function provides()
    {
        return ['customMenu'];
    }
}
