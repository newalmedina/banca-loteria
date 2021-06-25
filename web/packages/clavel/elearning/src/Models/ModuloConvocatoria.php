<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class ModuloConvocatoria extends Model
{
    protected $table = "modulo_convocatorias";
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = false;

    public function getFechaInicioFormattedAttribute()
    {
        if ($this->fecha_inicio!=null && $this->fecha_inicio != '') {
            $fecha_inicio = new \Carbon\Carbon($this->fecha_inicio);
            return $fecha_inicio->format('d/m/Y');
        }

        return "";
    }

    public function getFechaFinFormattedAttribute()
    {
        if ($this->fecha_fin!=null && $this->fecha_fin != '') {
            $fecha_fin = new \Carbon\Carbon($this->fecha_fin);
            return $fecha_fin->format('d/m/Y');
        }

        return "";
    }
}
