<?php

namespace Clavel\Elearning\Models;

use Kalnoy\Nestedset\NodeTrait;
use Clavel\Basic\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Clavel\Elearning\Traits\RealTranslatableTrait;

class Contenido extends Model
{
    use RealTranslatableTrait, \Astrotomic\Translatable\Translatable;
    use NodeTrait;

    public $useTranslationFallback = true;
    protected $table = 'contenidos';

    public $translatedAttributes =
    [
        'nombre',
        'url_amigable',
        'contenido',
        'contenido_aprobado',
        'contenido_suspendido',
        'mp4',
        'webm',
        'vtt'
    ];
    public $tracking = null;

    public function getLftName()
    {
        return 'lft';
    }

    public function getRgtName()
    {
        return 'rgt';
    }

    public function scopeActivos($query)
    {
        return $query->where("activo", 1);
    }

    public function scopeObligatorios($query)
    {
        return $query->where("obligatorio", 1);
    }

    public function modulo()
    {
        return $this->belongsTo('Clavel\Elearning\Models\Modulo', 'modulo_id', 'id');
    }

    public function tipo()
    {
        return $this->hasOne('Clavel\Elearning\Models\TipoContenido', 'id', 'tipo_contenido_id');
    }

    public function evaluacion()
    {
        return $this->hasOne('Clavel\Elearning\Models\ContenidoEvaluacion', 'contenido_id', 'id');
    }

    public function preguntas()
    {
        return $this->hasMany('Clavel\Elearning\Models\Pregunta', 'contenido_id', 'id');
    }

    public function trackVideo()
    {
        return $this->hasOne('Clavel\Elearning\Models\TrackVideo', 'contenido_id', 'id');
    }

    public function getMedia()
    {
        return Media::where("path", "=", $this->storepath)->where("mime", "like", "image%")->orderBy("id", "asc");
    }

    public function trackEvaluacion()
    {
        return $this->hasMany('Clavel\Elearning\Models\TrackContenidoEvaluacion');
    }

    public function trackEvalByUserConvocatoria($user_id, $convocatoria_id)
    {
        return $this->trackEvaluacion()->where("user_id", $user_id)
            ->where("convocatoria_id", $convocatoria_id)
            ->orderBy("fecha_intento", "desc");
    }

    /**
     * Metodo que devuelve las estadisticas de acceso al contenido
     *
     * Las estadisticas dependen del tipo de contenido, es decir, si es Evaluacion o el resto
     *
     * @return array
     */
    public function getStats()
    {
        // Si no es evaluaci�n
        if (empty($this->evaluacion)) {
            $tracking = TrackContenido::where("contenido_id", $this->attributes["id"])->get();
            return $this->displayStats($tracking);
        } else {
            // Es evaluacion
            $tracking = [
                "contenido" => TrackContenido::where("contenido_id", $this->attributes["id"])->get(),
                "evaluacion" => TrackContenidoEvaluacion::where("contenido_id", $this->attributes["id"])->get()
            ];
            return $this->displayStatsEvaluacion($tracking);
        }
    }

    /**
     * Como es contenido, revisamos quien ha completado el contenido solamente
     *
     * @param $tracking
     * @return array
     */
    private function displayStats($tracking)
    {
        $aprobados = 0;
        $suspendidos = 0;
        $pendientes = 0;
        $total_usuarios = 0;
        $nota_media = 0;
        $cuantos_media = 0;

        foreach ($tracking as $track) {
            $total_usuarios++;
            if (!$track->completado) {
                $pendientes++;
            }
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

    /**
     * Como es contenido tipo examen, revisamos quien ha completado el contenido,
     * quien ha aprobado y quien ha suspendido
     *
     * @param $tracking
     * @return array
     */
    private function displayStatsEvaluacion($tracking)
    {
        $aprobados = 0;
        $suspendidos = 0;
        $pendientes = 0;
        $total_usuarios = 0;
        $nota_media = 0;
        $cuantos_media = 0;
        $total_realizado = 0;

        // Revisamos los contenidos de tipo evaluaci�n
        for ($i = 0; $i < $tracking["evaluacion"]->count(); $i++) {
            $trackEvaluacion = $tracking["evaluacion"][$i];
            if ($trackEvaluacion->aprobado) {
                $aprobados++;
            } else {
                $suspendidos++;
            }
            $nota_media += $trackEvaluacion->nota;
            $total_realizado++;
        }
        // Calculamos la nota media
        if ($total_realizado > 0) {
            $nota_media = round($nota_media / $total_realizado, 2);
            $cuantos_media = TrackContenidoEvaluacion::where("contenido_id", $this->attributes["id"])
                ->where("nota", ">", $nota_media)
                ->count();
            $cuantos_media = ($cuantos_media * 100) / $total_realizado;
        } else {
            $nota_media = "-";
        }

        // Revisamos los contenidos generales
        for ($i = 0; $i < $tracking["contenido"]->count(); $i++) {
            $total_usuarios++;
            $trackContenido = $tracking["contenido"][$i];
            if (!$trackContenido->completado) {
                $pendientes++;
            }
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
}
