<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class ClavelHelpersServiceProvider extends ServiceProvider
{
    protected $files;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->files = new Filesystem;

        $helper = app_path().'/Helpers/Clavel/ExcelHelper.php';

        if ($this->files->exists($helper)) {
            require_once $helper;
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
