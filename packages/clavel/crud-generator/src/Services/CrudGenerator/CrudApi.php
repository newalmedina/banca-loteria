<?php


namespace Clavel\CrudGenerator\Services\CrudGenerator;

use Illuminate\Support\Str;
use Clavel\CrudGenerator\Models\Module;
use Clavel\CrudGenerator\Models\ModuleField;
use Clavel\CrudGenerator\Services\CrudGenerator;

class CrudApi
{
    private $crudGenerator = null;

    public function __construct(CrudGenerator $crudGenerator)
    {
        $this->crudGenerator = $crudGenerator;
    }

    public function generate()
    {
        if ($this->crudGenerator->module->has_api_crud) {
            $this->apiDefinition($this->crudGenerator->module);
            $this->api($this->crudGenerator->module);
        }
    }

    protected function apiDefinition(Module $module)
    {
        $name = $module->model;

        $requiredArray = [];
        $propertyArray = [];

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
                    if ($field->is_required) {
                        $requiredArray[] = '"' . $field->column_name . '"';
                    }
                    switch ($field->field_type_slug) {
                            // case "text":

                            //     break;
                            // case "textarea":

                            //     break;
                        default:
                    }
                } else {
                    if ($field->is_required) {
                        $requiredArray[] = '"' . $field->column_name . '"';
                    }

                    switch ($field->field_type_slug) {
                            // case "radio_yes_no":

                            //     break;
                            // case "text":
                            // case "password":

                            //     break;
                            // case "textarea":

                            //     break;
                            // case "email":

                            //     break;
                            // case "checkbox":

                            //     break;
                        case "number":
                            $rule_field = '
                                *   @OA\Property(
                                *      property="' . $field->column_name . '",
                                *      type="integer",
                                *      format="int64",
                                *      readOnly=true
                                *   )
                            ';
                            $propertyArray[] = $rule_field;
                            break;
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
                            //     break;
                            // case "belongsToManyRelationship":
                            //     break;
                        default:
                            $rule_field = '
                                *   @OA\Property(
                                *      property="' . $field->column_name . '",
                                *      type="string"
                                *   )
                            ';
                            $propertyArray[] = $rule_field;
                    }
                }
            }
        }

        $fieldRequired = implode(",", $requiredArray);
        $fieldProperties = implode(",\n", $propertyArray);


        $requestPath = $this->crudGenerator->resourcePath . "Api/OADefinition.stub";
        $requestTemplate = str_replace(
            [
                '{{__fieldRequired__}}',
                '{{__fieldProperties__}}',
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $fieldRequired,
                $fieldProperties,
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural
            ],
            $this->crudGenerator->getStub($requestPath)
        );

        $requestsDirectory = $this->crudGenerator
            ->destinyPath . DIRECTORY_SEPARATOR . $module->modelPlural .
            DIRECTORY_SEPARATOR . "Api" . DIRECTORY_SEPARATOR . "v1";

        if (!file_exists($requestsDirectory)) {
            mkdir($requestsDirectory, 0755, true);
        }

        file_put_contents($requestsDirectory . DIRECTORY_SEPARATOR . "OADefinition.php", $requestTemplate);
    }


    protected function api(Module $module)
    {
        $name = $module->model;

        $rulesArray = [];

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
                    // switch ($field->field_type_slug) {
                    //     case "text":

                    //         break;
                    //     case "textarea":

                    //         break;
                    // }
                } else {
                    switch ($field->field_type_slug) {
                            // case "radio_yes_no":

                            //     break;
                            // case "text":
                            // case "password":

                            //     break;
                            // case "textarea":

                            //     break;
                            // case "email":

                            //     break;
                            // case "checkbox":

                            //     break;
                        case "number":
                            $rule_field = '\'' . $field->column_name . '\' => \'';
                            $sep = '';
                            // Forzamos a numerico
                            $rule_field .= $sep . 'integer';
                            $sep = '|';
                            // ¿Es obligatorio?
                            if ($field->is_required) {
                                $rule_field .= $sep . 'required';
                                $sep = '|';
                            }
                            // Valor mínimo
                            if (!empty($field->min_length)) {
                                $rule_field .= $sep . 'min:' . $field->min_length;
                                $sep = '|';
                            }
                            // Valor maximo
                            if (!empty($field->max_length)) {
                                $rule_field .= $sep . 'max:' . $field->max_length;
                                $sep = '|';
                            }
                            $rule_field .= '\'';

                            $rulesArray[] = $rule_field;

                            break;
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

                            //     break;
                            // case "belongsToManyRelationship":
                            //     break;
                        default:
                            if ($field->is_required) {
                                $rulesArray[] = '\'' . $field->column_name . '\' => \'required\'';
                            }
                    }
                }
            }
        }

        $fieldRules = implode(",\n", $rulesArray);


        $requestPath = $this->crudGenerator->resourcePath . "Api/Api.stub";
        $requestTemplate = str_replace(
            [
                '{{__fieldRules__}}',
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $fieldRules,
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural
            ],
            $this->crudGenerator->getStub($requestPath)
        );

        $requestsDirectory = $this->crudGenerator
            ->destinyPath . DIRECTORY_SEPARATOR .
            $module->modelPlural . DIRECTORY_SEPARATOR . "Api" . DIRECTORY_SEPARATOR . "v1";

        if (!file_exists($requestsDirectory)) {
            mkdir($requestsDirectory, 0755, true);
        }

        file_put_contents(
            $requestsDirectory . DIRECTORY_SEPARATOR . $module->modelPlural . "ApiController.php",
            $requestTemplate
        );
    }
}
