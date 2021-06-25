<?php namespace Clavel\TranslatorManager\Console\Commands;

use Clavel\TranslatorManager\Manager;
use Illuminate\Console\Command;

class ResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borrar todas las traducciones de la base de datos';


    /** @var  Manager */
    protected $manager;

    /**
     * Create a new command instance.
     *
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->manager->truncateTranslations();
        $this->info('All translations are deleted');
    }
}
