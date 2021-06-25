<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPregunta extends Model
{
    protected $table = 'tipo_preguntas';
    public $timestamps = false;

    public function scopeActivas($query)
    {
        return $query->where("activa", "=", 1);
    }
}
