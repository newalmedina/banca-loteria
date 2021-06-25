<?php

namespace Clavel\CrudGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class FieldType extends Model
{
    protected $table = "crud_field_types";

    public function scopeActives($query)
    {
        return $query->where("active", true);
    }
}
