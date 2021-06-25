<?php


namespace Clavel\CrudGenerator\Services\CrudGenerator;

use Clavel\CrudGenerator\Models\Module;
use Clavel\CrudGenerator\Models\ModuleField;
use Clavel\CrudGenerator\Services\CrudGenerator;
use Illuminate\Support\Str;

class CrudTranslation
{
    private $crudGenerator = null;

    public function __construct(CrudGenerator $crudGenerator)
    {
        $this->crudGenerator = $crudGenerator;
    }

    public function generate()
    {
        $this->translations($this->crudGenerator->module);
    }

    protected function translations(Module $module)
    {
        $name = $module->model;
        $moduleName = $module->title;
        $fieldData = "";
        // Leemos los campos a generar dinamicamente
        $fields = ModuleField::where('crud_module_id', $module->id)
            ->orderBy('order_list', 'ASC')
            ->get();
        foreach ($fields as $field) {
            $langData =
                "'{$field->column_name}'  => '{$field->column_title}'," .
                "'{$field->column_name}_helper' => 'Introduce {$field->column_title}'," .
                "'{$field->column_name}_required' => 'El campo {$field->column_title} es obligatorio',";

            switch ($field->field_type_slug) {
                case "number":
                    $langData .=
                        "'{$field->column_name}_integer' =>
                        'El campo {$field->column_title} debe ser numérico entero'," .
                        "'{$field->column_name}_min' =>
                        'El campo {$field->column_title} debe ser como mínimo :min'," .
                        "'{$field->column_name}_max' =>
                        'El campo {$field->column_title} debe ser como máximo :max',";

                    // no break
                case "radio_yes_no":
                    /*
                    $langData .=
                        "'{$field->column_name}_0' => 'Si',".
                        "'{$field->column_name}_1' => 'No',";
*/
                    break;
                case "image":
                    $langData .=
                        "'search_{$field->column_name}'  => 'Selecciona imagen'," .
                        "'quitar_{$field->column_name}' => 'Quitar imagen'," .
                        "'view_{$field->column_name}' => 'Ver imagen',";
                    break;
                case "file":
                    $langData .=
                        "'search_{$field->column_name}'  => 'Selecciona fichero'," .
                        "'quitar_{$field->column_name}' => 'Quitar fichero'," .
                        "'view_{$field->column_name}' => 'Descargar fichero',";
                    break;
                case "select":
                case "radio":
                case "checkboxMulti":
                    // Leemos el data para poner los textos en idioma

                    $data = $field->data;
                    if (!empty($data)) {
                        $someArray = json_decode($data, true);
                        foreach ($someArray as $key => $value) {
                            $langData .= "'{$field->column_name}_{$value[0]}'  => '{$value[1]}',\n";
                        }
                    }

                    break;
            }
            $fieldData .= $langData;
        }

        $translationsPath = $this->crudGenerator->resourcePath . "Translations/es/model/admin_lang.stub";
        $translationsTemplate = str_replace(
            [
                '{{__fields__}}',
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
                $fieldData,
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural,
                $moduleName,
                strtolower(Str::plural($moduleName)),
                strtolower($moduleName),
                Str::plural($moduleName)
            ],
            $this->crudGenerator->getStub($translationsPath)
        );

        $translationsDirectory = $this->crudGenerator
            ->destinyPath . DIRECTORY_SEPARATOR . $module->modelPlural . DIRECTORY_SEPARATOR .
            "Translations/es/" . $module->modelLowerCaselPlural . "/";

        if (!file_exists($translationsDirectory)) {
            mkdir($translationsDirectory, 0755, true);
        }

        file_put_contents($translationsDirectory . DIRECTORY_SEPARATOR . "admin_lang.php", $translationsTemplate);
    }
}
