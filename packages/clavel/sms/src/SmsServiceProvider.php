<?php
namespace Clavel\Sms;

use App\Providers\BaseServiceProvider;

class SmsServiceProvider extends BaseServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publicaciones
        $this->publishes([
            __DIR__ . '/../config/sms.php' => config_path('sms.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
