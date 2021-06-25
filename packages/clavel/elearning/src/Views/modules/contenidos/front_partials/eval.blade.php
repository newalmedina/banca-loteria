<?php
$evalQuery = $contenido->trackEvalByUserConvocatoria(Auth::user()->id, $contenido->tracking->convocatoria_id);
// Esta variable es un Collection de instancias del TrackContenidoEvaluacion, que son todos los intentos del usuario a este examen.
$intentos = $evalQuery->get();
$validados = $evalQuery->validados()->get();
?>

@if($contenido->evaluacion->presencial)
    Es presencial por lo que no mostramos nada de contenido.
@else
    <!-- Modal para la Modificación de estado de proyectos -->
    <div id="modalFaltanRespuestas" class="modal fade" role="dialog" aria-labelledby="modalFaltanRespuestas">
        <div class="modal-dialog modal-lg">
            <div id="content_block" class="modal-content">
                <div class="modal-header modal-header-danger">

                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">{{ trans('elearning::contenidos/front_lang.titulo_faltan_respuesta') }}</h4>
                </div>
                <div class="modal-body">
                    {!! trans("elearning::contenidos/front_lang.marcar-respuestas") !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("general/front_lang.cerrar") }}</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Fin Modal para la Modificación de estado de proyectos -->

    <!-- Modal Enviando-->
    <div class="modal fade" id="sendingExamModal" tabindex="-1" role="dialog" aria-labelledby="sendingExamModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="loader"></div>
                    <div clas="loader-txt">
                        <p>{!! trans("elearning::contenidos/front_lang.enviando_examen") !!}<br><br><small>{!! trans("elearning::contenidos/front_lang.paciencia") !!}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($contenido->contenido!='') )
        <div class="row">
            <div class="col-sm-12">
                <h3>{{ $contenido->nombre }}</h3>
                <div class="alert alert-default">
                    <div class="row">
                        <div class="col-sm-1">
                            <i class="fa fa-info-circle" style="font-size:5em;" aria-hidden="true"></i>
                        </div>
                        <div class="col-sm-11">
                            {!! $contenido->contenido !!}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(!empty($contenido->evaluacion->porcentaje_aprobado) && empty($validados->count()))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-warning">
                    {{ trans("elearning::contenidos/front_lang.necesario_superar") }}
                    {{ $contenido->evaluacion->porcentaje_aprobado }}
                    {{ trans("elearning::contenidos/front_lang.necesario_superar_2") }}
                </div>
            </div>
        </div>
    @endif
    @if($contenido->evaluacion->porcentaje_aprobado > 0 && !empty($validados->count()))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert @if($intentos->first()->aprobado) alert-success @else alert-danger @endif">
                    <div class="row">
                        <div class="col-sm-8">
                            @if($intentos->first()->aprobado)
                                @if($contenido->contenido_aprobado!='')
                                    {!! $contenido->contenido_aprobado !!}
                                @else
                                    {{ trans("elearning::contenidos/front_lang.superado") }}
                                @endif
                            @else
                                @if($contenido->contenido_suspendido!='')
                                    {!! $contenido->contenido_suspendido !!}
                                @else
                                    {{ trans("elearning::contenidos/front_lang.no_superado") }}
                                @endif
                            @endif
                            @if($contenido->evaluacion->mostrar_resultado)
                                <br>
                                {{ trans("elearning::contenidos/front_lang.su_puntuacion").($intentos->first()->nota*10)."% ".round($intentos->first()->puntuacion_obtenida,0)."/".round($intentos->first()->puntuacion_maxima,0) }}
                            @endif
                        </div>

                        <div class="col-sm-4 text-right">
                            @if($contenido->evaluacion->permitir_resetear && $contenido->evaluacion->numero_resets > $intentos->count() && $intentos->first()->aprobado == 0)
                                <button type="button" onclick="Reset();" class="btn btn-info">{{ trans("elearning::contenidos/front_lang.repetir") }}</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {!! Form::model($validados->first(), array('route' => array('front.contenidos.store',$contenido->url_amigable,$contenido->id), 'method' => 'POST', 'id' => 'formData', 'class' => 'form-horizontal eval_form', 'files' => true), array('role' => 'form')) !!}
    {!! Form::hidden('contenido_id', $contenido->id, array('id' => 'contenido_id')) !!}
    {!! Form::hidden('modulo_id', $contenido->modulo->id, array('id' => 'modulo_id')) !!}
    {!! Form::hidden('asignatura_id', $contenido->modulo->asignatura->id, array('id' => 'asignatura_id')) !!}

    {{-- Examen normal sin grupos --}}
    @if(!$contenido->evaluacion->grupos_preguntas)
        @php
            // Ponemos el array de colores a vacio para dejar solo el color estandar
            $gruposDePreguntas = [];

            $preguntas = $contenido->preguntas()
                ->activas()
                ->ordered($contenido->evaluacion->preguntas_aleatorias)
                ->limited($contenido->evaluacion->numero_preguntas_visibles,$intentos->first())
                ->get();
        @endphp
    @else
        {{-- Examen con grupos de preguntas --}}
        @php
            // Leemos el array de colores para mostrar la pregunta
            $gruposDePreguntas = Clavel\Elearning\Models\GrupoPregunta::where('contenido_id', $contenido->id)->pluck('color', 'id')->all();

            // Si no ha hecho nunca el examen mostramos las preguntas según criterios de grupo
            $examenDeGrupos = true;
            $evaluacion = $intentos->first();
            if(!empty($evaluacion) && $evaluacion->validado == true) {
                $examenDeGrupos = false;
            }

            if($examenDeGrupos) {
                $preguntasTodas = $contenido
                    ->preguntas()
                    ->activas()
                    ->ordered($contenido->evaluacion->preguntas_aleatorias)
                    ->get();

                $grupos = [];
                foreach ($preguntasTodas as $pregunta) {
                    $grupos[$pregunta->grupo_pregunta_id][] = $pregunta;
                }
                // Nos quedamos con las claves de los grupos para despues acceder por indice
                $clavesGrupos = array_keys($grupos);
                // En este punto tenemos preguntas agrupadas por grupo. Atención porque el grupo con id 0 son las que no tienen
                // grupo
                // Listado de preguntas final
                $preguntasGrupos = [];
                // máximo numero de preguntas que tendrá el listado final
                $maxPreguntas = (empty($contenido->evaluacion->numero_preguntas_visibles)?
                                $preguntasTodas->count():
                                $contenido->evaluacion->numero_preguntas_visibles);
                // Contador de preguntas que vamos seleccionando
                $contadoPreguntas = 0;
                // Indicador de si en el array de grupos de preguntas nos quedamos sin preguntas, es decir el número de preguntas
                // que tenemos es inferior al máximo de preguntas que queremos
                $sinPreguntas = false;
                // Contados de grupos para ir navegando por ellos
                $maxGrupos = sizeof($grupos);
                // Grupo actual
                $contadorGrupos = 0;
                // Recorremos los grupos y vamos quitando una pregunta de cada grupo y la insertamos en el listado final
                while($contadoPreguntas<$maxPreguntas && !$sinPreguntas) {
                    // Obtenemos la pregunta
                    $preguntasGrupos[] = $grupos[$clavesGrupos[$contadorGrupos]][0];
                    // La borramos del grupo para que no la tenga en cuenta la proxima vez y verificamos si nos hemos quedado
                    // sin preguntas en ese grupo en cuyo caso lo quitamos
                    unset($grupos[$clavesGrupos[$contadorGrupos]][0]);
                    $grupos[$clavesGrupos[$contadorGrupos]] = array_values($grupos[$clavesGrupos[$contadorGrupos]]);
                    if(empty($grupos[$clavesGrupos[$contadorGrupos]])) {
                        unset($grupos[$clavesGrupos[$contadorGrupos]]);
                        $grupos = array_values($grupos);
                        $maxGrupos = sizeof($grupos);
                        $clavesGrupos = array_keys($grupos);
                        // No incrementamos el listado de grupos y pasamos al siguiente y vigilamos no lleguemos a cero
                        // Que quiere decir que no hay grupo y daria division por cero
                        if($maxGrupos>0) {
                            $contadorGrupos=$contadorGrupos%$maxGrupos;
                        }
                    } else {
                        // Pasamos al grupo siguiente
                        $contadorGrupos=($contadorGrupos+1)%$maxGrupos;
                    }
                    // Nos hemos quedado sin preguntas?
                    if(empty($grupos)) {
                        $sinPreguntas = true;
                    }
                    // Pasamos a la siguiente pregunta
                    $contadoPreguntas++;
                }
                $preguntas = collect($preguntasGrupos);
            } else {
                // Examen ya realizado
                $preguntas = $contenido->preguntas()
                ->limited($contenido->evaluacion->numero_preguntas_visibles,$intentos->first())
                ->get();
            }
        @endphp
    @endif

    <div class="row">
        <div class="col-sm-12">

            @foreach($preguntas as $key=>$pregunta)
                <div class="row">
                    <div class="col-xs-12 title_question pregunta">
                        <div class="p_orden" style="float:left;">
                            <span class="numero_pregunta">{!! ++$key.". " !!}</span>
                            @php
                                $colorGrupo = "";
                                if($pregunta->grupo_pregunta_id != 0 && isset($gruposDePreguntas[$pregunta->grupo_pregunta_id])) {
                                    $colorGrupo = ' style="background-color:'.$gruposDePreguntas[$pregunta->grupo_pregunta_id].' !important;" ';
                                }
                            @endphp
                            <span class="grupo_pregunta" {!! $colorGrupo !!}></span>
                        </div>
                        <div class="p_text titulo_pregunta" style="padding-left: 20px; ">{!! $pregunta->nombre !!}</div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">

                            @if($pregunta->tipo->slug == "unica" || $pregunta->tipo->slug == "multiple")
                            <div class="radio-list">
                                {{-- Preguntas tipo radio button o multi seleccion --}}
                                @foreach($pregunta->respuestas()->activas()->ordered($contenido->evaluacion->respuestas_aleatorias)->get() as $respuesta)
                                    <div class="row">
                                        @if(!is_null($validados->first()) && $contenido->evaluacion->mostrar_respuesta && $respuesta->correcta && ($contenido->evaluacion->numero_resets <= $intentos->count()|| $intentos->first()->aprobado == 1))
                                            <label class="radio-inline checkbox-inline col-xs-10 col-xs-offset-2 col-sm-11 col-sm-offset-1 fa-correct">
                                                <i class="fa fa-check-circle fa-eval fa-correct" aria-hidden="true"></i>
                                        @elseif(!is_null($validados->first()) && $respuesta->resultado->count() > 0 && $contenido->evaluacion->mostrar_respuesta && !$respuesta->correcta && $respuesta->resultado()->where("user_id","=",Auth::user()->id)->first()->marcada && ($contenido->evaluacion->numero_resets <= $intentos->count()|| $intentos->first()->aprobado == 1))
                                            <label class="radio-inline checkbox-inline col-xs-10 col-xs-offset-2 col-sm-11 col-sm-offset-1 fa-incorrect">
                                                 <i class="fa fa-times-circle fa-eval fa-incorrect" aria-hidden="true"></i>
                                        @else
                                            <label class="radio-inline checkbox-inline col-xs-10 col-xs-offset-2 col-sm-11 col-sm-offset-1">
                                        @endif

                                        @if($pregunta->tipo_pregunta_id == 1)
                                            {!! Form::radio('answers['.$pregunta->id.']', $respuesta->id, (!empty($validados->first()) && $respuesta->resultado->count() > 0) ? $respuesta->resultado()->where("user_id","=",Auth::user()->id)->first()->marcada : null, array('id'=>$pregunta->id,"class"=> ($pregunta->obligatoria) ? "obligatoria" : "",(!empty($validados->first())) ? "disabled":"")) !!}
                                        @elseif($pregunta->tipo_pregunta_id == 2)
                                            {!! Form::checkbox('answers['.$pregunta->id.']['.$respuesta->id.']', 1, (!empty($validados->first()) && $respuesta->resultado->count() > 0) ? $respuesta->resultado()->where("user_id","=",Auth::user()->id)->first()->marcada : null, array('id'=>$pregunta->id,"class"=> ($pregunta->obligatoria) ? "obligatoria" : "",(!empty($validados->first())) ? "disabled":"")) !!}
                                        @elseif($pregunta->tipo_pregunta_id == 3)
                                            {!! Form::textarea('answers['.$pregunta->id.']', (!empty($validados->first()) && $respuesta->resultado->count() > 0) ? $respuesta->resultado()->where("user_id","=",Auth::user()->id)->first()->observaciones : null, array('id'=>$pregunta->id,"class"=> ($pregunta->obligatoria) ? "obligatoria" : "",(!empty($validados->first())) ? "disabled":"")) !!}
                                        @endif

                                        {!! $respuesta->nombre !!}
                                        @if(!is_null($validados->first()) && $respuesta->resultado->count() > 0 && $contenido->evaluacion->mostrar_respuesta && ($contenido->evaluacion->numero_resets <= $intentos->count()|| $intentos->first()->aprobado == 1) && ($respuesta->correcta || $respuesta->resultado()->where("user_id","=",Auth::user()->id)->first()->marcada) && $respuesta->comentario!='')
                                            <div class="testimonial testimonial-style-3">
                                                <blockquote>
                                                    {!! $respuesta->comentario !!}
                                                </blockquote>
                                            </div>
                                        @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @else
                                {{-- Pregunta tipo texto --}}
                                @php
                                    $respuesta = $pregunta->respuestas()->activas()->first();
                                @endphp
                                <div class="pregunta_texto_textarea">
                                    {!! Form::textarea('answers['.$pregunta->id.']',
                                    (!empty($validados->first()) && $respuesta->resultado->count() > 0) ?
                                    $respuesta->resultado()->where("user_id","=",Auth::user()->id)->first()->observaciones : null,
                                    array('id'=>$pregunta->id,"class"=> ($pregunta->obligatoria) ? "obligatoria text-control" : "text-control",
                                    (!empty($validados->first())) ? "disabled":"")) !!}

                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @if(empty($validados->count()))
        <div class="row">
            <div class="col-sm-12 text-right">
                <button id="btnEnviarExamen" type="button" onclick="saveEval()" class="btn btn-info">{{ trans("elearning::contenidos/front_lang.enviar-evaluacion") }}</button>
            </div>
        </div>
    @endif
    {!! Form::close() !!}
@endif

<br clear="all">
<br clear="all">
