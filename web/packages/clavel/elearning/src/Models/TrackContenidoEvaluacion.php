<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class TrackContenidoEvaluacion extends Model
{
    protected $table = 'track_contenido_evaluacion';
    public $timestamps = false;

    public function scopeValidados($query)
    {
        return $query->where("validado", "=", 1);
    }
}
