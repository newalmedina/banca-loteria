<?php

namespace Clavel\CrudGenerator\Services;

use Exception;
use DirectoryIterator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;
use Clavel\CrudGenerator\Models\Module;
use Illuminate\Support\Facades\Artisan;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudApi;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudView;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudModel;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudSeeds;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudTests;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudRoutes;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudRequest;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudDatabase;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudResource;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudController;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Clavel\CrudGenerator\Services\CrudGenerator\CrudTranslation;

class CrudGenerator
{
    public $resourcePath = "";
    public $destinyPath = "";
    public $databasePath = "";
    public $module = null;

    protected $crudViews = null;
    protected $crudController = null;
    protected $crudRequest = null;
    protected $crudModel = null;
    protected $crudTranslation = null;
    protected $crudDatabase = null;
    protected $crudApi = null;
    protected $crudRoutes = null;
    protected $crudResource = null;
    protected $crudSeeds = null;
    protected $crudTests = null;

    public function __construct()
    {
        $this->resourcePath = __DIR__ . '/../../resources/stubs/adminlte2/';
        $this->databasePath = __DIR__ . '/../../resources/stubs/database/';
        $this->destinyPath = app_path('Modules');

        $this->crudModel = new CrudModel($this);
        $this->crudViews = new CrudView($this);
        $this->crudController = new CrudController($this);
        $this->crudRequest = new CrudRequest($this);
        $this->crudTranslation = new CrudTranslation($this);
        $this->crudDatabase = new CrudDatabase($this);
        $this->crudApi = new CrudApi($this);
        $this->crudRoutes = new CrudRoutes($this);
        $this->crudResource = new CrudResource($this);
        $this->crudSeeds = new CrudSeeds($this);
        $this->crudTests = new CrudTests($this);
    }

    public function generateAll(Module $module, $generate = [])
    {
        try {
            $this->module = $module;

            if (isset($generate['clean_all'])) {
                $this->cleanAll($this->module);
                $this->module($this->module);
            }

            if (isset($generate['model'])) {
                $this->crudModel->generate();
            }

            if (isset($generate['controller'])) {
                $this->crudController->generate();
            }

            if (isset($generate['requests'])) {
                $this->crudRequest->generate();
            }

            if (isset($generate['resources'])) {
                $this->crudResource->generate();
            }

            if (isset($generate['api'])) {
                $this->crudApi->generate();
            }

            if (isset($generate['views'])) {
                $this->crudViews->generate();
            }

            if (isset($generate['menu'])) {
                $this->menu($this->module);
            }

            if (isset($generate['routes'])) {
                $this->crudRoutes->generate();
            }

            if (isset($generate['translations'])) {
                $this->crudTranslation->generate();
            }

            if (isset($generate['database'])) {
                $this->crudDatabase->generate();
            }

            if (isset($generate['seeds'])) {
                $this->crudSeeds->generate();
            }

            if (isset($generate['test'])) {
                $this->crudTests->generate();
            }

            if (isset($generate['post'])) {
                $this->system($this->module);
                $this->install($this->module);
                $this->repair($this->module);
            }
        } catch (\Exception $ex) {
            return false;
        }
        return true;
    }

    public function cleanAll(Module $module)
    {
        try {
            $this->clean($module);

            $moduleDirectory = $this->destinyPath . DIRECTORY_SEPARATOR . $module->modelPlural;

            if (!file_exists($moduleDirectory)) {
                mkdir($moduleDirectory, 0755, true);
            }


            $this->system($module);
        } catch (\Exception $ex) {
            return false;
        }
        return true;
    }

    private function chmodR($path, $perm)
    {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $item) {
            chmod($item->getPathname(), 0755);
            if ($item->isDir() && !$item->isDot()) {
                $this->chmodR($item->getPathname(), $perm);
            }
        }
    }

    public function getStub($fileName)
    {
        return file_get_contents($fileName);
    }

    protected function clean(Module $module)
    {
        chmod($this->destinyPath, 0755);
        // Borramos el directorio modulos
        $directory =  $this->destinyPath . DIRECTORY_SEPARATOR . $module->modelPlural;
        if (file_exists($directory)) {
            $this->chmodR($directory, 0755);

            File::deleteDirectory($directory);
        }

        // Borramos el punto de menu
        $menuFile = base_path('resources/views/admin/includes/menu' .
            DIRECTORY_SEPARATOR . $module->modelLowerCase . ".blade.php");
        if (file_exists($menuFile)) {
            unlink($menuFile);
        }

        // Base de datos
        // Antes de borrar los ficheros hacemos un rollback
        $migration = DB::select(
            'select * from migrations where migration LIKE ? limit 1',
            ['%_create_' . $module->modelLowerCaselPlural . '_table']
        );
        if (sizeof($migration) > 0) {
            Artisan::call('migrate:rollback', array('--force' => true));
            //$className = "Create".$module->modelPlural."Table";
            //use CreatePromocionsTable; Class '/CreatePromocionsTable' not found
            //$migrateClass = new $className();
            //$migrateClass->down();
        }

        $databaseDirectory = database_path('migrations');
        $filename = $databaseDirectory . DIRECTORY_SEPARATOR . '*_create_' .
            $module->modelLowerCaselPlural . '_table.php';
        foreach (glob($filename) as $filefound) {
            unlink($filefound);
        }

        $seederFile = database_path('seeds') . DIRECTORY_SEPARATOR . $module->modelPlural . 'PermissionSeeder.php';
        if (file_exists($seederFile)) {
            unlink($seederFile);
        }
    }


    protected function menu($module)
    {
        $modelIcon = "";
        if (!empty($module->icon)) {
            $modelIcon = '<i class="fa ' . $module->icon . '" aria-hidden="true"></i>';
        }

        $name = $module->model;

        $filePath = $this->resourcePath . "Views/admin/includes/menu/menu.blade.stub";
        $fileTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{__iconModule__}}'
            ],
            [
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural,
                $modelIcon
            ],
            $this->getStub($filePath)
        );

        $filesDirectory = base_path('resources/views/admin/includes/menu');

        if (!file_exists($filesDirectory)) {
            mkdir($filesDirectory, 0755, true);
        }

        file_put_contents(
            $filesDirectory . DIRECTORY_SEPARATOR . $module->modelLowerCase . ".blade.php",
            $fileTemplate
        );
    }


    protected function module(Module $module)
    {
        if (!Config::get('modules.enable.' . $module->modelLowerCaselPlural)) {
            $array = Config::get('modules');

            $array['enable'][$module->modelLowerCaselPlural] =  [
                "name" => $module->modelPlural,
                "namespace" => $module->modelPlural,
                "route" => $module->modelLowerCaselPlural
            ];
            $data = var_export($array, 1);

            file_put_contents(app_path() . '/../config/modules.php', "<?php\n return " . $data . " ;");
            Artisan::call('cache:clear');
        }
    }

    protected function system(Module $module)
    {
        $directory = getcwd() . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
        $composer = $this->findComposer();

        $commands = [
            'cd ' . $directory,
            $composer . ' dumpauto',
        ];

        try {
            $result = shell_exec(implode(" && ", $commands));
        } catch (Exception $exception) {
            //dd($exception->getMessage());
        }

        /*
        $composer = $this->findComposer();

        $commands = [
            //$composer.' update',
            $composer.' dumpauto',
        ];

        $directory = getcwd().DIRECTORY_SEPARATOR ."..".DIRECTORY_SEPARATOR;


        $process = new Process([implode(" && ", $commands)], $directory, null, null, null);


        try {
            $result = "";

            $process->run(function ($type, $line) use ($result) {
                $result .= $line;
            });


            // here the process is finished
            if (! $process->isSuccessful()) {
                $result = $process->getErrorOutput();
            } else {
                $result = $process->getOutput();
            }



        } catch (ProcessFailedException $exception) {
            dd($exception->getMessage());
        }
        */
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd() . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
        }

        return 'composer';
    }

    protected function install(Module $module)
    {
        try {
            $className = "Create" . $module->modelPlural . "Table";
            if (!class_exists($className)) {
                $result = Artisan::call(
                    'migrate',
                    [
                        '-q' => null,
                        '-n' => null                    // no-interaction option
                    ]
                );
            }

            /*
            $result = Artisan::call('db:seed', [
                '--class' => $module->modelPlural.'PermissionSeeder'
            ]);
            */

            // El artisan Call no lo encuentra a la primera
            $directory = getcwd() . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;

            $commands = [
                'cd ' . $directory,
                'php artisan db:seed --class=' . $module->modelPlural . 'PermissionSeeder',
            ];

            // Vemos si quiere datos ficticios
            if ($this->module->has_fake_data) {
                $commands[] = 'php artisan db:seed --class=' . $module->modelPlural . 'Seeder';
            }

            try {
                $result = shell_exec(implode(" && ", $commands));
            } catch (Exception $exception) {
                //dd($exception->getMessage());
            }


            $result = Artisan::call('cache:clear');
        } catch (\Exception $ex) {
        }
    }

    protected function repair(Module $module)
    {
        $directory = getcwd() . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
        $composer = $this->findComposer();

        $commands = [
            'cd ' . $directory,
            './vendor/bin/php-cs-fixer --rules=@PSR2,line_ending,full_opening_tag,indentation_type' .
                ' fix ./app/Modules/' . $module->modelPlural,
            './vendor/bin/phpcbf -p --colors --standard=PSR2' .
                ' --ignore=*/tests/*,*/database/*,*/config/*,*/resources/*' .
                ' ./app/Modules/' . $module->modelPlural,
            './vendor/bin/phpcs -p --colors --standard=PSR2' .
                ' --ignore=*/tests/*,*/database/*,*/config/*,*/resources/*' .
                ' ./app/Modules/' . $module->modelPlural,
        ];

        try {
            $result = shell_exec(implode(" && ", $commands));
        } catch (Exception $exception) {
            //dd($exception->getMessage());
        }

        // $commands = [
        //     './vendor/bin/php-cs-fixer --rules=@PSR2,line_ending,full_opening_tag,indentation_type'.
        //        ' fix ./app/Modules/'.$module->modelPlural,
        //     './vendor/bin/phpcbf -p --colors --standard=PSR2'.
        //        ' --ignore=*/tests/*,*/database/*,*/config/*,*/resources/* ./app/Modules/'.$module->modelPlural,
        //     './vendor/bin/phpcs -p --colors --standard=PSR2'.
        //        ' --ignore=*/tests/*,*/database/*,*/config/*,*/resources/* ./app/Modules/'.$module->modelPlural,
        // ];

        // // php-cs-fixer.phar fix /path/to/project

        // $directory = getcwd().DIRECTORY_SEPARATOR ."..".DIRECTORY_SEPARATOR;

        // $process = new Process([implode(' && ', $commands)], $directory, null, null, null);

        // try {
        //     $process->run();
        // } catch (ProcessFailedException $exception) {
        //     dd($exception->getMessage());
        // }
        //echo $process->getOutput();
    }
}
