<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = "municipios";
    public $timestamps = false;

    public function provincia()
    {
        return $this->belongsTo('Clavel\Elearning\Models\Provincia');
    }
}
