<?php


namespace Clavel\CrudGenerator\Services\CrudGenerator;

use Clavel\CrudGenerator\Models\Module;
use Clavel\CrudGenerator\Models\ModuleField;
use Clavel\CrudGenerator\Services\CrudGenerator;
use Clavel\CrudGenerator\Services\ModelSelector;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CrudSeeds
{
    private $crudGenerator = null;

    public function __construct(CrudGenerator $crudGenerator)
    {
        $this->crudGenerator = $crudGenerator;
    }

    public function generate()
    {
        $this->seeds($this->crudGenerator->module);

        $this->generateFakeData($this->crudGenerator->module);
    }

    protected function seeds(Module $module)
    {
        $name = $module->model;


        $moduleName = $module->title;
        $seederPath = $this->crudGenerator->databasePath . "seeds/PermissionSeeder.stub";
        $seederTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{moduleName}}',
                '{{moduleNamePluralLowerCase}}',
                '{{moduleNameSingularLowerCase}}',
                '{{moduleNamePluralUpperCase}}'
            ],
            [
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural,
                $moduleName,
                strtolower(Str::plural($moduleName)),
                strtolower($moduleName),
                Str::plural($moduleName)
            ],
            $this->crudGenerator->getStub($seederPath)
        );

        $seederDirectory = database_path('seeds');

        if (!file_exists($seederDirectory)) {
            mkdir($seederDirectory, 0755, true);
        }

        file_put_contents(
            $seederDirectory . DIRECTORY_SEPARATOR . $module->modelPlural . 'PermissionSeeder.php',
            $seederTemplate
        );
    }

    protected function generateFakeData(Module $module)
    {
        $this->generateFactories($module);

        if ($this->crudGenerator->module->has_fake_data) {
            $this->generateSeeders($module);
        }
    }

    protected function generateFactories(Module $module)
    {
        $name = $module->model;

        $fieldsArray = [];

        $fields = ModuleField::where('crud_module_id', $module->id)
            ->where('in_create', true)
            ->orderBy('order_list', 'ASC')
            ->get();


        foreach ($fields as $field) {
            if ($field->column_name == 'created_at' ||
                $field->column_name == 'updated_at' ||
                $field->column_name == 'deleted_at'
            ) {
            } else {
                if ($field->is_multilang) {
                    switch ($field->field_type_slug) {
                        case "text":
                            $fieldsArray[] = '\'' . $field->column_name . '\' => $faker->word()';
                            break;
                        case "textarea":
                            $fieldsArray[] = '\'' . $field->column_name . '\' => $faker->sentence()';
                            break;
                        default:
                            $fieldsArray[] = '\'' . $field->column_name . '\' => $faker->numberBetween(1,999)';
                    }
                } else {
                    switch ($field->field_type_slug) {
                        case "radio_yes_no":
                            $fieldsArray[] = '\'' . $field->column_name . '\' => $faker->numberBetween(0,1)';
                            break;
                        case "text":
                            $fieldsArray[] = '\'' . $field->column_name . '\' => $faker->word()';
                            break;
                            // case "password":

                            //     break;
                        case "textarea":
                            $fieldsArray[] = '\'' . $field->column_name . '\' => $faker->sentence()';
                            break;
                            // case "email":

                            //     break;
                            // case "checkbox":

                            //     break;
                            // case "number":

                            //     break;
                            // case "float":

                            //     break;
                            // case "money":

                            //     break;
                            // case "radio":
                            // case "select":
                            // case "checkboxMulti":

                            //     break;
                            // case "date":

                            //     break;
                            // case "datetime":

                            //     break;
                            // case "time":

                            //     break;
                            // case "color":

                            //     break;
                            // case "belongsToRelationship":
                            //         break;
                            // case "belongsToManyRelationship":
                            //         break;
                        default:
                            $fieldsArray[] = '\'' . $field->column_name . '\' => $faker->numberBetween(1,999)';
                    }
                }
            }
        }

        $fieldsFields = implode(",\n", $fieldsArray);

        $databasePath = $this->crudGenerator->databasePath . "factories/Factory.stub";
        $databaseTemplate = str_replace(
            [
                '{{__fieldsFields__}}',
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $fieldsFields,
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural
            ],
            $this->crudGenerator->getStub($databasePath)
        );

        $databaseDirectory = database_path('factories');

        if (!file_exists($databaseDirectory)) {
            mkdir($databaseDirectory, 0755, true);
        }

        file_put_contents($databaseDirectory . DIRECTORY_SEPARATOR . $name . "Factory.php", $databaseTemplate);
    }

    protected function generateSeeders(Module $module)
    {
        $name = $module->model;

        $databasePath = $this->crudGenerator->databasePath . "seeds/DataSeeder.stub";
        $databaseTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural
            ],
            $this->crudGenerator->getStub($databasePath)
        );

        $databaseDirectory = database_path('seeds');

        if (!file_exists($databaseDirectory)) {
            mkdir($databaseDirectory, 0755, true);
        }

        file_put_contents(
            $databaseDirectory . DIRECTORY_SEPARATOR . $module->modelPlural . "Seeder.php",
            $databaseTemplate
        );
    }
}
