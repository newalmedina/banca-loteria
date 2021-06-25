@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')


@stop

@section('breadcrumb')
    <li><a href="{{ url("/admin/profesor") }}">{{ trans('elearning::profesor/admin_lang.zona_profesor') }}</a></li>
    <li><a href="{{ url("admin/profesor/detalle/asignatura/".$modulo->asignatura->id) }}">{{ $modulo->asignatura->titulo }}</a></li>
    <li><a href="{{ url("admin/profesor/detalle/modulo/".$modulo->id) }}">{{ $modulo->nombre }}</a></li>
    <li><a href="{{ url("admin/profesor/detalle/modulo/contenido/".$contenido->id) }}">{{ $contenido->nombre }}</a></li>
@stop

@section('content')
    @include('admin.includes.modals')

    {!! Form::model($validados->first(),
         array('route' => array('admin.profesor.examen.store',$contenido->url_amigable,$contenido->id),
         'method' => 'POST', 'id' => 'formData',
         'class' => 'form-horizontal eval_form', 'files' => true), array('role' => 'form')) !!}
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
            $gruposDePreguntas = Clavel\Elearning\Models\GrupoPregunta::where('contenido_id', $contenido->id)
                    ->pluck('color', 'id')
                    ->all();

            // Si no ha hecho nunca el examen mostramos las preguntas según criterios de grupo
            $examenDeGrupos = true;
            $evaluacion = $intentos->first();

            // Examen ya realizado
            $preguntas = $contenido->preguntas()
            ->limited($contenido->evaluacion->numero_preguntas_visibles,$intentos->first())
            ->get();

        @endphp
    @endif
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">{{ trans('elearning::profesor/admin_lang.examen_detalle_de') }}: {{ $user->userProfile->fullName }}</h3>
        </div>

        <div class="box-body">

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
                                                    @if(!is_null($validados->first()) &&
                                                    $respuesta->correcta)
                                                        <label class="radio-inline checkbox-inline col-xs-10 col-xs-offset-2 col-sm-11 col-sm-offset-1 fa-correct">
                                                            <i class="fa fa-check-circle fa-eval fa-correct" aria-hidden="true"></i>
                                                    @elseif(!is_null($validados->first()) &&
                                                    $respuesta->resultado->count() > 0 &&
                                                    !$respuesta->correcta &&
                                                    $respuesta->resultado()->where("user_id","=",$user->id)->first()->marcada)
                                                        <label class="radio-inline checkbox-inline col-xs-10 col-xs-offset-2 col-sm-11 col-sm-offset-1 fa-incorrect">
                                                            <i class="fa fa-times-circle fa-eval fa-incorrect" aria-hidden="true"></i>
                                                    @else
                                                        <label class="radio-inline checkbox-inline col-xs-10 col-xs-offset-2 col-sm-11 col-sm-offset-1">
                                                    @endif

                                                    @if($pregunta->tipo_pregunta_id == 1)
                                                        {!! Form::radio('answers['.$pregunta->id.']', $respuesta->id, (!empty($validados->first()) && $respuesta->resultado->count() > 0) ? $respuesta->resultado()->where("user_id","=",$user->id)->first()->marcada : null, array('id'=>$pregunta->id,"class"=> ($pregunta->obligatoria) ? "obligatoria" : "",(!empty($validados->first())) ? "disabled":"")) !!}
                                                    @elseif($pregunta->tipo_pregunta_id == 2)
                                                        {!! Form::checkbox('answers['.$pregunta->id.']['.$respuesta->id.']', 1, (!empty($validados->first()) && $respuesta->resultado->count() > 0) ? $respuesta->resultado()->where("user_id","=",$user->id)->first()->marcada : null, array('id'=>$pregunta->id,"class"=> ($pregunta->obligatoria) ? "obligatoria" : "",(!empty($validados->first())) ? "disabled":"")) !!}
                                                    @endif

                                                    {!! $respuesta->nombre !!}
                                                    @if(!is_null($validados->first()) && $respuesta->resultado->count() > 0 && $contenido->evaluacion->mostrar_respuesta && $contenido->evaluacion->numero_resets <= $intentos->count() && ($respuesta->correcta || $respuesta->resultado()->where("user_id","=",$user->id)->first()->marcada) && $respuesta->comentario!='')
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
                                            $respuesta = $pregunta->respuestas()->first();
                                        @endphp
                                        <div class="pregunta_texto_textarea">
                                            {!! Form::textarea('answers['.$pregunta->id.']',
                                            (!empty($validados->first()) && $respuesta->resultado->count() > 0) ?
                                            $respuesta->resultado()->where("user_id","=",$user->id)->first()->observaciones : null,
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
        </div>
    </div>
    {!! Form::close() !!}

@endsection

@section("foot_page")
    <script type="text/javascript">
        $(function () {

        });
    </script>
@stop
