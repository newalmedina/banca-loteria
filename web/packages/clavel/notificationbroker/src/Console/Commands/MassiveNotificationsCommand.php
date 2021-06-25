<?php

namespace Clavel\NotificationBroker\Console\Commands;

use Clavel\NotificationBroker\Services\NotificationService;
use Illuminate\Console\Command;

class MassiveNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nb:notifications_broker_massive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =  'Envia de manera masiva las notificaciones cargadas sobre tmp';

    /**
     * Create a new command instance.
     *
     * @return void
     */


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ns = new NotificationService();
        $ns->massive();
    }
}
