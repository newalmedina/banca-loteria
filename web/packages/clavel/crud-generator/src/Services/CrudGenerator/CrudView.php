<?php

namespace Clavel\CrudGenerator\Services\CrudGenerator;

use Clavel\CrudGenerator\Models\Module;
use Clavel\CrudGenerator\Models\ModuleField;
use Clavel\CrudGenerator\Services\CrudGenerator;
use Illuminate\Support\Str;

class CrudView
{
    private $crudGenerator = null;

    public function __construct(CrudGenerator $crudGenerator)
    {
        $this->crudGenerator = $crudGenerator;
    }

    public function generate()
    {
        $this->generateIndexView($this->crudGenerator->module);
        $this->generateEditView($this->crudGenerator->module);
    }

    protected function generateIndexView(Module $module)
    {
        $name = $module->model;
        $viewsPath = $this->crudGenerator->resourcePath . "Views/index.blade.stub";

        // Leemos los campos a generar dinamicamente
        $fieldsModule = ModuleField::where('crud_module_id', $module->id)
            ->where('in_list', true)
            ->where('column_name', '<>', 'id')
            ->orderBy('order_list', 'ASC')
            ->get();

        $hasActive = false;
        $tableHeads = "";
        $fields = [];
        $i = 0;
        foreach ($fieldsModule as $field) {
            if ($field->column_name == 'active') {
                $hasActive = true;
                $tableHeads .= "<th scope=\"col\">\n";
                $fields[$i++] = '
                    {
                        "title"         : "{!! trans(\'{{modelNamePluralUpperCase}}::' .
                    '{{modelNamePluralLowerCase}}/admin_lang.fields.' . $field->column_name . '\') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : \'' . $field->column_name . '\',
                        sWidth          : \'50px\'
                    }';
            } else {
                switch ($field->field_type_slug) {
                    case "radio_yes_no":
                        $fields[$i++] = '
                        {
                            "title"         : "{!! trans(\'{{modelNamePluralUpperCase}}::' .
                            '{{modelNamePluralLowerCase}}/admin_lang.fields.' . $field->column_name . '\') !!}",
                            orderable       : false,
                            searchable      : false,
                            data            : \'' . $field->column_name . '\',
                            sWidth          : \'50px\'
                        }';
                        $tableHeads .= "<th scope=\"col\">\n";
                        break;
                    case "text":
                    case "select":
                        $fields[$i++] = '
                        {
                                "title"         : "{!! trans(\'{{modelNamePluralUpperCase}}::' .
                            '{{modelNamePluralLowerCase}}/admin_lang.fields.' . $field->column_name . '\') !!}",
                                orderable       : true,
                                searchable      : true,
                                data            : \'' . $field->column_name . '\',
                                name            : \'c' . ($field->is_multilang ? "t" : "") . '.' .
                            $field->column_name . '\',
                                sWidth          : \'\'
                            }
                        ';
                        $tableHeads .= "<th scope=\"col\">\n";
                        break;
                    case "textarea":
                        $fields[$i++] = '';
                        break;
                }
            }
        }

        $fieldsData = implode(',', $fields) . ",";

        $changeState = "";
        if ($hasActive) {
            $activePath = $this->crudGenerator->resourcePath . "Views/controls/changeState.stub";
            $changeState = $this->crudGenerator->getStub($activePath);
        }

        // Tiene exportaciones Excel
        $excelExports = "";
        if ($module->has_exports) {
            $excelPath =  $this->crudGenerator->resourcePath . "Views/excel.stub";
            $excelExports = $this->crudGenerator->getStub($excelPath);
        }

        // Generamos el fichero final
        $viewsTemplate = str_replace(
            [
                '{{__tableHeads__}}',
                '{{__changeState__}}',
                '{{__fields__}}',
                '{{__excelExports__}}',
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $tableHeads,
                $changeState,
                $fieldsData,
                $excelExports,
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural
            ],
            $this->crudGenerator->getStub($viewsPath)
        );

        $viewsDirectory = $this->crudGenerator
            ->destinyPath . DIRECTORY_SEPARATOR . $module->modelPlural . DIRECTORY_SEPARATOR . "Views";

        if (!file_exists($viewsDirectory)) {
            mkdir($viewsDirectory, 0755, true);
        }

        file_put_contents($viewsDirectory . DIRECTORY_SEPARATOR . "admin_index.blade.php", $viewsTemplate);
    }

    protected function generateEditView(Module $module)
    {
        $this->generateEditViewNoLang($module);
        $this->generateEditViewLang($module);
    }

    protected function generateEditViewNoLang(Module $module)
    {
        $name = $module->model;

        $scriptsStyles = "";
        $scriptsIncludes = "";
        $scriptsData = "";

        // Añadimos los estilos y los scripts por defecto
        $scriptsStyles .= '';
        $scriptsIncludes .= '';


        $fieldData = "";
        // Leemos los campos a generar dinamicamente que no son multiidioma
        $fields = ModuleField::where('crud_module_id', $module->id)
            ->where('in_edit', true)
            ->where('is_multilang', false)
            ->orderBy('order_create', 'ASC')
            ->get();

        $needIncludesDateTime = false;
        $needIncludesColor = false;
        $needIncludesSelect = false;
        foreach ($fields as $field) {
            // Para cada campo leemos su stub y lo rellenamos
            $fieldPath = $this->crudGenerator->resourcePath .
                "Views/form_components/{$field->field_type_slug}.stub";
            $fieldTemplate = $this->crudGenerator->getStub($fieldPath);
            $fieldTemplate = str_replace(
                [
                    '{{fieldName}}',
                    '{{__constFieldName__}}',
                    '{{fieldNamePlural}}',
                ],
                [
                    $field->column_name,
                    strtoupper($field->column_name),
                    Str::plural($field->column_name)
                ],
                $fieldTemplate
            );

            // Verificamos si tenemos que añadir scripts segun el tipo de campo
            switch ($field->field_type_slug) {
                case "datetime":
                case "date":
                case "time":
                    $needIncludesDateTime = true;
                    $scriptsDatePath = $this->crudGenerator->resourcePath .
                        "Views/controls/" . $field->field_type_slug . ".stub";
                    $scriptsData .= str_replace(
                        [
                            '{{__columnName__}}',
                            '{{__columnNameFirstCapital__}}'
                        ],
                        [
                            $field->column_name,
                            Str::ucfirst(strtolower($field->column_name)),
                        ],
                        $this->crudGenerator->getStub($scriptsDatePath)
                    );
                    break;
                case "image":
                case "file":
                    $scriptsImagePath = $this->crudGenerator->resourcePath .
                        "Views/controls/" . $field->field_type_slug . ".stub";
                    $scriptsData .= str_replace(
                        [
                            '{{__columnName__}}',
                            '{{__columnNameFirstCapital__}}'
                        ],
                        [
                            $field->column_name,
                            Str::ucfirst(strtolower($field->column_name)),
                        ],
                        $this->crudGenerator->getStub($scriptsImagePath)
                    );
                    break;
                case "color":
                    $needIncludesColor = true;
                    $scriptsDatePath = $this->crudGenerator->resourcePath .
                        "Views/controls/" . $field->field_type_slug . ".stub";
                    $scriptsData .= str_replace(
                        [
                            '{{__columnName__}}',
                            '{{__columnNameFirstCapital__}}'
                        ],
                        [
                            $field->column_name,
                            Str::ucfirst(strtolower($field->column_name)),
                        ],
                        $this->crudGenerator->getStub($scriptsDatePath)
                    );
                    break;
                case "select":
                    $needIncludesSelect = true;
                    break;
            }

            $fieldData .= $fieldTemplate;
        }
        // Si hemos añadido algun campo de tipo fecha, hora, añadimos los includes pertinentes
        if ($needIncludesDateTime) {
            $scriptsStyles .= '<link
            href="{{ asset(\'/assets/admin/vendor/datetimepicker/css/bootstrap-datetimepicker.min.css\') }}"
            rel="stylesheet" type="text/css" />';
            $scriptsIncludes .= '<script
            src="{{ asset(\'/assets/admin/vendor/moment/moment.min.js\')}}">
            </script>' .
                '<script
                src="{{ asset(\'/assets/admin/vendor/datetimepicker/js/bootstrap-datetimepicker.min.js\')}}">
                </script>';
        }
        if ($needIncludesColor) {
            $scriptsStyles .= '<link
            href="{{ asset("/assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}"
            rel="stylesheet" type="text/css" />';
            $scriptsIncludes .= '<script type="text/javascript"
            src="{{ asset(\'/assets/admin/vendor/colorpicker/js/bootstrap-colorpicker.min.js\')}}">
            </script>';
        }
        // Si hemos añadido algun campo de tipo select, añadimos los includes pertinentes
        if ($needIncludesSelect) {
            $scriptsStyles .= '<link href="{{ asset("/assets/admin/vendor/select2/css/select2-bootstrap.min.css") }}"
            rel="stylesheet" type="text/css" />';
            $scriptsIncludes .= '';
        }

        // Si tiene textarea tenemos que añadir los scripts del editor
        $textareaCount = ModuleField::where('crud_module_id', $module->id)
            ->where('in_edit', true)
            ->where('field_type_slug', 'textarea')
            ->where('use_editor', '=', 'tiny')
            ->count();

        if ($textareaCount > 0) {
            $scriptsIncludes .= '<script type="text/javascript"
            src="{{ asset("assets/admin/vendor/tinymce/tinymce.min.js") }}">
            </script>';
            $scriptsPath = $this->crudGenerator->resourcePath . "Views/controls/textareaScript.stub";
            $scriptsData .= $this->crudGenerator->getStub($scriptsPath);
        }

        // Cargamos el stub de la edicion de campos sin edicion
        $viewsPath = $this->crudGenerator->resourcePath . "Views/edit.blade.stub";
        $viewsTemplate = $this->crudGenerator->getStub($viewsPath);

        // Ahora ponemos los textos de los campos según el modelo
        $viewsTemplate = str_replace(
            [
                '{{__fields__}}',
                '{{__scriptsIncludes__}}',
                '{{__scriptsStyles__}}',
                '{{__scriptsData__}}',
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $fieldData,
                $scriptsIncludes,
                $scriptsStyles,
                $scriptsData,
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural
            ],
            $viewsTemplate
        );

        $viewsDirectory = $this->crudGenerator
            ->destinyPath . DIRECTORY_SEPARATOR . $module->modelPlural . DIRECTORY_SEPARATOR . "Views";

        if (!file_exists($viewsDirectory)) {
            mkdir($viewsDirectory, 0755, true);
        }

        file_put_contents($viewsDirectory . DIRECTORY_SEPARATOR . "admin_edit.blade.php", $viewsTemplate);
    }

    protected function generateEditViewLang(Module $module)
    {
        $name = $module->model;

        $scriptsStyles = "";
        $scriptsIncludes = "";
        $fieldData = "";
        // Leemos los campos a generar dinamicamente que son multiidioma
        $fields = ModuleField::where('crud_module_id', $module->id)
            ->where('in_edit', true)
            ->where('is_multilang', true)
            ->orderBy('order_create', 'ASC')
            ->get();

        // si no hay nada volvemos
        if ($fields->count() == 0) {
            return;
        }


        foreach ($fields as $field) {
            // Para cada campo leemos su stub y lo rellenamos
            $fieldPath = $this->crudGenerator
            ->resourcePath . "Views/form_components/{$field->field_type_slug}_lang.stub";
            $fieldTemplate = $this->crudGenerator->getStub($fieldPath);
            $fieldTemplate = str_replace('{{fieldName}}', $field->column_name, $fieldTemplate);
            switch ($field->field_type_slug) {
                case "select":
                    $data = $field->data;
                    if (!empty($data)) {
                        $someArray = json_decode($data, true);

                        $arrayValores = [];
                        foreach ($someArray as $key => $value) {
                            $arrayValores[] = "'" . $key . "' => '" . $value . "'";
                        }

                        $constSelects = implode(",\n", $arrayValores);
                        $fieldTemplate = str_replace('{{__optionsSelect__}}', $constSelects, $fieldTemplate);
                    }
                    break;
            }
            $fieldData .= $fieldTemplate;
        }

        // Cargamos el stub de la edicion de campos sin edicion
        $viewsPath = $this->crudGenerator->resourcePath . "Views/edit_lang.blade.stub";
        $viewsTemplate = $this->crudGenerator->getStub($viewsPath);

        // Ahora ponemos los textos de los campos según el modelo
        $viewsTemplate = str_replace(
            [
                '{{__fields__}}',
                '{{__scriptsIncludes__}}',
                '{{__scriptsStyles__}}',
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $fieldData,
                $scriptsIncludes,
                $scriptsStyles,
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural
            ],
            $viewsTemplate
        );

        $viewsDirectory = $this->crudGenerator
            ->destinyPath . DIRECTORY_SEPARATOR . $module->modelPlural . DIRECTORY_SEPARATOR . "Views";

        if (!file_exists($viewsDirectory)) {
            mkdir($viewsDirectory, 0755, true);
        }

        file_put_contents($viewsDirectory . DIRECTORY_SEPARATOR . "admin_edit_lang.blade.php", $viewsTemplate);
    }
}
