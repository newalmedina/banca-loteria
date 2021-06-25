<?php

namespace Clavel\Elearning\Services;

use Clavel\Elearning\Models\Contenido;
use Clavel\Elearning\Models\RespuestaResultado;
use Clavel\Elearning\Models\TrackContenidoEvaluacion;

class ProfesorHelper
{
    private $user;
    private $scope;
    private $scope_pivot_id;
    private $scope_child;
    private $convocatoria_id;
    private $scopeClass;
    private $trackScopeClass;
    private $textScopeId;
    private $childClass;
    private $trackChildClass;
    private $specificFields = array();

    private function getHierarchicalTree()
    {
        switch ($this->scope) {
            case "asignatura":
                $this->scope_child = "modulo";
                $this->specificFields[] = "orden";
                $this->specificFields[] = "fecha_inicio";
                break;
            case "modulo":
                $this->scope_child = "contenido";
                $this->specificFields[] = "lft";
                $this->specificFields[] = "fecha_lectura";
                break;
        }
    }

    private function getClassNames()
    {
        $this->scopeClass = "\Clavel\Elearning\Models\\" . ucfirst($this->scope);
        $this->trackScopeClass = "\Clavel\Elearning\Models\Track" . ucfirst($this->scope);
        $this->textScopeId = $this->scope . "_id";

        if (!empty($this->scope_child)) {
            $this->childClass = "\Clavel\Elearning\Models\\" . ucfirst($this->scope_child);
            $this->trackChildClass = "\Clavel\Elearning\Models\Track" . ucfirst($this->scope_child);
        }
    }


    public function setInternalVariables($user, $scope, $scope_pivot_id, $convocatoria_id)
    {
        $this->user = $user;
        $this->scope = $scope;
        $this->scope_pivot_id = $scope_pivot_id;
        $this->convocatoria_id = $convocatoria_id;
        $this->getHierarchicalTree();
        $this->getClassNames();
    }

    public function getStatsPartial()
    {
        $trackScope = $this->trackScopeClass::with('convocatoria')
            ->where("user_id", $this->user->id)
            ->where($this->textScopeId, $this->scope_pivot_id)
            ->where("convocatoria_id", $this->convocatoria_id)->first();

        $compactVariables = array("trackScope" => $trackScope);

        /* Si es el scope es asignatura o modulo recogemos el track de los hijos */
        if (!empty($this->childClass)) {
            $compactVariables["totales"] = $this->childClass::where($this->textScopeId, $this->scope_pivot_id)
                ->activos()
                ->orderBy($this->specificFields[0])->get();

            $compactVariables["tracksChildren"] = $this->trackChildClass::with($this->scope_child)
                ->where($this->textScopeId, $this->scope_pivot_id)
                ->where("user_id", $this->user->id)->where("convocatoria_id", $this->convocatoria_id)
                ->orderBy($this->specificFields[1])->get();

            $methodName = "aditional_info_$this->scope";
            $compactVariables["infoAdicional"] = $this->$methodName(
                $compactVariables["totales"],
                $compactVariables["tracksChildren"]
            );
        } else {
            /* Si es un contenido simplemente buscamos la informacion adicional necesaria */
            $methodName = "aditional_info_$this->scope";
            $compactVariables["infoAdicional"] = $this->$methodName($trackScope);
        }

        return $compactVariables;
    }

    private function aditionalInfoAsignatura($totales, $tracksChildren)
    {
        $res = array(
            "totales" => $totales->count(),
            "que_puntua" => $this->childClass::activos()
                ->where($this->textScopeId, $this->scope_pivot_id)
                ->where("puntua", 1)->where("peso", ">", 0)
                ->count(),
            "totales_vistos" => $tracksChildren->count(),
            "que_puntua_vistos" => 0
        );

        foreach ($tracksChildren as $track) {
            if ($track->modulo->puntua && $track->modulo->peso > 0) {
                $res["que_puntua_vistos"]++;
            }
        }

        return $res;
    }

    private function aditionalInfoModulo($totales, $tracksChildren)
    {
        $res = array(
            "totales" => $totales->count(),
            "obligatorios" => $this->childClass::activos()
                ->where($this->textScopeId, $this->scope_pivot_id)
                ->where("obligatorio", 1)
                ->count(),
            "totales_vistos" => $tracksChildren->count(),
            "obligatorios_vistos" => 0
        );

        foreach ($tracksChildren as $track) {
            if ($track->contenido->obligatorio) {
                $res["obligatorios_vistos"]++;
            }
        }

        return $res;
    }

    private function aditionalInfoContenido($trackScope)
    {
        $res = array(
            "trackEval" => $trackScope->contenido
                ->trackEvaluacion()
                ->validados()
                ->where("user_id", $this->user->id)
                ->where("convocatoria_id", $this->convocatoria_id)
                ->orderBy("fecha_intento", "DESC")
                ->first()
        );

        return $res;
    }

    public function recalcularNota(TrackContenidoEvaluacion $track_eval)
    {
        $contenido = Contenido::findOrFail($track_eval->contenido_id);
        $resultados = RespuestaResultado::where("contenido_id", "=", $track_eval->contenido_id)
            ->where("user_id", "=", $track_eval->user_id)
            ->where("convocatoria_id", "=", $track_eval->convocatoria_id);

        $total = clone $resultados;
        $total = $total->where("correcta", 1)->sum("puntos_correcta");
        $obtenido = $resultados->sum("puntos_obtenidos");

        if ($total <= 0) {
            $total = 1;
        }

        if ($obtenido < 0) {
            $obtenido = 0;
        } elseif ($obtenido > $total) {
            $obtenido = $total;
        }

        $track_eval->puntuacion_obtenida = $obtenido;
        $track_eval->puntuacion_maxima = $total;
        $track_eval->nota = ($obtenido * 10) / $total;

        $aprobado = false;
        $porcentaje_aprobado = ($track_eval->nota) * 10;
        if ($porcentaje_aprobado >= $contenido->evaluacion->porcentaje_aprobado) {
            $aprobado = true;
        }

        $track_eval->aprobado = $aprobado;

        return ($track_eval->save()) ? $track_eval : null;
    }
}
