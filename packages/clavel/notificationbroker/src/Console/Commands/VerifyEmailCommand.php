<?php

namespace Clavel\NotificationBroker\Console\Commands;

use Clavel\NotificationBroker\Services\BounceMail\BounceMailService;
use Illuminate\Console\Command;

class VerifyEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nb:verify-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica los emails con error en la cuenta';


    public $useFetchstructure = true;


    /**
     * Create a new command instance.
     *
     * @return void
     */


    public function handle()
    {
        $bs = new BounceMailService();
        $bs->verify();
    }
}
