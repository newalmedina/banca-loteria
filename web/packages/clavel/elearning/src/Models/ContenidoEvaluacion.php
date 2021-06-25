<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class ContenidoEvaluacion extends Model
{
    protected $table = "contenidos_evaluacion";
    protected $fillable = [];
    protected $guarded = [];


    public function contenido()
    {
        return $this->belongsTo('Clavel\Elearning\Models\Contenido', 'contenido_id', 'id');
    }

    public function track()
    {
        return $this->hasMany('Clavel\Elearning\Models\TrackContenidoEvaluacion', 'contenido_id', 'contenido_id');
    }
}
