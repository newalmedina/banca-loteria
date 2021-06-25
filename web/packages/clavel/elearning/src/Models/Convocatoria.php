<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;

class Convocatoria extends Model
{
    protected $table = "asignatura_convocatorias";
    protected $fillable = [];
    protected $guarded = [];

    public function getFechaInicioFormattedAttribute()
    {
        if ($this->fecha_inicio != null && $this->fecha_inicio != '') {
            $fecha_inicio = new \Carbon\Carbon($this->fecha_inicio);
            return $fecha_inicio->format('d/m/Y');
        }

        return "";
    }

    public function getFechaFinFormattedAttribute()
    {
        if ($this->fecha_fin != null && $this->fecha_fin != '') {
            $fecha_fin = new \Carbon\Carbon($this->fecha_fin);
            return $fecha_fin->format('d/m/Y');
        }

        return "";
    }

    public function gruposPivot()
    {
        return $this->belongsToMany('Clavel\Elearning\Models\Grupo', 'asignatura_convocatoria_grupos')
            ->withPivot('grupo_id');
    }

    public function grupoSelected($grupo_id)
    {
        if (isset($this->attributes["id"])) {
            return (self::join(
                "asignatura_convocatoria_grupos",
                "asignatura_convocatorias.id",
                "=",
                "asignatura_convocatoria_grupos.convocatoria_id"
            )
                ->where("asignatura_convocatoria_grupos.grupo_id", "=", $grupo_id)
                ->where("asignatura_convocatoria_grupos.convocatoria_id", "=", $this->attributes["id"])
                ->count() > 0) ? true : false;
        } else {
            return false;
        }
    }

    public function certificado()
    {
        return $this->hasOne('Clavel\Elearning\Models\Certificado', 'id', 'certificado_id');
    }

    public function moduloConvocatoria()
    {
        return $this->hasMany('Clavel\Elearning\Models\ModuloConvocatoria', 'convocatoria_id', 'id');
    }

    public function getRagoModulo($modulo_id)
    {
        return $this->moduloConvocatoria()->where("modulo_id", $modulo_id)->first();
    }
}
