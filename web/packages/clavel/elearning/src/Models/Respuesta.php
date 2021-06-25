<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use \Astrotomic\Translatable\Translatable;

    protected $table = 'respuestas';

    public $useTranslationFallback = true;
    public $timestamps = true;

    public $translatedAttributes = ['nombre','comentario'];

    public function pregunta()
    {
        return $this->belongsTo('Clavel\Elearning\Models\Pregunta', 'pregunta_id', 'id');
    }

    public function resultado()
    {
        return $this->hasMany('Clavel\Elearning\Models\RespuestaResultado', 'respuesta_id', 'id');
    }

    public function scopeOrdered($query, $aleatorio)
    {
        if ($aleatorio) {
            return $query->orderByRaw("RAND()");
        }

        return $query;
    }

    public function scopeActivas($query)
    {
        return $query->where("activa", "=", 1);
    }
}
