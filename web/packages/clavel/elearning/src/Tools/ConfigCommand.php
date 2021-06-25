<?php

namespace Clavel\Elearning\Tools;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'elearning:config';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Instala la configuración de la eLearning')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Realiza los cambios en los ficheros de configuración.');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Realizando los cambios',
            '======================',
            '',
        ]);

        // Configuracion general
        $filename =__DIR__.'/../../../../../config/general.php';
        if (file_exists($filename) and is_file($filename)) {
            file_put_contents(
                $filename,
                str_replace(
                    "'home_if_authenticated' => '/'",
                    "'home_if_authenticated' => '/asignaturas'",
                    file_get_contents($filename)
                )
            );
        }

        // Configuracion menus
        $filename =__DIR__.'/../../../../../config/menus.php';
        if (file_exists($filename) and is_file($filename)) {
            $content = file_get_contents($filename);
            $content= str_replace(
                "'navbar' => \Nwidart\Menus\Presenters\Bootstrap\NavbarPresenter::class,",
                "'navbar' => \Nwidart\Menus\Presenters\Bootstrap\NavPillsPresenter::class,",
                $content
            );
            $content= str_replace(
                "'navbar-right' => \Nwidart\Menus\Presenters\Bootstrap\NavbarRightPresenter::class,",
                "'navbar-right' => \Nwidart\Menus\Presenters\Bootstrap\NavPillsPresenter::class,",
                $content
            );
            file_put_contents(
                $filename,
                $content
            );
        }

        $filename =__DIR__.'/../../../../../routes/web.php';
        if (file_exists($filename) and is_file($filename)) {
            file_put_contents(
                $filename,
                str_replace(
                    "Route::get('/', 'Home\FrontHomeController@index')->name('home');",
                    "// Route::get('/', 'Home\FrontHomeController@index')->name('home');",
                    file_get_contents($filename)
                )
            );
        }
        return 0;
    }
}
