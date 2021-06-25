<?php

namespace Clavel\Elearning\Services;

use Clavel\Elearning\Models\Asignatura;
use Clavel\Elearning\Models\Contenido;
use Clavel\Elearning\Models\Convocatoria;
use Clavel\Elearning\Models\Modulo;
use Clavel\Elearning\Models\RespuestaResultado;
use Clavel\Elearning\Models\TipoContenido;
use Clavel\Elearning\Models\TrackAsignatura;
use Clavel\Elearning\Models\TrackContenido;
use Clavel\Elearning\Models\TrackContenidoEvaluacion;
use Clavel\Elearning\Models\TrackModulo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TrackContent
{
    public $convocatoria_id = "";
    public $premium = false;

    /**
     * TrackContent constructor.
     * @param $contenido_id
     * @param $modulo_id
     * @param $asignatura_id
     * @param bool $premium Indica si se puede acceder independientemente de que exista una convocatoria activa.
     */
    public function __construct($contenido_id, $modulo_id, $asignatura_id, $premium = false)
    {
        if ($contenido_id != "") {
            $contenido = Contenido::find($contenido_id);
            if (empty($contenido)) {
                abort(403, "No se ha encontrado el contenido que busca");
            }
            $modulo_id = $contenido->modulo->id;
        }
        if ($modulo_id != "") {
            $modulo = Modulo::activos()->find($modulo_id);
            if (empty($modulo)) {
                abort(403, "No se ha encontrado el módulo que busca");
            }
            $asignatura_id = $modulo->asignatura->id;
        }
        $asignatura = Asignatura::active()->find($asignatura_id);
        if (empty($asignatura)) {
            abort(403, "No se ha encontrado la asignatura que busca");
        }


        $this->premium = $premium;
        $this->asignaturaActiva($asignatura);
    }

    public function asignaturaActiva(Asignatura $asignatura)
    {
        $this->convocatoria_id = "";

        // Buscamos la convocatoria activa en la fecha actual
        $convocatoria = $asignatura->getConvocatoriaActiva();
        if (!empty($convocatoria)) {
            $this->convocatoria_id = $convocatoria->id;
            return true;
        }

        // En este punto puede ocurrir que el usuario tenga privilegios para acceder independientemente de si la
        // convocatoria esta activa o no
        if ($this->premium) {
            $convocatoria = $asignatura->getConvocatoriaViable();
            if (!empty($convocatoria)) {
                $this->convocatoria_id = $convocatoria->id;
                return true;
            }
        }
        return false;
    }

    public function getConvocatoriaActiva(Modulo $modulo)
    {
        $today = Carbon::today()->format('Y-m-d');
        foreach ($modulo->asignatura->convocatorias as $convocatoria) {
            //Debemos comprobar que estamos en la convocatoria válida de la asignatura
            if ($convocatoria->fecha_inicio <= $today && $convocatoria->fecha_fin >= $today) {
                //Esta es la convocatoria activa
                //Comprobamos que esta convocatoria no tenga su homologo en el módulo
                if (!empty($convocatoria->getRagoModulo($modulo->id))) {
                    //Si tiene homologo debemos comprobar que estemos en fechas validas
                    if ($convocatoria->getRagoModulo($modulo->id)->fecha_inicio <= $today
                        && $convocatoria->getRagoModulo($modulo->id)->fecha_fin >= $today
                    ) {
                        return $convocatoria;
                    }
                } else {
                    // No tiene convocatoria el módulo por lo tanto devolvemos true porque
                    // la convocatoria de la assignatura está activa
                    return $convocatoria;
                }
            }
        }
        return null;
    }

    public function getInformacionConvocatoria($convocatoria_id, $modulo_id = "")
    {
        $convocatoria = Convocatoria::findorFail($convocatoria_id);
        if (empty($convocatoria)) {
            abort(403, "La convocatoria de este contenido no está activa");
        }

        if ($modulo_id != "") {
            if (!empty($convocatoria->getRagoModulo($modulo_id))) {
                $convocatoria->fecha_inicio = $convocatoria->getRagoModulo($modulo_id)->fecha_inicio;
                $convocatoria->fecha_fin = $convocatoria->getRagoModulo($modulo_id)->fecha_fin;
                $convocatoria->consultar = $convocatoria->getRagoModulo($modulo_id)->consultar;
                $convocatoria->porcentaje = $convocatoria->getRagoModulo($modulo_id)->porcentaje;
            }
        }
        return $convocatoria;
    }

    public function moduloActivo(Modulo $modulo, $exception = true)
    {
        if (Auth::user()->can('frontend-asignaturas-convocatoria-premium')) {
            return true;
        }
        //Una vez tenemos cargado el módulo debemos comprobar que por fecha de convocatoria lo podamos activar
        $ret_fecha = $this->moduloActivoFecha($modulo, $exception);

        //Comprobamos si tiene algún módulo que completar antes de empezar este.
        $ret_requerido = $this->moduloRequerido($modulo, $exception);

        if ($ret_fecha === true && $ret_requerido === true) {
            return true;
        }

        return false;
    }

    public function moduloActivoFecha(Modulo $modulo, $exception)
    {
        //Buscamos la convocatoria activa
        $convocatoria = $this->getConvocatoriaActiva($modulo);
        if (empty($convocatoria)) {
            // Tenemos que mirar si no hay ninguna convocatoria activa, si este usuario ha
            // realizado algo en alguna convocatoria
            $track_modulo = TrackModulo::where("modulo_id", "=", $modulo->id)
                ->where("user_id", "=", Auth::user()->id)->orderBy("fecha_inicio", "desc")->first();
            if (empty($track_modulo)) {
                if ($exception) {
                    abort(403, "La convocatoria de este contenido no está activa");
                } else {
                    return false;
                }
            } else {
                //Buscamos los datos de esta convocatoria
                $convocatoria = $this->getInformacionConvocatoria($track_modulo->convocatoria_id, $modulo->id);
                if (!$convocatoria->consultar || !$track_modulo->completado) {
                    if ($exception) {
                        abort(403, "La convocatoria de este contenido no está activa");
                    } else {
                        return false;
                    }
                }
            }
        }
        $this->convocatoria_id = $convocatoria->id;
        return true;
    }


    public function getRequerido($modulo)
    {
        if ($modulo->obligatorio_id != null) {
            //Comprobamos que el módulo esta completado.
            $modulo_original = Modulo::findorFail($modulo->obligatorio_id);
            return $modulo_original->nombre;
        }
        return "";
    }

    public function moduloRequerido(Modulo $modulo, $exception)
    {
        if ($modulo->obligatorio_id != null) {
            //Comprobamos que el módulo esta completado.
            $modulo_original = Modulo::findorFail($modulo->obligatorio_id);
            if (!$this->moduloCompleto($modulo_original)) {
                if ($exception) {
                    abort(403, "Necesita completar el módulo: " . $modulo_original->nombre);
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    public function moduloIniciado(Modulo $modulo)
    {
        $track = TrackModulo::where("modulo_id", "=", $modulo->id)
            ->where("user_id", "=", Auth::user()->id)
            ->where("convocatoria_id", "=", $this->convocatoria_id)
            ->first();
        if (empty($track)) {
            return false;
        }
        return true;
    }

    public function moduloCompleto(Modulo $modulo)
    {
        $track = TrackModulo::where("modulo_id", "=", $modulo->id)
            ->where("user_id", "=", Auth::user()->id)
            ->where("convocatoria_id", "=", $this->convocatoria_id)
            ->first();
        if (empty($track)) {
            return false;
        }

        return $track->completado;
    }

    public function trackingContenido($contenido_id, $estado)
    {
        /*
         * Que debemos hacer en el caso del track de contenido?
         * 1. Si no tenemos nada marcado debemos marcar como iniciada la asignatura y el módulo al que pertenecen
         * iniciarAsignatura - iniciarModulo (Iniciado)
         * 2. Si está iniciado debemos marcar este contenido como visitado
         * (si fuera tipo evaluación debemos mirar que $estado sea = 2)
         * tackContenido
         * 3. Una vez marcado debemos mirar que se hayan visualizado todos los contenidos y si es
         * asi marcar el modulo como completado
         * completarModulo - completarAsignatura (Completado)
         */
        $contenido = Contenido::activos()->findorFail($contenido_id);
        if (empty($contenido)) {
            abort(403, "No se ha encontrado el contenido que busca");
        }

        $this->moduloActivo($contenido->modulo);

        $this->iniciarAsignatura($contenido->modulo->asignatura->id);
        $this->iniciarModulo($contenido->modulo->id, $contenido->modulo->asignatura->id);

        // Marcamos el contenido (Lo hacemos aqui porque estas funciones se deben ejecutar siempre cuando se
        //  marca un contenido.
        $track = TrackContenido::where("contenido_id", "=", $contenido->id)
            ->where("user_id", "=", Auth::user()->id)
            ->where("convocatoria_id", "=", $this->convocatoria_id)
            ->first();
        if (empty($track)) {
            $track = new TrackContenido();
            $track->contenido_id = $contenido->id;
            $track->asignatura_id = $contenido->modulo->asignatura->id;
            $track->convocatoria_id = $this->convocatoria_id;
            $track->modulo_id = $contenido->modulo->id;
            $track->user_id = Auth::user()->id;
            $track->fecha_lectura = Carbon::now();
            $track->validado = true;
            $track->obligatorio = $contenido->obligatorio;
            $track->completado = true;

            if ($contenido->tipo->slug == 'eval' && $estado == 1 && !$contenido->evaluacion->presencial) {
                $track->completado = false;
            }
            $track->save();
        } else {
            if ($contenido->tipo->slug == 'eval' && $estado == 2) {
                $track->fecha_lectura = Carbon::now();
                $track->completado = true;
            }
            $track->obligatorio = $contenido->obligatorio;
            $track->save();
        }

        $this->completarModulo($contenido->modulo->id);
        $this->completarAsignatura($contenido->modulo->asignatura->id);
    }

    public function iniciarAsignatura($asignatura_id)
    {
        //Leemos la entrada del tracking de la asignatura
        $track = TrackAsignatura::where("asignatura_id", "=", $asignatura_id)
            ->where("user_id", "=", Auth::user()->id)
            ->where("convocatoria_id", "=", $this->convocatoria_id)
            ->first();
        if (empty($track)) {
            $track = new TrackAsignatura();
            $track->asignatura_id = $asignatura_id;
            $track->convocatoria_id = $this->convocatoria_id;
            $track->user_id = Auth::user()->id;
            $track->fecha_inicio = Carbon::now();
            $track->aprobado = false;
            $track->completado = false;

            $track->save();
        }
    }

    public function iniciarModulo($modulo_id, $asignatura_id)
    {
        //Leemos la entrada del tracking de la asignatura
        $track = TrackModulo::where("modulo_id", "=", $modulo_id)
            ->where("user_id", "=", Auth::user()->id)
            ->where("convocatoria_id", "=", $this->convocatoria_id)
            ->first();
        if (empty($track)) {
            $track = new TrackModulo();
            $track->modulo_id = $modulo_id;
            $track->asignatura_id = $asignatura_id;
            $track->convocatoria_id = $this->convocatoria_id;
            $track->user_id = Auth::user()->id;
            $track->fecha_inicio = Carbon::now();
            $track->aprobado = false;
            $track->completado = false;

            $track->save();
        }
    }

    public function completarAsignatura($asignatura_id)
    {
        $asignatura_acabada = true;
        $track = TrackAsignatura::where("asignatura_id", "=", $asignatura_id)
            ->where("user_id", "=", Auth::user()->id)
            ->where("convocatoria_id", "=", $this->convocatoria_id)->first();
        if (empty($track)) {
            abort(404);
        }
        if (!$track->completado || !$track->aprobado) {
            //Comprobamos que todos los modulos de esta asignatura esten completados y calculamos la nota del curso
            $asignatura = Asignatura::findorFail($asignatura_id);
            if (empty($asignatura)) {
                abort(404);
            }

            foreach ($asignatura->modulos()->activos()->get() as $modulo) {
                if (!$this->moduloCompleto($modulo)) {
                    $asignatura_acabada = false;
                }
            }


            if ($asignatura_acabada) {
                //Marcamos el módulo como completado
                $track->fecha_fin = Carbon::now();
                $asignatura_aprobada = $this->calcularAsignatura($asignatura->id, $this->convocatoria_id);
                $track->aprobado = $asignatura_aprobada[0];
                $track->nota = $asignatura_aprobada[1];
                $track->completado = true;
                $track->save();
            }
        }
    }

    public function completarModulo($modulo_id)
    {
        /*
         * Como sabemos si esta el módulo terminado?
         * 1. Deberemos leer todos los contenidos activos que tiene este módulo
         * 2. Deberemos comprobar que hemos marcado el mismo número de contenidos y
         * compararemos los obligatorios.
         * 3. Una vez se hayan visualizado los contenidos obligatorios (o todos los contenidos)
         * se marcara como finalizado.
         */
        $track = TrackModulo::where("modulo_id", "=", $modulo_id)
            ->where("user_id", "=", Auth::user()->id)
            ->where("convocatoria_id", "=", $this->convocatoria_id)
            ->first();
        if (empty($track)) {
            abort(404);
        }
        if (!$track->completado) {
            //Si no lo hemos marcado como completado, comprobamos si lo tenemos que marcar.
            $modulo = Modulo::findorFail($modulo_id);
            if (empty($modulo)) {
                abort(404);
            }

            $tipoContenidoId = TipoContenido::where("slug", "tema")->first()->id;
            $trackContenidos = TrackContenido::where("modulo_id", "=", $modulo_id)
                ->where("user_id", "=", Auth::user()->id)
                ->where("convocatoria_id", "=", $this->convocatoria_id);
            $contenidos = $modulo->contenidos()->where("tipo_contenido_id", "<>", $tipoContenidoId)->activos();

            $contenidos_visitados = $trackContenidos->completados()->count();
            $contenidos_visitados_obligatorios = $trackContenidos->completados()->obligatorios()->count();
            $contenidos_para_visitar = $contenidos->count();
            $contenidos_para_visitar_obligatorios = $contenidos->obligatorios()->count();
            if (($contenidos_para_visitar_obligatorios == 0
                    && $contenidos_para_visitar == $contenidos_visitados) ||
                ($contenidos_para_visitar_obligatorios > 0
                    && $contenidos_para_visitar_obligatorios == $contenidos_visitados_obligatorios)
            ) {
                //Marcamos el módulo como completado
                $track->fecha_fin = Carbon::now();
                $modulo_aprobado = $this->calcularModulo($modulo_id, $this->convocatoria_id);
                $track->aprobado = $modulo_aprobado[0];
                $track->nota = $modulo_aprobado[1];
                $track->completado = true;
                $track->save();
            }
        }
    }

    public function calcularStatsAsignatura($asignatura_id, $convocatoria_id, $user_id = "")
    {

        // Si no me pasan el usuario al cual hay que obtener su porcentaje para aprobar,
        // cojo el que esta logonado en el sistema...
        if ($user_id == "") {
            $user_id = Auth::user()->id;
        }

        // Obtengo la asignatura, si esta no existe, devolvemos un error, ya que no se sabe como ha entrado aquí...
        $asignatura = Asignatura::findOrFail($asignatura_id);

        if (empty($convocatoria_id)) {
            return array("corte" => 10, "nota" => 10);
        }

        // Buscamos la convocatoria que vamos a mostrar sus estadísticas...
        $convocatoria = $this->getInformacionConvocatoria($convocatoria_id);
        if (empty($convocatoria)) {
            abort(403, "La convocatoria de esta asignatura no está activa");
        }

        // Obtengo aquellos módulos que puntúan dentro de esta asignatura...
        $modulos = $asignatura->modulos()->where('puntua', '=', 1);

        // Sólo entramos en el caso que haya módulos que puntúan, si no, devolvemos ya el 100%
        if ($modulos->count() > 0) {
            $peso_total = $asignatura->modulos()->where('puntua', '=', 1)->sum("peso");

            // Sólo entramos en el caso que peso total sea mayor que 0,
            // si no significa que ningún módulo tiene un peso sobre la asignatura y se devuelve el 100%
            if ($peso_total > 0) {
                //Hacemos los calculos en base a 10 e iniciamos la nota de la asignatura a 0
                $nota_total = 10;
                $nota_asignatura = 0;

                // Recorro los módulos para obtener su información
                foreach ($modulos->get() as $modulo) {
                    $peso_modulo = $modulo->peso;
                    $modulo_track = $modulo->resultados()
                        ->where("user_id", "=", $user_id)
                        ->where("convocatoria_id", "=", $convocatoria_id)->first();
                    $nota_modulo = (!empty($modulo_track)) ? $modulo_track->nota : 0;

                    $nota_maxima_eval = ($peso_total) ? ($peso_modulo * $nota_total) / $peso_total : 0;
                    //Dividimos entre 10 porque las notas de las evaluaciones están en base 10
                    $nota_eval_modulo = ($nota_modulo * $nota_maxima_eval) / 10;

                    $nota_asignatura = $nota_asignatura + $nota_eval_modulo;
                }

                $corte = ($convocatoria->porcentaje * $nota_total) / 100;

                return array("corte" => $corte, "nota" => $nota_asignatura);
            }
        }

        return array("corte" => 10, "nota" => 10);
    }

    public function calcularAsignatura($asignatura_id, $convocatoria_id, $user_id = "")
    {
        $corte = $this->calcularStatsAsignatura($asignatura_id, $convocatoria_id, $user_id);

        // Una vez tenemos las notas solo hay que comprobar que se supere el porcentaje especificado
        // en la convocatoria de este módulo.

        if ($corte["corte"] <= $corte["nota"]) {
            return array(0 => true, 1 => $corte["nota"]);
        } else {
            return array(0 => false, 1 => $corte["nota"]);
        }
    }

    public function calcularModulo($modulo_id, $convocatoria_id, $user_id = "")
    {
        /*
         * Debemos buscar los contenidos evaluables que puntuan y ver si los hemos aprobado o no
         * 1. Buscar contenidos tipo evaluación y mirar si están aprobadas
         * 2. Si no hay ningún contenido evaluación, con que estén visualizados los datos,
         *  los marcaremos como aprobados y un 10 de nota.
         * 3. ???
         */
        if ($user_id == "") {
            $user_id = Auth::user()->id;
        }

        $modulo = Modulo::findOrFail($modulo_id);

        $convocatoria = $this->getInformacionConvocatoria($convocatoria_id, $modulo_id);
        if (empty($convocatoria)) {
            abort(403, "La convocatoria de este módulo no está activa");
        }

        $evaluaciones = $modulo->evaluaciones()->where('puntua', '=', 1);
        if ($evaluaciones->count() <= 0) {
            return array(0 => true, 1 => 10);
        } else {
            //Revisamos las evaluaciones y devolvemos si está aprobado o no.
            /*
             * 1. Coger las notas de cada evaluacion y calcuar su peso en el módulo
             * 2. Sumar esa nota a la suma de notas del resto de evaluaciones del módulo
             */
            $peso_total = $modulo->evaluaciones()->where('puntua', '=', 1)->sum("peso");

            if ($peso_total == 0) {
                return array(0 => true, 1 => 10);
            }

            //Hacemos los calculos en base a 10
            $nota_total = 10;

            $nota_modulo = 0;
            foreach ($evaluaciones->get() as $evaluacion) {
                $peso_evaluacion = $evaluacion->peso;
                $modulo_track = $evaluacion->track()->validados()
                    ->where("user_id", "=", $user_id)
                    ->where("convocatoria_id", "=", $convocatoria_id)
                    ->first();
                $nota_evaluacion = (!empty($modulo_track)) ? $modulo_track->nota : 0;

                $nota_maxima_eval = ($peso_total) ? ($peso_evaluacion * $nota_total) / $peso_total : 0;
                //Dividimos entre 10 porque las notas de las evaluaciones están en base 10
                $nota_eval_modulo = ($nota_evaluacion * $nota_maxima_eval) / 10;

                $nota_modulo = $nota_modulo + $nota_eval_modulo;
            }
            // Una vez tenemos las notas solo hay que comprobar que se supere el porcentaje
            // especificado en la convocatoria de este módulo.
            $corte = ($convocatoria->porcentaje * $nota_total) / 100;
            if ($corte <= $nota_modulo) {
                return array(0 => true, 1 => $nota_modulo);
            } else {
                return array(0 => false, 1 => $nota_modulo);
            }
        }
    }

    public function calcularContenido($contenido_id, $user_id = "", $convocatoria_id = "")
    {
        if ($user_id == "") {
            $user_id = Auth::user()->id;
        }
        if ($convocatoria_id == "") {
            $convocatoria_id = $this->convocatoria_id;
        }

        $contenido = Contenido::findorFail($contenido_id);
        if (empty($contenido)) {
            abort(404);
        }

        //Buscamos los resultados de la evaluación de este usuario en esta convocatoria
        // (por def. usuario actual y convocatoria actual)
        $resultados = RespuestaResultado::where("contenido_id", "=", $contenido_id)
            ->where("user_id", "=", $user_id)
            ->where("convocatoria_id", "=", $convocatoria_id);
        $total = clone $resultados;
        $total = $total->where("correcta", 1)->sum("puntos_correcta");
        $obtenido = $resultados->sum("puntos_obtenidos");

        if ($total <= 0) {
            $total = 1;
        }

        //Si por alguna razón hubieramos sacado menos de un 0, ponemos un 0
        if ($obtenido < 0) {
            $obtenido = 0;
        }
        //Si por alguna razón hubieramos sacado mas de la nota total ponemos la nota total
        if ($obtenido > $total) {
            $obtenido = $total;
        }

        //Hay que mirar que no tenga ningun intento validado
        $track_content = TrackContenidoEvaluacion::where("contenido_id", "=", $contenido_id)
            ->where("user_id", "=", $user_id)
            ->where("convocatoria_id", "=", $convocatoria_id);
        $num_intento = $track_content->count() + 1;

        if ($track_content->validados()->count() == 0) {
            $track_eval = new TrackContenidoEvaluacion();
            $track_eval->contenido_id = $contenido_id;
            $track_eval->modulo_id = $contenido->modulo->id;
            $track_eval->asignatura_id = $contenido->modulo->asignatura->id;
            $track_eval->convocatoria_id = $convocatoria_id;
            $track_eval->user_id = $user_id;
            $track_eval->numero_intento = $num_intento;
            $track_eval->fecha_intento = Carbon::now();
            $track_eval->validado = true;
            $track_eval->puntuacion_obtenida = $obtenido;
            $track_eval->puntuacion_maxima = $total;
            $track_eval->nota = ($obtenido * 10) / $total;

            $aprobado = false;
            $porcentaje_aprobado = ($track_eval->nota) * 10;
            if ($porcentaje_aprobado >= $contenido->evaluacion->porcentaje_aprobado) {
                $aprobado = true;
            }

            $track_eval->aprobado = $aprobado;

            $track_eval->save();
        }
    }

    public function resetearContenido(Contenido $contenido, $user_id = "", $convocatoria_id = "")
    {
        /*
         * Que necesitamos para saber si podemos resetear el contenido
         * 1. Debe tener un registro
         * 2. El contenido debe permitir resetear
         * 3. No debemos haber realizado el máximo de intentos
         */
        if ($user_id == "") {
            $user_id = Auth::user()->id;
        }
        if ($convocatoria_id == "") {
            $convocatoria_id = $this->convocatoria_id;
        }
        if ($contenido->evaluacion->permitir_resetear) {
            $track_content = TrackContenidoEvaluacion::where("contenido_id", "=", $contenido->id)
                ->where("user_id", "=", $user_id)
                ->where("convocatoria_id", "=", $convocatoria_id);
            //Comprobamos que no hayamos realizado todos los intentos
            if ($contenido->evaluacion->numero_resets > $track_content->count()) {
                //Como permitimos resetear miramos que tengamos un registro validado
                $content = $track_content->validados()->first();
                if (empty($content)) {
                    abort(404);
                }

                //Debemos borrar todos los datos de este usuario para este examen.
                $track_eval = RespuestaResultado::where("contenido_id", "=", $contenido->id)
                    ->where("user_id", "=", $user_id)
                    ->where("convocatoria_id", "=", $convocatoria_id)
                    ->delete();

                //Lo marcamos como no validado y lo guardamos
                $content->validado = false;
                $content->save();

                //Desmarcamos lo relacionado con este módulo
                $this->desmarcarContenido($contenido->id, $user_id, $convocatoria_id);
                $this->desmarcarModulo($contenido->modulo->id, $user_id, $convocatoria_id);
                $this->desmarcarAsignatura($contenido->modulo->asignatura->id, $user_id, $convocatoria_id);
            }
        }
    }

    public static function desmarcarContenido($contenido_id, $user_id, $convocatoria_id)
    {
        $track = TrackContenido::where("contenido_id", "=", $contenido_id)
            ->where("user_id", "=", $user_id)
            ->where("convocatoria_id", "=", $convocatoria_id)
            ->first();
        if (empty($track)) {
            abort(404);
        }
        $track->completado = false;
        $track->save();
    }

    public static function desmarcarModulo($modulo_id, $user_id, $convocatoria_id)
    {
        $track = TrackModulo::where("modulo_id", "=", $modulo_id)
            ->where("user_id", "=", $user_id)
            ->where("convocatoria_id", "=", $convocatoria_id)
            ->first();

        if (empty($track)) {
            abort(404);
        }
        $track->completado = false;
        $track->aprobado = false;
        $track->nota = 0;
        $track->fecha_fin = null;

        $track->save();
    }

    /**
     * Desmarca la finalización de una asignatura en una convocatoria para un usuario
     *
     * @param $asignatura_id
     * @param $user_id
     * @param $convocatoria_id
     *
     * @return void
     */
    public static function desmarcarAsignatura($asignatura_id, $user_id, $convocatoria_id)
    {
        $track = TrackAsignatura::where("asignatura_id", "=", $asignatura_id)
            ->where("user_id", "=", $user_id)
            ->where("convocatoria_id", "=", $convocatoria_id)
            ->first();

        if (empty($track)) {
            abort(404);
        }
        $track->completado = false;
        $track->aprobado = false;
        $track->nota = 0;
        $track->fecha_fin = null;
        $track->save();
    }

    public function completeAsignatura($asignatura)
    {
        //Primero revisamos si tenemos alguna asignatura que completar antes

        if ($asignatura->obligatorio_id > 0) {
            //Como tenemos una asginatura asignada miramos si esta completa.
            $pretest = TrackAsignatura::where("asignatura_id", "=", $asignatura->obligatorio_id)
                ->where("user_id", "=", Auth::user()->id)->first();
            if (empty($pretest) || !$pretest->completado) {
                $asignatura_ob = Asignatura::find($asignatura->obligatorio_id);
                $this->asignaturaActiva($asignatura_ob);
                //Tenemos que revisar los modulos de esta asignatura
                $modulos = Modulo::activos()->where("asignatura_id", "=", $asignatura->obligatorio_id)
                    ->get();
                if (!empty($modulos)) {
                    foreach ($modulos as $modulo_original) {
                        //Cogemos el primer contenido de este módulo y lo redireccionamos a el.
                        $contenido = Contenido::activos()
                            ->where("modulo_id", "=", $modulo_original->id)
                            ->first();
                        if ($modulo_original->obligatorio_id > 0) {
                            $modulo_requerido = Modulo::find($modulo_original->obligatorio_id);
                            if (!empty($modulo_requerido) &&
                                $this->moduloCompleto($modulo_requerido) &&
                                !$this->moduloCompleto($modulo_original)
                            ) {
                                return 'contenido/detalle-contenido/' .
                                    $contenido->url_amigable . '/' . $contenido->id;
                            }
                        } elseif (!$this->moduloCompleto($modulo_original)) {
                            return 'contenido/detalle-contenido/' . $contenido->url_amigable . '/' . $contenido->id;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function recalcularNotasAsignatura($asignatura_id, $convocatoria_id)
    {
        $asignatura_acabada = true;
        // Leemos la asignatura actual
        $asignatura = Asignatura::findorFail($asignatura_id);
        if (empty($asignatura)) {
            abort(404);
        }


        // Leemos todos los tracks de la asignatura/convocatoria
        $tracks = TrackAsignatura::where("asignatura_id", "=", $asignatura_id)
            ->where("convocatoria_id", "=", $convocatoria_id)
            ->where("completado", "1")
            ->get();
        if (empty($tracks)) {
            abort(404);
        }

        foreach ($tracks as $track) {
            foreach ($asignatura->modulos as $modulo) {
                $this->recalcularNotasModulo($modulo->id, $convocatoria_id, $track->user_id);
            }
            //Marcamos el módulo como completado
            $asignatura_aprobada = $this->calcularAsignatura($asignatura->id, $convocatoria_id, $track->user_id);
            $track->aprobado = $asignatura_aprobada[0];
            $track->nota = $asignatura_aprobada[1];
            $track->save();
        }
    }

    public function recalcularNotasModulo($modulo_id, $convocatoria_id, $user_id)
    {
        $track = TrackModulo::where("modulo_id", "=", $modulo_id)
            ->where("user_id", "=", $user_id)
            ->where("convocatoria_id", "=", $convocatoria_id)
            ->where("completado", "1")
            ->first();
        if (empty($track)) {
            abort(404);
        }

        //Si no lo hemos marcado como completado, comprobamos si lo tenemos que marcar.
        $modulo = Modulo::findorFail($modulo_id);
        if (empty($modulo)) {
            abort(404);
        }

        $trackContenidos = TrackContenido::where("modulo_id", "=", $modulo_id)
            ->where("user_id", "=", $user_id)
            ->where("convocatoria_id", "=", $convocatoria_id);
        $contenidos = $modulo->contenidos()->activos();

        $contenidos_visitados = $trackContenidos->completados()->count();
        $contenidos_visitados_obligatorios = $trackContenidos->completados()->obligatorios()->count();
        $contenidos_para_visitar = $contenidos->count();
        $contenidos_para_visitar_obligatorios = $contenidos->obligatorios()->count();
        if (($contenidos_para_visitar_obligatorios == 0 &&
                $contenidos_para_visitar == $contenidos_visitados) ||
            ($contenidos_para_visitar_obligatorios > 0 &&
                $contenidos_para_visitar_obligatorios == $contenidos_visitados_obligatorios)
        ) {
            //Marcamos el módulo como completado
            $modulo_aprobado = $this->calcularModulo($modulo_id, $this->convocatoria_id, $user_id);
            $track->aprobado = $modulo_aprobado[0];
            $track->nota = $modulo_aprobado[1];
            $track->save();
        }
    }

    public function asignaturaRequerida(Asignatura $asignatura)
    {
        if ($asignatura->obligatorio_id != null) {
            //Se trae la asignatura que es obligatoria realizar antes
            $asignatura_original = Asignatura::findorFail($asignatura->obligatorio_id);
            // Se realiza la comprobación si se encuentra completada la asignatura
            $track = TrackAsignatura::where("asignatura_id", "=", $asignatura_original->id)
                ->where("user_id", "=", Auth::user()->id)
                ->first();
            if (empty($track) || $track->completado == 0) {
                return false;
            }
        }
        return true;
    }

    public function getasignaturaRequerido($asignatura)
    {
        if ($asignatura->obligatorio_id != null) {
            //Comprobamos si la asginatura se requiere. Y si es así, traemos el titulo.
            $asignatura_original = Asignatura::findorFail($asignatura->obligatorio_id);
            return $asignatura_original->titulo;
        }
        return "";
    }
}
