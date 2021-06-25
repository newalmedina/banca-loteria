<?php

namespace Clavel\CrudGenerator\Services\CrudGenerator;

use Clavel\CrudGenerator\Models\Module;
use Clavel\CrudGenerator\Models\ModuleField;
use Clavel\CrudGenerator\Services\CrudGenerator;
use Clavel\CrudGenerator\Services\ModelSelector;
use Illuminate\Support\Str;

class CrudRequest
{
    private $crudGenerator = null;

    public function __construct(CrudGenerator $crudGenerator)
    {
        $this->crudGenerator = $crudGenerator;
    }

    public function generate()
    {
        $this->request($this->crudGenerator->module);
    }

    protected function request(Module $module)
    {
        $name = $module->model;

        // Vemos si hay campos multiidioma
        $hasLang = ModuleField::where('crud_module_id', $module->id)
            ->where('can_modify', true)
            ->where('is_multilang', true)
            ->count();

        $langData = "";
        if ($hasLang) {
            $langData = '$this->locale = app()->getLocale();';
        }


        $rulesArray = [];
        $messagesArray = [];

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
                        $rulesArray[] = '$this->validationRules[\'lang.\'.$this->locale.\'.' .
                            $field->column_name . '\'] = \'required\';';
                        $messagesArray[] = '\'lang.\'.$this->locale.\'.' . $field->column_name .
                            '.required\' => trans(\'{{modelNamePluralUpperCase}}::' .
                            '{{modelNamePluralLowerCase}}/admin_lang.fields.' .
                            $field->column_name . '_required\')';
                    }
                    switch ($field->field_type_slug) {
                        case "text":
                            break;
                        case "textarea":
                            break;
                    }
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
                            $rule_field = '$this->validationRules[\'' . $field->column_name . '\'] = \'';
                            $sep = '';
                            // Forzamos a numerico
                            $rule_field .= $sep . 'integer';
                            $messagesArray[] = '\'' . $field->column_name .
                                '.integer\' => trans(\'{{modelNamePluralUpperCase}}::' .
                                '{{modelNamePluralLowerCase}}/admin_lang.fields.' .
                                $field->column_name . '_integer\')';
                            $sep = '|';
                            // ¿Es obligatorio?
                            if ($field->is_required) {
                                $rule_field .= $sep . 'required';
                                $sep = '|';
                                $messagesArray[] = '\'' . $field->column_name .
                                    '.required\' => trans(\'{{modelNamePluralUpperCase}}::' .
                                    '{{modelNamePluralLowerCase}}/admin_lang.fields.' .
                                    $field->column_name . '_required\')';
                            }
                            // Valor mínimo
                            if (!empty($field->min_length)) {
                                $rule_field .= $sep . 'min:' . $field->min_length;
                                $sep = '|';
                                $messagesArray[] = '\'' . $field->column_name .
                                    '.min\' => trans(\'{{modelNamePluralUpperCase}}::' .
                                    '{{modelNamePluralLowerCase}}/admin_lang.fields.' .
                                    $field->column_name . '_min\')';
                            }
                            // Valor maximo
                            if (!empty($field->max_length)) {
                                $rule_field .= $sep . 'max:' . $field->max_length;
                                $sep = '|';
                                $messagesArray[] = '\'' . $field->column_name .
                                    '.max\' => trans(\'{{modelNamePluralUpperCase}}::' .
                                    '{{modelNamePluralLowerCase}}/admin_lang.fields.' .
                                    $field->column_name . '_max\')';
                            }
                            $rule_field .= '\';';


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
                        case "belongsToRelationship":
                            $rulesArray[] = '$this->validationRules[\'' .
                            $field->column_name . '_id\'] = \'required\';';
                            $messagesArray[] = '\'' . $field->column_name .
                                '_id.required\' => trans(\'{{modelNamePluralUpperCase}}::' .
                                '{{modelNamePluralLowerCase}}/admin_lang.fields.' .
                                $field->column_name . '_required\')';

                            break;
                            // case "belongsToManyRelationship":
                            //     break;
                        default:
                            if ($field->is_required) {
                                $rulesArray[] = '$this->validationRules[\'' .
                                $field->column_name . '\'] = \'required\';';
                                $messagesArray[] = '\'' . $field->column_name .
                                    '.required\' => trans(\'{{modelNamePluralUpperCase}}::' .
                                    '{{modelNamePluralLowerCase}}/admin_lang.fields.' .
                                    $field->column_name . '_required\')';
                            }
                    }
                }
            }
        }

        $fieldRules = implode("\n", $rulesArray);
        $fieldMessages = implode(",\n", $messagesArray);

        $requestPath = $this->crudGenerator->resourcePath . "Requests/Request.stub";
        $requestTemplate = str_replace(
            [
                '{{__langData__}}',
                '{{__fieldRules__}}',
                '{{__fieldMessages__}}',
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $langData,
                $fieldRules,
                $fieldMessages,
                $name,
                $module->modelLowerCaselPlural,
                $module->modelLowerCase,
                $module->modelPlural
            ],
            $this->crudGenerator->getStub($requestPath)
        );

        $requestsDirectory = $this->crudGenerator->destinyPath . DIRECTORY_SEPARATOR . $module->modelPlural .
            DIRECTORY_SEPARATOR . "Requests";

        if (!file_exists($requestsDirectory)) {
            mkdir($requestsDirectory, 0755, true);
        }

        file_put_contents(
            $requestsDirectory . DIRECTORY_SEPARATOR . "Admin" . $module->modelPlural . "Request.php",
            $requestTemplate
        );
    }
}
