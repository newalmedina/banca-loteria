<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class TrackAsignatura extends Model
{
    protected $table = 'track_asignatura';
    public $timestamps = false;

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function convocatoria()
    {
        return $this->hasOne('Clavel\Elearning\Models\Convocatoria', 'id', 'convocatoria_id');
    }

    public function asignatura()
    {
        return $this->hasOne('Clavel\Elearning\Models\Asignatura', 'id', 'asignatura_id');
    }

    public function getFechaInicioFormattedAttribute($time = false)
    {
        if ($this->fecha_inicio!=null && $this->fecha_inicio != '') {
            $fecha_inicio = new \Carbon\Carbon($this->fecha_inicio);
            return $fecha_inicio->format('d/m/Y H:i:s');
        }

        return "";
    }

    public function getFechaInicioNormalFormattedAttribute($time = false)
    {
        if ($this->fecha_inicio!=null && $this->fecha_inicio != '') {
            $fecha_inicio = new \Carbon\Carbon($this->fecha_inicio);
            return $fecha_inicio->format('d/m/Y');
        }

        return "";
    }

    public function getFechaFinFormattedAttribute($time = false)
    {
        if ($this->fecha_fin!=null && $this->fecha_fin != '') {
            $fecha_fin = new \Carbon\Carbon($this->fecha_fin);
            return $fecha_fin->format('d/m/Y H:i:s');
        }

        return "";
    }

    public function getFechaFinNormalFormattedAttribute($time = false)
    {
        if ($this->fecha_fin!=null && $this->fecha_fin != '') {
            $fecha_fin = new \Carbon\Carbon($this->fecha_fin);
            return $fecha_fin->format('d/m/Y');
        }

        return "";
    }

    public function scopeAprobadas($query)
    {
        return $query->where("completado", 1)->where("aprobado", 1);
    }

    public function scopeSuspendidas($query)
    {
        return $query->where("completado", 1)->where("aprobado", 0);
    }

    public function scopePendientes($query)
    {
        return $query->where("completado", 0);
    }
}
