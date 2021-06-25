<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Pregunta extends Model
{
    use \Astrotomic\Translatable\Translatable;

    protected $table = 'preguntas';

    public $useTranslationFallback = true;
    public $timestamps = true;

    public $translatedAttributes = ['nombre'];

    public function contenido()
    {
        return $this->belongsTo('Clavel\Elearning\Models\Contenido', 'contenido_id', 'id');
    }

    public function tipo()
    {
        return $this->belongsTo('Clavel\Elearning\Models\TipoPregunta', 'tipo_pregunta_id', 'id');
    }

    public function respuestas()
    {
        return $this->hasMany('Clavel\Elearning\Models\Respuesta', 'pregunta_id', 'id');
    }

    public function resultado()
    {
        return $this->hasMany('Clavel\Elearning\Models\RespuestaResultado', 'pregunta_id', 'id');
    }
    public function scopeOrdered($query, $aleatorio)
    {
        if ($aleatorio) {
            return $query->orderByRaw("RAND()");
        } else {
            return $query->orderBy("orden", "asc");
        }
    }

    public function scopeMias($query, $evaluacion)
    {
        if (!empty($evaluacion)) {
            $arrayResultado = RespuestaResultado::select("respuesta_resultados.pregunta_id")
                ->distinct()
                ->where("respuesta_resultados.contenido_id", "=", $evaluacion->contenido_id)
                ->where("respuesta_resultados.user_id", "=", Auth::user()->id)
                ->get()
                ->toArray();
            $query->whereIn('id', $arrayResultado);
        }

        return $query;
    }

    public function scopeLimited($query, $numero, $evaluacion)
    {
        if ($numero <= 0 || empty($numero)) {
            return $query;
        }

        if ($numero > 0 && (empty($evaluacion) || $evaluacion->validado == false)) {
            return $query->limit($numero);
        }
        return $query->select(array("preguntas.id", "preguntas.tipo_pregunta_id", "preguntas.obligatoria"))
            ->join("respuesta_resultados as rr", "rr.pregunta_id", "preguntas.id")
            ->where("rr.user_id", Auth::user()->id)->groupBy("preguntas.id");
    }

    public function scopeActivas($query)
    {
        return $query->where("activa", "=", 1);
    }

    public function scopeObligatorias($query)
    {
        return $query->where("obligatoria", "=", 1);
    }

    public function scopeQuitarObligatorias($query, $preguntas_obligatorias)
    {
        foreach ($preguntas_obligatorias as $preg) {
            $query->where("id", "!=", $preg->id);
        }
        return $query;
    }
}
