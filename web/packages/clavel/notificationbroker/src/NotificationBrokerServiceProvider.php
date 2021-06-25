<?php namespace Clavel\NotificationBroker;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use App\Providers\BaseServiceProvider;

class NotificationBrokerServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->module = "notificationbroker";

        $this->init(__DIR__, __NAMESPACE__);

        $this->registerViews(__DIR__);

        $this->publish(__DIR__, "notificationbroker");
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
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->files = new Filesystem;

        // Register the controllers
        $this->app->make('Clavel\NotificationBroker\Controllers\Whatsapp\WhatsappController');
        $this->app->make('Clavel\NotificationBroker\Controllers\Api\NotificationController');
        $this->app->make('Clavel\NotificationBroker\Controllers\Blacklists\AdminBlacklistsController');
        $this->app->make('Clavel\NotificationBroker\Controllers\Notifications\AdminNotificationsController');
        $this->app->make('Clavel\NotificationBroker\Controllers\Notifications\AdminNotificationsGroupController');
        $this->app->make('Clavel\NotificationBroker\Controllers\Plantillas\AdminPlantillasController');
        $this->app->make('Clavel\NotificationBroker\Controllers\BounceTypes\AdminBounceTypesController');
        $this->app->make('Clavel\NotificationBroker\Controllers\BouncedEmails\AdminBouncedEmailsController');
    }
}
