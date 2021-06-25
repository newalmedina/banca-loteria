<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class TrackContenido extends Model
{
    protected $table = 'track_contenido';
    public $timestamps = false;

    public function scopeObligatorios($query)
    {
        return $query->where("obligatorio", 1);
    }

    public function scopeCompletados($query)
    {
        return $query->where("completado", 1);
    }

    public function contenido()
    {
        return $this->hasOne('Clavel\Elearning\Models\Contenido', 'id', 'contenido_id');
    }

    public function convocatoria()
    {
        return $this->hasOne('Clavel\Elearning\Models\Convocatoria', 'id', 'convocatoria_id');
    }

    public function getFechaLecturaFormattedAttribute()
    {
        if ($this->fecha_lectura!=null && $this->fecha_lectura!= '') {
            $fecha_lectura = new \Carbon\Carbon($this->fecha_lectura);
            return $fecha_lectura->format('d/m/Y H:i:s');
        }

        return "";
    }
}
