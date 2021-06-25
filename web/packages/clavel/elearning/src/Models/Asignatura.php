<?php

namespace Clavel\Elearning\Models;

use Carbon\Carbon;
use Clavel\Elearning\Traits\RealTranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Asignatura extends Model
{
    use RealTranslatableTrait, \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;
    protected $table = 'asignaturas';

    public $translatedAttributes = [
        'titulo', 'url_amigable',
        'breve', 'descripcion', 'creditos',
        'academico',
        'caracteristica',
        'plazas', 'admision', 'coordinacion', 'estudiantes'
    ];

    public function scopeActive($query)
    {
        return $query->where('activo', 1);
    }

    public function convocatorias()
    {
        return $this->hasMany('Clavel\Elearning\Models\Convocatoria', 'asignatura_id', 'id');
    }

    public function getConvocatoriaPosibleAttribute()
    {
        $convocatoria = "";

        if ($this->convocatorias()->count() > 0) {
            // Miramos si tenemos una convocatoria en el futuro
            $convocatoria = $this->convocatorias()
                ->where("fecha_fin", ">=", Carbon::today())
                ->orderBy("fecha_fin")->first();
            if (empty($convocatoria)) {
                // No hay en el futuro, miramos en el pasado
                $convocatoria = $this->convocatorias()
                    ->where("fecha_inicio", "<", Carbon::today())
                    ->orderBy("fecha_inicio", 'DESC')
                    ->first();
            }
        }

        return $convocatoria;
    }

    public function cursoPivot()
    {
        return $this->belongsToMany('Clavel\Elearning\Models\Curso', 'asignatura_cursos')
            ->withPivot('curso_id');
    }

    public function codigo()
    {
        return $this->belongsToMany('Clavel\Elearning\Models\Codigo', 'codigo_asignaturas')
            ->withPivot('asignatura_id');
    }

    public function cursoSelected($curso_id)
    {
        if (isset($this->attributes["id"])) {
            return ($this->join("asignatura_cursos", "asignaturas.id", "=", "asignatura_cursos.asignatura_id")
                ->where("asignatura_cursos.curso_id", "=", $curso_id)
                ->where("asignatura_cursos.asignatura_id", "=", $this->attributes["id"])
                ->count() > 0) ? true : false;
        } else {
            return false;
        }
    }

    public function getConvocatoriaActiva($fecha = null)
    {
        if (empty($fecha)) {
            $fecha = Carbon::today();
        }

        // Localizamos la convocatoria localizada que esta en la fecha indicada
        $convocatoria = $this->convocatorias()
            ->where("fecha_inicio", "<=", $fecha)
            ->where("fecha_fin", ">=", $fecha)
            ->first();

        return $convocatoria;
    }


    public function getConvocatoriaViable($fecha = null)
    {
        if (empty($fecha)) {
            $fecha = Carbon::today();
        }

        // Buscamos la proxima convocatoria y sino la encuentro la anterior m?s proxima
        $convocatoria = null;
        if ($this->convocatorias()->count() > 0) {
            // Miramos si tenemos una convocatoria en el futuro
            $convocatoria = $this->convocatorias()
                ->where("fecha_fin", ">", $fecha)
                ->orderBy("fecha_fin")
                ->first();
            if (empty($convocatoria)) {
                // No hay en el futuro, miramos en el pasado
                $convocatoria = $this->convocatorias()
                    ->where("fecha_inicio", "<", $fecha)
                    ->orderBy("fecha_inicio", 'DESC')
                    ->first();
            }
        }

        return $convocatoria;
    }

    public function getActiva()
    {
        // Se comprueba que la asignatura tiene una convocatoria activa, es decir las fechas de inicio y fin estan
        // alrededor de la fecha actual
        $convocatoria = $this->convocatorias()
            ->where("fecha_inicio", "<=", Carbon::today())
            ->where("fecha_fin", ">=", Carbon::today())
            ->first();
        if (empty($convocatoria)) {
            // Si no la tiene activa, buscamos su convocatoria futura o pasada
            $convocatoria = $this->getConvocatoriaPosibleAttribute();
            if (empty($convocatoria)) {
                // En el caso que no devuelva nada, no hay convocatorias
                $mensaje = trans("elearning::asignaturas/front_lang.convocatoria_inactiva");
            } else {
                // Comprobamos si se va a abrir proximamente o si ya esta cerrada definitivamente
                $fecha_inicio = new Carbon($convocatoria->fecha_inicio);
                $fecha_fin = new Carbon($convocatoria->fecha_fin);
                if ($convocatoria->fecha_inicio >= Carbon::today()) {
                    $mensaje = trans("elearning::asignaturas/front_lang.convocatoria_inactiva_hasta") .
                        $fecha_inicio->format("d/m/Y");
                }
                if ($convocatoria->fecha_fin <= Carbon::today()) {
                    $mensaje = trans("elearning::asignaturas/front_lang.convocatoria_inactiva_desde") .
                        $fecha_fin->format("d/m/Y");
                }
            }

            return array("activa" => false, "mensaje" => $mensaje);
        }

        if ($convocatoria->gruposPivot()->count() > 0) {
            $esta_en_grupo = false;
            foreach ($convocatoria->gruposPivot()->get() as $grupo) {
                if ($grupo->userSelected(Auth::user()->id)) {
                    $esta_en_grupo = true;
                }
            }
            if (!$esta_en_grupo) {
                if (config("elearning.autentificacion.TIPO_REGISTRO") == 2 &&
                    $this->codigo()->active()->where("codigo_id", Auth::user()->codigo_id)->count() == 0
                ) {
                    return array(
                        "activa" => false,
                        "mensaje" => 'Esta convocatoria es especifica para un grupo, del que no forma parte.'
                    );
                }
            }
        }

        return array("activa" => true);
    }

    public function getStats()
    {
        $aprobados = 0;
        $suspendidos = 0;
        $pendientes = 0;
        $nota_media = 0;
        $total_realizado = 0;
        $total_usuarios = 0;
        $cuantos_media = 0;
        $tracking_asignatura = TrackAsignatura::where("asignatura_id", $this->attributes["id"])->get();
        foreach ($tracking_asignatura as $track) {
            $total_usuarios++;
            if ($track->completado) {
                if ($track->aprobado) {
                    $aprobados++;
                } else {
                    $suspendidos++;
                }
                $nota_media += $track->nota;
                $total_realizado++;
            } else {
                $pendientes++;
            }
        }

        if ($total_realizado > 0) {
            $nota_media = round($nota_media / $total_realizado, 2);
            $cuantos_media = TrackAsignatura::where("asignatura_id", $this->attributes["id"])
                ->where("completado", 1)
                ->where("nota", ">", $nota_media)
                ->count();
            $cuantos_media = ($cuantos_media * 100) / $total_realizado;
        } else {
            $nota_media = "-";
        }
        if ($total_usuarios < 1) {
            $total_usuarios = 1;
        }
        return array(
            "pendientes" => $pendientes,
            "aprobados" => $aprobados,
            "suspendidos" => $suspendidos,
            "nota_media" => $nota_media,
            "total_usuarios" => $total_usuarios,
            "superan_media" => $cuantos_media
        );
    }

    public function modulos()
    {
        return $this->hasMany('Clavel\Elearning\Models\Modulo', 'asignatura_id', 'id');
    }

    public function track()
    {
        return $this->hasMany('Clavel\Elearning\Models\TrackAsignatura', 'asignatura_id', 'id');
    }

    public function trackContenido()
    {
        return $this->hasMany('Clavel\Elearning\Models\TrackContenido', 'asignatura_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User')->withPivot('fecha_matricula');
    }

    public function getPercentContents($convocatoria_id)
    {
        $total = $this->modulos()
            ->join("contenidos", "modulos.id", "=", "contenidos.modulo_id")
            ->join("tipo_contenidos", "tipo_contenidos.id", "=", "contenidos.tipo_contenido_id")
            ->where("tipo_contenidos.slug", "<>", 'tema')
            ->count();
        $cursado = $this
            ->trackContenido()
            ->join("contenidos", "contenidos.id", "=", "track_contenido.contenido_id")
            ->join("tipo_contenidos", "tipo_contenidos.id", "=", "contenidos.tipo_contenido_id")
            ->where("tipo_contenidos.slug", "<>", 'tema')
            ->where("user_id", "=", Auth::user()->id)
            ->where("convocatoria_id", "=", $convocatoria_id)
            ->count();

        if ($total > 0 && $cursado > 0) {
            return round(($cursado * 100) / $total);
        }

        return 0;
    }

    public function getThisUserTrackAttribute()
    {
        return $this->track()->where("user_id", auth()->user()->id)->first();
    }

    public function profesorPivot()
    {
        return $this->belongsToMany('App\Models\User', 'asignatura_profesor')
            ->withPivot('asignatura_id');
    }

    public function profesorSelected($user_id)
    {
        if (isset($this->attributes["id"])) {
            return ($this->join("asignatura_profesor", "asignaturas.id", "=", "asignatura_profesor.asignatura_id")
                ->where("asignatura_profesor.user_id", "=", $user_id)
                ->where("asignatura_profesor.asignatura_id", "=", $this->attributes["id"])
                ->count() > 0) ? true : false;
        } else {
            return false;
        }
    }

    public function profesorAsignaturas($user_id)
    {
        return $this->join("asignatura_profesor", "asignaturas.id", "=", "asignatura_profesor.asignatura_id")
            ->where("asignatura_profesor.user_id", "=", $user_id)
            ->where("asignaturas.activo", "1");
    }
}
