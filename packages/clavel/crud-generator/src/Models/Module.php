<?php

namespace Clavel\CrudGenerator\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = "crud_modules";


    public function fields()
    {
        return $this->hasMany('Clavel\CrudGenerator\Models\ModuleField', 'crud_module_id', 'id');
    }

    public function getModelPluralAttribute()
    {
        if (!empty($this->model_plural)) {
            return $this->model_plural;
        }

        if (empty($this->model)) {
            return "";
        }

        return Str::plural($this->model);
    }

    public function getModelLowerCaseAttribute()
    {
        return strtolower($this->model);
    }

    public function getModelLowerCaselPluralAttribute()
    {
        return strtolower($this->getModelPluralAttribute());
    }

    public function getTableNameAttribute()
    {
        if (!empty($this->table_name)) {
            return $this->table_name;
        }

        return $this->getModelLowerCaselPluralAttribute();
    }
}
