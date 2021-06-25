<?php namespace Clavel\TranslatorManager\Console\Commands;

use Clavel\TranslatorManager\Manager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar las traducciones de los ficheros php';


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
        //$replace = $this->option('replace');
        $counter = $this->manager->importTranslations(false, null, false);
        $this->info('Done importing, processed '.$counter.' items!');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['replace', 'R', InputOption::VALUE_NONE, 'Replace existing keys'],
        ];
    }
}
