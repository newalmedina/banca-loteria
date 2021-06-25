<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;
    protected $table = 'cursos';

    public $translatedAttributes = ['nombre', 'url_amigable'];

    public function certificado()
    {
        return $this->belongsTo('Clavel\Elearning\Models\Certificado');
    }

    public function scopeActive($query)
    {
        return $query->where('activo', 1);
    }

    public function asignaturaPivot()
    {
        return $this->belongsToMany('Clavel\Elearning\Models\Asignatura', 'asignatura_cursos')
            ->withPivot('asignatura_id');
    }

    public function asignaturas()
    {
        return $this->belongsToMany('Clavel\Elearning\Models\Asignatura', 'asignatura_cursos');
    }

    public function asignaturaSelected($asignatura_id)
    {
        if (isset($this->attributes["id"])) {
            return (self::join("asignatura_cursos", "cursos.id", "=", "asignatura_cursos.curso_id")
                    ->where("asignatura_cursos.asignatura_id", "=", $asignatura_id)
                    ->where("asignatura_cursos.curso_id", "=", $this->attributes["id"])
                    ->count() > 0) ? true : false;
        } else {
            return false;
        }
    }

    public function checkCertificado()
    {
        $res = false;
        if (!empty($this->certificado_id)) {
            // Si tiene un certificado asignado, comprobamos que el usuario a aprobado a todas las asignaturas del curso
            $tracks = DB::table("asignaturas AS a")
                ->join("asignatura_cursos AS ac", "a.id", "ac.asignatura_id")
                ->leftJoin("track_asignatura as ta", function ($join) {
                    $join->on("ta.asignatura_id", "ac.asignatura_id")
                        ->where("ta.user_id", auth()->user()->id);
                })->select("ac.asignatura_id", "ta.aprobado", "ta.fecha_inicio")
                ->where("ac.curso_id", $this->id)->where("a.activo", 1)->orderBy("ta.fecha_inicio")
                ->groupBy("ac.asignatura_id", "ta.aprobado", "ta.fecha_inicio")->get();

            // Si el curso tiene asignaturas activas comprobamos que el usuario haya aprobado en todas
            if ($tracks->count() > 0) {
                $res = true;
                foreach ($tracks as $track) {
                    if (empty($track->aprobado)) {
                        return false;
                    }
                }
            }
        }
        return $res;
    }
}
