<?php

namespace Clavel\TranslatorManager;

use Illuminate\Support\Facades\Config;
use App\Providers\BaseServiceProvider;
use Illuminate\Filesystem\Filesystem;

class TranslatorManagerServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->module = "translator-manager";

        $this->init(__DIR__, __NAMESPACE__);

        $this->registerViews(__DIR__);

        $this->publish(__DIR__, "translator-manager");
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
    }
}
