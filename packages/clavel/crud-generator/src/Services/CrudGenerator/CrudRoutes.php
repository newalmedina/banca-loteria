<?php


namespace Clavel\CrudGenerator\Services\CrudGenerator;

use Clavel\CrudGenerator\Models\Module;
use Clavel\CrudGenerator\Services\CrudGenerator;
use Illuminate\Support\Str;

class CrudRoutes
{
    private $crudGenerator = null;

    public function __construct(CrudGenerator $crudGenerator)
    {
        $this->crudGenerator = $crudGenerator;
    }

    public function generate()
    {
        $this->routes($this->crudGenerator->module, 'web');

        if ($this->crudGenerator->module->has_api_crud) {
            $this->routes($this->crudGenerator->module, 'api');
        }
    }

    protected function routes(Module $module, $filename)
    {
        $name = $module->model;

        $routesPath = $this->crudGenerator->resourcePath . "routes/" . $filename . ".stub";
        $routesTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{has_api_auth}}'
            ],
            [
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural,
                (($this->crudGenerator->module->has_api_crud_secure) ? "auth:" : "")
            ],
            $this->crudGenerator->getStub($routesPath)
        );

        $routesDirectory = $this->crudGenerator
            ->destinyPath . DIRECTORY_SEPARATOR . $module->modelPlural . DIRECTORY_SEPARATOR . "routes";

        if (!file_exists($routesDirectory)) {
            mkdir($routesDirectory, 0755, true);
        }

        file_put_contents($routesDirectory . DIRECTORY_SEPARATOR . $filename . ".php", $routesTemplate);
    }
}
