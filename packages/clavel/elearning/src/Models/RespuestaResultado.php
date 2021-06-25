<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaResultado extends Model
{
    protected $table = 'respuesta_resultados';
    public $timestamps = false;

    public function respuesta()
    {
        $this->belongsTo('Clavel\Elearning\Models\Respuesta', 'respuesta_id', 'id');
    }
}
