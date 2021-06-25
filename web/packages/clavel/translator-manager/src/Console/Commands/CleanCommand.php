<?php


namespace Clavel\TranslatorManager\Console\Commands;

use Clavel\TranslatorManager\Manager;
use Illuminate\Console\Command;

class CleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra las traducciones vacias de la base de datos';


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
        $this->manager->cleanTranslations();
        $this->info('Done cleaning translations');
    }
}
