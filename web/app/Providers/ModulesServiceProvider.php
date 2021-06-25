<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

// Con este modulos registramos los modulos desarrollados por nosotros en app/Modules/ con toda sus estructura
class ModulesServiceProvider extends ServiceProvider
{
    protected $files;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (is_dir(app_path().'/Modules/')) {
            $modules = config("modules.enable");

            foreach ($modules as $moduleData) {
                $module = $moduleData["name"];
                // Allow routes to be cached
                if (!$this->app->routesAreCached()) {
                    $route_files = [
                        app_path() . '/Modules/' . $module . '/routes.php',
                        app_path() . '/Modules/' . $module . '/routes/web.php',
                        app_path() . '/Modules/' . $module . '/routes/api.php',
                    ];
                    foreach ($route_files as $route_file) {
                        if ($this->files->exists($route_file)) {
                            include $route_file;
                        }
                    }
                }
                $helper = app_path().'/Modules/'.$module.'/helper.php';
                $views  = app_path().'/Modules/'.$module.'/Views';
                $trans  = app_path().'/Modules/'.$module.'/Translations';
                $commands  = app_path().'/Modules/'.$module.'/Console/Commands';

                if ($this->files->exists($helper)) {
                    include_once $helper;
                }
                if ($this->files->isDirectory($views)) {
                    $this->loadViewsFrom($views, $module);
                }
                if ($this->files->isDirectory($trans)) {
                    $this->loadTranslationsFrom($trans, $module);
                }

                if ($this->app->runningInConsole() && $this->files->isDirectory($commands)) {
                    $files = $this->files->files($commands);
                    foreach ($files as $file) {
                        $command = 'App\Modules\\'.$module.'\Console\Commands\\'.$file->getBasename('.php');
                        $this->commands($command);
                    }
                }
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->files = new Filesystem;
    }
}
