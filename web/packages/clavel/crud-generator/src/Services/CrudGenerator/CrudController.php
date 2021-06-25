<?php


namespace Clavel\CrudGenerator\Services\CrudGenerator;

use Clavel\CrudGenerator\Models\Module;
use Clavel\CrudGenerator\Models\ModuleField;
use Clavel\CrudGenerator\Services\CrudGenerator;
use Clavel\CrudGenerator\Services\ModelSelector;
use Illuminate\Support\Str;

class CrudController
{
    private $crudGenerator = null;

    public function __construct(CrudGenerator $crudGenerator)
    {
        $this->crudGenerator = $crudGenerator;
    }

    public function generate()
    {
        $this->controller($this->crudGenerator->module);
    }

    protected function controller(Module $module)
    {
        $name = $module->model;
        $includes = "";

        $controllerPath = $this->crudGenerator->resourcePath . "Controllers/Controller.stub";

        // Icono del modulo
        $modelIcon = "";
        if (!empty($module->icon)) {
            $modelIcon = '<i class="fa ' . $module->icon . '" aria-hidden="true"></i>';
        }

        // Vemos si hay campos multiidioma
        $hasLang = ModuleField::where('crud_module_id', $module->id)
            ->where('can_modify', true)
            ->where('is_multilang', true)
            ->count();

        $langData = "";
        $langDataVar = "";
        $includeLang = "";
        if ($hasLang) {
            // Textos de traducción de create/edit
            $fieldPath = $this->crudGenerator->resourcePath . "Controllers/langData.stub";
            $fieldTemplate = $this->crudGenerator->getStub($fieldPath);
            $langData = $fieldTemplate;
            $langDataVar = ",\n'a_trans'";
            $includeLang = "use App\Modules\{{modelNamePluralUpperCase}}\Models\{{modelName}}Translation;\n" .
                "use App\Services\LanguageService;";
        }

        // Datatable
        $tablePath = $this->crudGenerator->resourcePath . "Controllers/table" . ($hasLang ? "Lang" : "") . "Data.stub";
        $tableData = $this->crudGenerator->getStub($tablePath);

        $tableFieldsArray = [];
        $saveData = "";
        $saveDataLang = "";
        $saveDataLangStub = "";
        $fields = ModuleField::where('crud_module_id', $module->id)
            ->where('in_list', true)
            ->orderBy('order_list', 'ASC')
            ->get();

        $i = 0;
        $hasActive = false;
        foreach ($fields as $field) {
            if ($field->is_multilang) {
                // Si el de idioma utilizamos la join
                $tableFieldsArray[$i++] = "'ct." . $field->column_name . "'";
            } else {
                switch ($field->field_type_slug) {
                    case "belongsToRelationship":
                        $tableFieldsArray[$i++] = "'c." . $field->column_name . "_id'";
                        break;
                    default:
                        // Si el de idioma utilizamos la join
                        $tableFieldsArray[$i++] = "'c." . $field->column_name . "'";
                        break;
                }
            }

            // verificamos si el campo es active para añadir la columna
            if ($field->column_name == 'active') {
                $hasActive = true;
            }
        }


        // Edits
        $additionalLists = "";
        $additionalListsVars = "";
        $saveDataSync = "";
        $uploadFiles = "";
        $fields = ModuleField::where('crud_module_id', $module->id)
            ->where('in_edit', true)
            ->orderBy('order_list', 'ASC')
            ->get();

        $hasMultilangFields = false;
        foreach ($fields as $field) {
            if ($field->is_multilang) {
                switch ($field->field_type_slug) {
                    case "text":
                    case "textarea":
                        $saveDataLang .= '$itemTrans->' . $field->column_name . ' = empty($value["' .
                            $field->column_name . '"])?"":$value["' . $field->column_name . '"];';
                        $hasMultilangFields = true;
                        break;
                }
            } else {
                switch ($field->field_type_slug) {
                    case "radio_yes_no":
                    case "checkbox":
                        $saveData .= '${{modelNameSingularLowerCase}}->' . $field->column_name .
                            ' = $request->input("' . $field->column_name . '", false);';
                        break;
                    case "text":
                    case "textarea":
                    case "password":
                    case "email":
                    case "radio":
                    case "select":
                    case "color":
                        $saveData .= '${{modelNameSingularLowerCase}}->' . $field->column_name .
                            ' = $request->input("' . $field->column_name . '", "");';
                        break;
                    case "image":
                    case "file":
                        $uploadFiles = ", 'files'=>true";

                        $saveData .= '
                            $myServiceSPW = new StoragePathWork("{{modelNamePluralLowerCase}}");
                            ${{modelNameSingularLowerCase}}->' . $field->column_name .
                                ' = $request->input("' . $field->column_name . '", "");

                            if ($request->input("delete_' . $field->column_name . '")==\'1\') {
                                $myServiceSPW->deleteFile(${{modelNameSingularLowerCase}}->' .
                                $field->column_name . ', \'\');
                                ${{modelNameSingularLowerCase}}->' . $field->column_name . '="";
                            }

                            $file = $request->file(\'file_' . $field->column_name . '\',  \'\');

                            if (!empty($file)) {
                                $filename = $myServiceSPW->saveFile($file,  \'\');
                                ${{modelNameSingularLowerCase}}->' . $field->column_name . ' = $filename;
                            }
                            ';

                            // Si no existe el use de la clase lo incluimos
                        if (strpos($includes, "use App\Services\StoragePathWork;") === false) {
                            $includes .= "use App\Services\StoragePathWork;";
                        }
                        break;
                    case "number":
                        $saveData .= '${{modelNameSingularLowerCase}}->' . $field->column_name .
                            ' = $request->input("' . $field->column_name . '", 0);';
                        break;
                    case "float":
                    case "money":
                        $saveData .= '${{modelNameSingularLowerCase}}->' . $field->column_name .
                            ' = $request->input("' . $field->column_name . '", 0.0);';
                        break;
                    case "date":
                    case "datetime":
                    case "time":
                        $saveData .= '${{modelNameSingularLowerCase}}->' . $field->column_name .
                            ' = $request->input("' . $field->column_name . '", null);';
                        break;
                    case "belongsToRelationship":
                        $saveData .= '${{modelNameSingularLowerCase}}->' . $field->column_name .
                            '_id = $request->input("' . $field->column_name . '_id", null);';
                        $modelName = explode(DIRECTORY_SEPARATOR, $field->default_value);

                        $nameSpace = ModelSelector::extractNamespace($field->default_value . ".php");
                        $modelName = explode(DIRECTORY_SEPARATOR, $field->default_value);
                        $className = end($modelName);

                        $fullClassName = $nameSpace . "\\" . $className;
                        // Si no existe el use de la clase lo incluimos
                        if (strpos($includes, "use " . $fullClassName . ";") === false) {
                            $includes .= "use " . $fullClassName . ";";
                        }

                        $additionalLists .= "\$" . Str::plural($field->column_name) . " = " .
                            end($modelName) . "::all()->pluck('" . $field->data .
                            "', 'id')->prepend(trans('global.pleaseSelect'), '');";
                        $additionalListsVars .= ",'" . Str::plural($field->column_name) . "'";

                        break;
                    case "belongsToManyRelationship":
                        $modelName = explode(DIRECTORY_SEPARATOR, $field->default_value);


                        $nameSpace = ModelSelector::extractNamespace($field->default_value . ".php");
                        $modelName = explode(DIRECTORY_SEPARATOR, $field->default_value);
                        $className = end($modelName);

                        $fullClassName = $nameSpace . "\\" . $className;
                        // Si no existe el use de la clase lo incluimos
                        if (strpos($includes, "use " . $fullClassName . ";") === false) {
                            $includes .= "use " . $fullClassName . ";";
                        }

                        $additionalLists .= "\$" . Str::plural($field->column_name) . " = " .
                            end($modelName) . "::all()->pluck('" . $field->data .
                            "', 'id')->prepend(trans('global.pleaseSelect'), '');";
                        $additionalListsVars .= ",'" . Str::plural($field->column_name) . "'";

                        $saveDataSync .= "\${{modelNameSingularLowerCase}}->" .
                            $field->column_name . "()->sync(\$request->input('" . $field->column_name . "', []));";

                        break;
                    case "checkboxMulti":
                        $saveData .= '${{modelNameSingularLowerCase}}->' .
                            $field->column_name . ' = implode(\'|\', $request->input("' .
                            $field->column_name . '", []));';
                        break;
                }
            }
        }

        // Ahora de los datos de idioma creamos el stub
        if ($hasMultilangFields) {
            $tablePath = $this->crudGenerator->resourcePath . "Controllers/saveDataLang.stub";
            $saveDataLangStub = str_replace(
                [
                    '{{__saveDataLang__}}'
                ],
                [
                    $saveDataLang
                ],
                $this->crudGenerator->getStub($tablePath)
            );
        }


        // Verificamos si tenemos que añadir la columna active
        $activeColumnData = "";
        $activeColumn = "";
        $rawColumns = "";
        if ($hasActive) {
            $activePath = $this->crudGenerator->resourcePath . "Controllers/activeColumn.stub";
            $activeColumnData = $this->crudGenerator->getStub($activePath);
            $activeColumn = "'active', ";
            $rawColumns = "'active', ";
        }

        // Hay softdeletes
        $softDeletes = "";
        if ($module->has_soft_deletes) {
            $softDeletes = "->whereNull('c.deleted_at')\r\n";
        }


        $editColumnData = "";
        $fields = ModuleField::where('crud_module_id', $module->id)
            ->where('in_list', true)
            ->where('column_name', '<>', 'id')
            ->where('column_name', '<>', 'active')
            ->orderBy('order_list', 'ASC')
            ->get();


        foreach ($fields as $field) {
            if ($field->is_multilang) {
            } else {
                switch ($field->field_type_slug) {
                    case "radio_yes_no":
                        $editColumnData .= '
                            $table->editColumn(\'' . $field->column_name . '\', function ($row) {
                                return $row->' . $field->column_name .
                            ' == 1 ? trans(\'general/admin_lang.yes\') : trans(\'general/admin_lang.no\');
                            });
                        ';
                        break;
                    case "select":
                        $editColumnData .= '
                            $table->editColumn(\'' . $field->column_name . '\', function ($row) {
                                return $row->' . $field->column_name . ' ? ' .
                            "trans('{{modelNamePluralUpperCase}}::{{modelNamePluralLowerCase}}/admin_lang.fields." .
                            $field->column_name . '_\'.$row->' . $field->column_name . ') : \'\';
                            });
                        ';
                        break;
                }
            }
        }
        $tableFields = implode(",\n", $tableFieldsArray);



        // Exportamos Excel
        $excelExport = "";
        if ($module->has_exports) {
            $tableFieldsHeaderExcelArray = [];
            $tableFieldsSelectExcelArray = [];
            $tableFieldsExcelArray = [];


            $fields = ModuleField::where('crud_module_id', $module->id)
                ->orderBy('order_list', 'ASC')
                ->get();
            $i = 0;

            $hasLang = false;
            foreach ($fields as $field) {
                if ($field->is_multilang) {
                    // Si el de idioma utilizamos la join
                    $tableFieldsSelectExcelArray[$i++] = "'{{modelNameSingularLowerCase}}_translations."
                        . $field->column_name . "'";
                    $tableFieldsExcelArray[$i++] = "\$value->" . $field->column_name;
                    $hasLang = true;
                } else {
                    switch ($field->field_type_slug) {
                        case "belongsToRelationship":
                            $tableFieldsSelectExcelArray[$i++] = "'{{modelNamePluralLowerCase}}." .
                                $field->column_name . "_id'";
                            $tableFieldsExcelArray[$i++] = "\$value->" . $field->column_name . "_id";
                            break;
                        default:
                            $tableFieldsSelectExcelArray[$i++] = "'{{modelNamePluralLowerCase}}." .
                                $field->column_name . "'";
                            $tableFieldsExcelArray[$i++] = "\$value->" . $field->column_name;
                            break;
                    }
                }

                $tableFieldsHeaderExcelArray[$i++] =
                    "trans('{{modelNamePluralUpperCase}}::{{modelNamePluralLowerCase}}/admin_lang.fields." .
                    $field->column_name . "')";
            }
            $tableFieldsSelectExcel = implode(",\n", $tableFieldsSelectExcelArray);
            $tableFieldsHeaderExcel = implode(",\n", $tableFieldsHeaderExcelArray);
            $tableFieldsExcel = implode(",\n", $tableFieldsExcelArray);

            $excelExportPath = $this->crudGenerator->resourcePath .
                "Controllers/excelExportQuery" . ($hasLang ? "Lang" : "") . ".stub";
            $excelExportQuery = $this->crudGenerator->getStub($excelExportPath);



            $excelExportPath = $this->crudGenerator->resourcePath . "Controllers/excelExport.stub";
            $excelExport = str_replace(
                [
                    '{{__tableFieldsHeader__}}',
                    '{{__excelExportQuery__}}',
                    '{{__tableFieldsSelect__}}',
                    '{{__tableFields__}}',
                    '{{modelName}}',
                    '{{modelNamePluralLowerCase}}',
                    '{{modelNameSingularLowerCase}}',
                    '{{modelNamePluralUpperCase}}'

                ],
                [
                    $tableFieldsHeaderExcel,
                    $excelExportQuery,
                    $tableFieldsSelectExcel,
                    $tableFieldsExcel,
                    $name,
                    $module->modelLowerCaselPlural,
                    $module->modelLowerCase,
                    $module->modelPlural

                ],
                $this->crudGenerator->getStub($excelExportPath)
            );

            $includes .= "use App\Helpers\Clavel\ExcelHelper;\n"
                . "use PhpOffice\PhpSpreadsheet\Cell\Coordinate;\n"
                . "use PhpOffice\PhpSpreadsheet\Spreadsheet;\n"
                . "use PhpOffice\PhpSpreadsheet\Style\Fill;\n"
                . "use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;\n"
                . "use PhpOffice\PhpSpreadsheet\Writer\Xlsx;\n"
                . "use PhpOffice\PhpSpreadsheet\IOFactory;\n";
        }


        $controllerTemplate = str_replace(
            [
                '{{__uploadFiles__}}',
                '{{__saveDataSync__}}',
                '{{__additionalLists__}}',
                '{{__additionalListsVars__}}',
                '{{__saveData__}}',
                '{{__saveDataLangStub__}}',
                '{{modelIcon}}',
                '{{__langData__}}',
                '{{__langDataVar__}}',
                '{{__langTable__}}',
                '{{__tableFields__}}',
                '{{__activeColumn__}}',
                '{{__activeColumnData__}}',
                '{{__rawColumns__}}',
                '{{__editColumnData__}}',
                '{{__includeLang__}}',
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{__softDeletes__}}',
                '{{__excelExport__}}',
                '{{__includes__}}',
                '{{modelTableName}}'
            ],
            [
                $uploadFiles,
                $saveDataSync,
                $additionalLists,
                $additionalListsVars,
                $saveData,
                $saveDataLangStub,
                $modelIcon,
                $langData,
                $langDataVar,
                $tableData,
                $tableFields,
                $activeColumn,
                $activeColumnData,
                $rawColumns,
                $editColumnData,
                $includeLang,
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural,
                $softDeletes,
                $excelExport,
                $includes,
                $module->tableName
            ],
            $this->crudGenerator->getStub($controllerPath)
        );

        $controllerDirectory = $this->crudGenerator->destinyPath .
            DIRECTORY_SEPARATOR . $module->modelPlural . DIRECTORY_SEPARATOR . "Controllers";

        if (!file_exists($controllerDirectory)) {
            mkdir($controllerDirectory, 0755, true);
        }

        file_put_contents($controllerDirectory . DIRECTORY_SEPARATOR . "Admin" .
            $module->modelPlural . "Controller.php", $controllerTemplate);
    }
}
