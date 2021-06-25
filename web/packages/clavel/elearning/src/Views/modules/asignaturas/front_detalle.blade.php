@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li><a href="{{ url("/asignaturas") }}">{{ trans("elearning::asignaturas/front_lang.listado") }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-3 col-md-push-9">

                <div class="panel panel-default">
                    <div class="panel-body">
                        <dl>
                            <?php $convocatoria = $asignatura->convocatoria_posible; ?>
                            @if($convocatoria!=null)
                                <dt>{{ trans("elearning::asignaturas/front_lang.convocatoria") }}</dt>
                                <dd>
                                    {{ $convocatoria->fecha_inicio_formatted }}
                                    - {{ $convocatoria->fecha_fin_formatted }}
                                    @if($convocatoria->fecha_fin<Carbon\Carbon::today())
                                        <span
                                            class="text-danger">({{ trans("elearning::asignaturas/front_lang.finalizada") }}
                                            )</span>
                                    @endif
                                    @if($convocatoria->fecha_inicio>Carbon\Carbon::today())
                                        <span
                                            class="text-success">({{ trans("elearning::asignaturas/front_lang.no_iniciada") }}
                                            )</span>
                                    @endif
                                </dd>
                            @endif
                            @if($asignatura->cursoPivot()->count()>0)
                                <dt>{{ trans("elearning::asignaturas/front_lang.cursos") }}</dt>
                                <dd>
                                    <?php $nLoop = 1; ?>
                                    @foreach($asignatura->cursoPivot()->get() as $curso)
                                        @if($nLoop>1), @endif{{ $curso->nombre }}<?php $nLoop++; ?>
                                    @endforeach
                                </dd>
                            @endif
                            @if($asignatura->creditos!='')
                                <dt>{{ trans("elearning::asignaturas/front_lang.creditos") }}</dt>
                                <dd>{{ $asignatura->creditos }}</dd>
                            @endif
                            @if($asignatura->academico!='')
                                <dt>{{ trans("elearning::asignaturas/front_lang.academico") }}</dt>
                                <dd>{{ $asignatura->academico }}</dd>
                            @endif
                            @if($asignatura->caracteristica!='')
                                <dt>{{ trans("elearning::asignaturas/front_lang.caracteristicas") }}</dt>
                                <dd>{{ $asignatura->caracteristica }}</dd>
                            @endif
                            @if($asignatura->plazas!='')
                                <dt>{{ trans("elearning::asignaturas/front_lang.plazas") }}</dt>
                                <dd>{{ $asignatura->plazas }}</dd>
                            @endif
                            @if($asignatura->admision!='')
                                <dt>{{ trans("elearning::asignaturas/front_lang.admision") }}</dt>
                                <dd>{{ $asignatura->admision }}</dd>
                            @endif
                            @if($asignatura->coordinacion!='')
                                <dt>{{ trans("elearning::asignaturas/front_lang.coordinacion") }}</dt>
                                <dd>{{ $asignatura->coordinacion }}</dd>
                            @endif
                            @if($asignatura->estudiantes!='')
                                <dt>{{ trans("elearning::asignaturas/front_lang.estudiantes") }}</dt>
                                <dd>{{ $asignatura->estudiantes }}</dd>
                            @endif

                            @if($asignatura->obligatorio_id!='')
                                <dt>{{ trans("elearning::asignaturas/front_lang.previa_asignatura") }}</dt>
                                <dd>{{ $tracking->getasignaturaRequerido($asignatura) }}</dd>
                            @endif

                        </dl>
                    </div>
                </div>

                @if($asignatura->requiere_codigo)
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle" style="margin-right: 5px;" aria-hidden="true"></i>
                        {{ trans("elearning::asignaturas/front_lang.info_requiere_codigo") }}

                    </div>
                @endif

                @if(!empty($asignatura->titulo))
                    <div class="panel panel-default">
                        <div class="panel-body">
                            @if(Auth::user() != null)
                                <?php $activa = $asignatura->getActiva(); ?>
                                @if($activa["activa"] )
                                        @if($asignatura->obligatorio_id!='' && $tracking->asignaturaRequerida($asignatura) === false )
                                            <dd>{{ trans("elearning::asignaturas/front_lang.bloqueada_asignatura") }}</dd><br>
                                            <strong>{{ $tracking->getasignaturaRequerido($asignatura) }}</strong>
                                            <div style="text-align: center;"><br>
                                            <button type="button" id="volverAsignatura"
                                                    class="btn btn-info">{{ trans("elearning::asignaturas/front_lang.volver") }}</button>
                                            </div>
                                        @else
                                            <p>{{ trans("elearning::asignaturas/front_lang.empezar_info") }}</p>
                                            <div style="text-align: center;">
                                                <button type="button" id="openAsignatura"
                                                        class="btn btn-info">{{ trans("elearning::asignaturas/front_lang.empezar") }}</button>
                                            </div>
                                        @endif

                                @else
                                    <p class="text-warning">{{ $activa["mensaje"] }}</p>
                                @endif

                            @else
                                <p class="text-warning">{{ trans("elearning::asignaturas/front_lang.registrado") }}</p>
                            @endif

                        </div>
                    </div>
                @endif

            </div>
            <div class="col-md-9 col-md-pull-3">
                @if($asignatura->image!='')
                    <img style="width: 100%; display: block;" src="{{ url("asignaturas/openImage/".$asignatura->id) }}" alt="">
                @else
                    <div style="height: 150px; width: 100%; display: block; background-color: #F4F4F4; text-align: center">
                        <i class="fa fa-laptop" style="font-size: 64px; margin-top: 50px;" aria-hidden="true"></i>
                    </div>
                @endif
                <br clear="all">
                @if(!empty($asignatura->titulo))
                    <h2>{{ $asignatura->titulo }}</h2>
                    {!! ($asignatura->descripcion!='') ? $asignatura->descripcion : $asignatura->breve !!}
                @else
                    <div class="alert alert-warning">
                        <?php $traducidos = $asignatura->getTraduccionesReales(); ?>
                        {{ trans_choice("general/front_lang.no_lang", $traducidos->count()) }}:
                        <strong>{{ $asignatura->getTraduccionesReales()->implode("name", ", ") }}</strong>
                    </div>
                @endif
                <br clear="all">

                @if($asignatura->modulos()->activos()->count()>0 and !empty($asignatura->titulo))
                    @foreach($asignatura->modulos()->activos()->orderBy("orden")->get() as $modulo)
                        <div class="panel panel-default box-shadow-custom">
                            <div class="panel-heading">
                                <div class="feature-box feature-box-primary">
                                    <div class="feature-box-icon"
                                         @if($modulo->fondo!='') style="background-color: {{$modulo->fondo}} !important;" @endif>
                                        <i class="fa  fa-graduation-cap" aria-hidden="true"></i>
                                    </div>
                                    <div class="feature-box-info">
                                        @if(!empty($modulo->nombre))
                                            <h4 class="mb-sm">{{ $modulo->nombre }}</h4>
                                            <p class="mb-lg">{!! $modulo->descripcion !!}</p>
                                        @else
                                            <div class="alert alert-warning mb-none">
                                                <?php $traducidos = $modulo->getTraduccionesReales(); ?>
                                                {{ trans_choice("general/front_lang.no_lang", $traducidos->count()) }}:
                                                <strong>{{ $modulo->getTraduccionesReales()->implode("name", ", ") }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($modulo->contenidos()->activos()->count()>0)
                                <div class="panel-body list-group" style="padding: 0px;">
                                    @foreach($modulo->contenidos()->activos()->orderBy('lft')->get() as $contenido)
                                        <?php
                                        $output = "";
                                        for ($i = 0; $i < $contenido->depth; $i++) {
                                            $output .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        }
                                        ?>
                                        <p class="list-group-item">{!! $output.$contenido->nombre  !!}</p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <br clear="all">
                    @endforeach
                @else
                    <p class="text-warning">{{ trans("elearning::asignaturas/front_lang.No_existe_modulos") }}</p>
                @endif

            </div>
        </div>
    </div>
    <br clear="all">
@endsection


@section('foot_page')
    <script>
        $(document).ready(function() {
            $("#volverAsignatura").click(function(e) {
                e.preventDefault();
                window.location = "{{ url("asignaturas/") }}";
            });


            $("#openAsignatura").click(function(e) {
                e.preventDefault();
                window.location = "{{ url("asignaturas/contenido/".$asignatura->url_amigable."/".$asignatura->id) }}";
            });
        });
    </script>
@endsection
