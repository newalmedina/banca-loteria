<?php

namespace Clavel\NotificationBroker\Console\Commands;

use Clavel\NotificationBroker\Services\NotificationService;
use Illuminate\Console\Command;

class VerifySmsCreditsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nb:verify-sms-credits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulta los créditos y notifica a los usuarios la llegada del límite';

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
        $ns->verifySmsCredits();
    }
}
