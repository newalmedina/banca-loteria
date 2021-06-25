<?php

namespace App\Modules\Newsletter\Providers;

use App\Providers\BaseServiceProvider;

class NewsletterListServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Modules\Newsletter\Contracts\NewsletterList',
            'App\Modules\Newsletter\Services\Mailchimp\NewsletterList'
        );
    }
}
