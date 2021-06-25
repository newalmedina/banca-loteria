<?php

namespace Clavel\NotificationBroker\Console\Commands;

use Clavel\NotificationBroker\Services\NotificationService;
use Illuminate\Console\Command;

class ResendSomeNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nb:resend_notifications_broker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reenvia notificaciones de manera controlada. A MEDIDA';

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
        $ns->resend();
    }
}
