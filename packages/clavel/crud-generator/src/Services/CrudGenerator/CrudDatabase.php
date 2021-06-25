<?php


namespace Clavel\CrudGenerator\Services\CrudGenerator;

use Clavel\CrudGenerator\Models\Module;
use Clavel\CrudGenerator\Models\ModuleField;
use Clavel\CrudGenerator\Services\CrudGenerator;
use Clavel\CrudGenerator\Services\ModelSelector;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CrudDatabase
{
    private $crudGenerator = null;

    public function __construct(CrudGenerator $crudGenerator)
    {
        $this->crudGenerator = $crudGenerator;
    }

    public function generate()
    {
        $this->database($this->crudGenerator->module);
    }

    protected function database(Module $module)
    {
        $name = $module->model;


        // Vemos si hay campos multiidioma
        $hasLang = ModuleField::where('crud_module_id', $module->id)
            ->where('can_modify', true)
            ->where('is_multilang', true)
            ->count();

        $langData = "";
        $langTableDrop = "";
        if ($hasLang) {
            $langTablePath = $this->crudGenerator->databasePath."migrations/translationTable.stub";
            $langData = $this->crudGenerator->getStub($langTablePath);

            $langTableDrop = "Schema::dropIfExists('{{modelNameSingularLowerCase}}_translations');";
        }

        // Leemos los campos a generar dinamicamente
        $fields = ModuleField::where('crud_module_id', $module->id)
            ->orderBy('order_list', 'ASC')
            ->get();

        $pivotTable = "";
        $fieldsLangArray = [];
        $fieldsArray = [];
        $i = 0;
        $j = 0;
        foreach ($fields as $field) {
            if ($field->column_name == 'created_at' ||
                $field->column_name == 'updated_at' ||
                $field->column_name == 'deleted_at') {
            } else {
                if ($field->is_multilang) {
                    $fieldsLangArray[$i++] = '';
                    switch ($field->field_type_slug) {
                        case "text":
                            $fieldsLangArray[$i++] = '$table->string(\''.$field->column_name.'\');';
                            break;
                        case "textarea":
                            $fieldsLangArray[$i++] = '$table->text(\''.$field->column_name.'\')->nullable();';
                            break;
                    }
                } else {
                    switch ($field->field_type_slug) {
                        case "radio_yes_no":
                            $fieldsArray[$j++] = '$table->boolean(\''.$field->column_name.'\')->default(0);';
                            break;
                        case "text":
                        case "password":
                        case "image":
                        case "file":
                            $fieldsArray[$j++] = '$table->string(\''.$field->column_name.'\');';
                            break;
                        case "textarea":
                            $fieldsArray[$j++] = '$table->text(\''.$field->column_name.'\')->nullable();';
                            break;
                        case "email":
                            $fieldsArray[$j++] = '$table->string(\''.$field->column_name.'\')->unique();';
                            break;
                        case "checkbox":
                            $fieldsArray[$j++] = '$table->boolean(\''.$field->column_name.'\')->default(false);';
                            break;
                        case "number":
                            $db_field = '$table->integer(\''.$field->column_name.'\')';
                            if (!$field->is_required) {
                                $db_field .= '->nullable()';
                            }
                            if (!empty($field->default_value)) {
                                $db_field .= '->default('.$field->default_value.')';
                            }
                            $db_field .= ';';
                            $fieldsArray[$j++] = $db_field;

                            break;
                        case "float":
                            $fieldsArray[$j++] = '$table->float(\''.$field->column_name.'\',15,2)->nullable();';
                            break;
                        case "money":
                            $fieldsArray[$j++] = '$table->decimal(\''.$field->column_name.'\',15,2)->nullable();';
                            break;
                        case "radio":
                        case "select":
                        case "checkboxMulti":
                            $fieldsArray[$j++] = '$table->string(\''.$field->column_name.'\')->nullable();';
                            break;
                        case "date":
                            $fieldsArray[$j++] = '$table->date(\''.$field->column_name.'\')->nullable();';
                            break;
                        case "datetime":
                            $fieldsArray[$j++] = '$table->datetime(\''.$field->column_name.'\')->nullable();';
                            break;
                        case "time":
                            $fieldsArray[$j++] = '$table->time(\''.$field->column_name.'\')->nullable();';
                            break;
                        case "color":
                            $fieldsArray[$j++] = '$table->string(\''.$field->column_name.'\',10)->nullable();';
                            break;
                        case "belongsToRelationship":
                            $nameSpace = ModelSelector::extractNamespace($field->default_value.".php");
                            $modelName = explode(DIRECTORY_SEPARATOR, $field->default_value);
                            $className = end($modelName);

                            $fullClassName = $nameSpace."\\".$className;
                            $model = new $fullClassName;
                            // get the column names for the table
                            $tableName = $model->getTable();

                            $fieldsArray[$j++] = '$table->unsignedInteger(\''.$field->column_name.'_id\')->nullable();';
                            $fieldsArray[$j++] = '$table->foreign(\''.$field->column_name.'_id\',\''.
                            $field->column_name.'_id_fk_966600\')->references(\'id\')->on(\''.$tableName.'\');';
                            break;
                        case "belongsToManyRelationship":
                            $nameSpace = ModelSelector::extractNamespace($field->default_value.".php");
                            $modelName = explode(DIRECTORY_SEPARATOR, $field->default_value);
                            $className = end($modelName);

                            $fullClassName = $nameSpace."\\".$className;
                            $model = new $fullClassName;
                            // get the column names for the table
                            $tableName = $model->getTable();


                            // Primero borraremos las pivot
                            $langTableDrop = "Schema::dropIfExists('{{modelNameSingularLowerCase}}_".
                                $tableName."');\n".$langTableDrop;

                            // Creamos una tabla pivot
                            $pivotTablePath = $this->crudGenerator->databasePath."migrations/pivot_table.stub";
                            $pivotTable = str_replace(
                                [
                                    '{{__tableName__}}',
                                    '{{__columnName__}}',
                                    '{{__columnNamePlural__}}',
                                ],
                                [
                                    strtolower($className),
                                    $field->column_name,
                                    Str::plural($field->column_name)
                                ],
                                $this->crudGenerator->getStub($pivotTablePath)
                            );




                            break;
                    }
                }
            }
        }

        if ($hasLang) {
            $fieldsLang = implode("\n", $fieldsLangArray);
            $langData = str_replace('{{__langFields__}}', $fieldsLang, $langData);
        }

        $fields = implode("\n", $fieldsArray);


        // Ahora miramos si tenemos los softdeletes
        $softDeletes = "";
        if ($module->has_soft_deletes) {
            $softDeletes = "\$table->softDeletes();\r\n";
        }


        $databasePath = $this->crudGenerator->databasePath."migrations/create_table.stub";
        $databaseTemplate = str_replace(
            [
                '{{__pivotTable__}}',
                '{{__langTable__}}',
                '{{__fields__}}',
                '{{__langTableDrop__}}',
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{__softDeletes__}}'
            ],
            [
                $pivotTable,
                $langData,
                $fields,
                $langTableDrop,
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural,
                $softDeletes
            ],
            $this->crudGenerator->getStub($databasePath)
        );

        $databaseDirectory = database_path('migrations');

        if (!file_exists($databaseDirectory)) {
            mkdir($databaseDirectory, 0755, true);
        }

        // Borramos si existe
        $filename = $databaseDirectory.DIRECTORY_SEPARATOR.'*_create_'.$module->modelLowerCaselPlural.'_table.php';
        foreach (glob($filename) as $filefound) {
            unlink($filefound);
        }

        file_put_contents($databaseDirectory.DIRECTORY_SEPARATOR.date('Y_m_d_His', time()) .
            '_create_'.$module->modelLowerCaselPlural.'_table.php', $databaseTemplate);
    }
}
