<?php namespace Clavel\TranslatorManager\Console\Commands;

use Clavel\TranslatorManager\Manager;
use Illuminate\Console\Command;

class FindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:find';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Localizar las traducciones en ficheros php';


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
        $counter = $this->manager->findTranslations();
        $this->info('Done importing, processed '.$counter.' items!');
    }
}
