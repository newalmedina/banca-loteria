<?php

namespace Clavel\NotificationBroker\Console\Commands;

use Clavel\NotificationBroker\Services\NotificationService;
use Illuminate\Console\Command;

class DelayedNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nb:notifications_broker_delayed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =  'Revisa las notificaciones pendientes de envio que no han sido enviadas
             porque fueron solicitadas fuera de los periodos de tiempos permitidos';

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
        $ns->delayed();
    }
}
