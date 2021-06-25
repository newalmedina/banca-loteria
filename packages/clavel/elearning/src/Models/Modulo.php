<?php

namespace Clavel\Elearning\Models;

use Carbon\Carbon;
use Clavel\Elearning\Traits\RealTranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Modulo extends Model
{
    use RealTranslatableTrait, \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;
    protected $table = 'modulos';

    public $translatedAttributes = ['nombre', 'url_amigable', 'descripcion', 'coordinacion'];

    public function scopeActivos($query)
    {
        return $query->where("activo", 1);
    }

    public function asignatura()
    {
        return $this->belongsTo('Clavel\Elearning\Models\Asignatura', 'asignatura_id', 'id');
    }

    public function contenidos()
    {
        return $this->hasMany('Clavel\Elearning\Models\Contenido', 'modulo_id', 'id');
    }

    public function convocatoria()
    {
        return $this->hasMany('Clavel\Elearning\Models\ModuloConvocatoria', 'modulo_id', 'id');
    }

    public function getConvocatoriaPosibleAttribute()
    {
        $convocatoria = "";


        if (self::convocatoria()->count() > 0) {
            $convocatoria = self::convocatoria()
                ->where("fecha_fin", ">=", Carbon::today())
                ->orderBy("fecha_fin")->first();
            if (empty($convocatoria) || $convocatoria == null) {
                $convocatoria = self::convocatoria()
                    ->where("fecha_inicio", "<", Carbon::today())
                    ->orderBy("fecha_inicio", 'DESC')
                    ->first();
            }
        }

        // Si el módulo no tiene convocatoria pasamos a la convocatoria de la asignatura
        if ($convocatoria == '') {
            $convocatoria = self::asignatura()->first()->convocatoria_posible;
        }

        return $convocatoria;
    }

    public function evaluaciones()
    {
        return $this->hasMany('Clavel\Elearning\Models\ContenidoEvaluacion', 'modulo_id', 'id');
    }

    public function resultados()
    {
        return $this->hasMany('Clavel\Elearning\Models\TrackModulo', 'modulo_id', 'id');
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
        $tracking_asignatura = TrackModulo::where("modulo_id", $this->attributes["id"])->get();
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
            $cuantos_media = TrackModulo::where("modulo_id", $this->attributes["id"])
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

    public function getComplete()
    {
        return $this->getCompleteUser(Auth::user()->id);
    }

    public function getCompleteUser($userId)
    {
        // Compruebo los completados
        $raw = DB::select('select distinct c2.id, (COUNT(c1.id)-SUM(coalesce(tc.completado,0))) as restantes
                          from ' . env('DB_PREFIX') . 'contenidos c1 inner join ' . env('DB_PREFIX') .
            'contenidos c2 on c1.lft BETWEEN c2.lft and c2.rgt and c2.modulo_id = ' . $this->id . '
                          left join ' . env('DB_PREFIX') .
            'track_contenido tc on c1.id = tc.contenido_id and tc.user_id=' . $userId . '
                          where c2.tipo_contenido_id<>1
                          GROUP BY c2.id');

        $completados = [];

        foreach ($raw as $contenido_completado) {
            if ($contenido_completado->restantes == 0) {
                $completados[$contenido_completado->id] = 1;
            }
        }

        return $completados;
    }
}
