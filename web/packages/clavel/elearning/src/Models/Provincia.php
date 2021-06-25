<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincias';
    public $timestamps = false;

    public function municipios()
    {
        return $this->hasMany('Clavel\Elearning\Models\Municipio');
    }
}
