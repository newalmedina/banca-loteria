<?php

namespace Clavel\NotificationBroker\Console\Commands;

use Clavel\NotificationBroker\Services\NotificationService;
use Illuminate\Console\Command;

class ValidateNotificationsSmsCertifiedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nb:notifications_broker_validation_sms_certified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se conecta a los proveedores de servicio y verifica
         el envío final desde las plataformas los SMS Certificados';

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
        $ns->validateSMSCertified();
    }
}
