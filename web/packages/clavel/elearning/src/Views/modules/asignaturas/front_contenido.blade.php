@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li><a href="{{ url("/asignaturas") }}">{{ trans("elearning::asignaturas/front_lang.listado") }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include("front.includes.modal_curso", compact("asignatura"))

    <div class="main_content">
        <div class="container curso">

            <section class="call-to-action call-to-action-default mb-xl">
                <div class="call-to-action-content">
                    <div class="row">

                        <div class="col-md-4" style="text-align: left;">
                            <div class="custom-size-cabecera">{{ trans("elearning::asignaturas/front_lang.cursado") }}</div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-primary"
                                     data-appear-progress-animation="{{ $avance }}%"
                                     data-appear-animation-delay="900">{{ $avance }}
                                    %
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-md-offset-2" style="text-align: left;">
                            <ul class="accommodations text-uppercase font-weight-bold p-none text-sm"
                                style="list-style: none;">
                                <li>
                                    <div class="row">
                                        <div class="col-xs-5"><span
                                                class="accomodation-title">{{ trans("elearning::asignaturas/front_lang.porcentaje") }}</span>
                                        </div>
                                        <div class="col-xs-6">
                                            @if(!empty($track_asignatura) && $track_asignatura->aprobado)
                                                <div class="progress progress-sm"
                                                     style="margin-bottom: 0; margin-top: 5px; height: 15px;">
                                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                                         aria-valuenow="{{ ($nota_asig = (int) ($track_asignatura->nota * 10))  }}" aria-valuemin="0" aria-valuemax="100"
                                                         style="width: {{ $nota_asig  }}%; padding-top: 2px; font-weight: 100">{{ $nota_asig  }}%</div>
                                                </div>
                                            @else
                                                <div class="progress progress-sm"
                                                     style="margin-bottom: 0; margin-top: 5px; height: 15px;">
                                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                                         aria-valuenow="{{ ($nota_asig = $tracking->calcularStatsAsignatura($asignatura->id,$tracking->convocatoria_id)["nota"] * 10) }}"
                                                         aria-valuemin="0" aria-valuemax="100"
                                                         style="width: {{ $tracking->calcularStatsAsignatura($asignatura->id,$tracking->convocatoria_id)["nota"] * 10}}%; padding-top: 2px; font-weight: 100">{{ $nota_asig }}%</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="row">
                                        <div class="col-xs-5"><span
                                                class="accomodation-title">{{ trans("elearning::asignaturas/front_lang.creditos") }}</span>
                                        </div>
                                        <div class="col-xs-6"><span
                                                class="accomodation-value custom-color-1">{{ $convocatoria->creditos }}</span>
                                        </div>
                                    </div>
                                </li>

                                <li>

                                    <div class="row">
                                        <div class="col-xs-5"><span
                                                class="accomodation-title">{{ trans("elearning::asignaturas/front_lang.fecha_cierre") }}</span>
                                        </div>
                                        <div class="col-xs-6"><span
                                                class="accomodation-value accomodation-title">{{ $convocatoria->fecha_fin_formatted }}</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
                <div class="call-to-action-btn">
                    @if(!empty($asignatura->titulo))
                        <a href="#" data-toggle="modal" data-target="#more_info"
                           class="btn btn-md btn-primary">{{ trans("elearning::asignaturas/front_lang.ver_info") }}</a>
                        @if(!empty($track_asignatura) && $track_asignatura->aprobado && $tracking->getInformacionConvocatoria($tracking->convocatoria_id)->certificado_id!='')
                            <a href="javascript:goToMod('{{ url("asignatura/".$asignatura->url_amigable."/".$asignatura->id."/generarCertificado")}}');"
                               class="btn btn-md btn-success">{{ trans("elearning::asignaturas/front_lang.certificado") }}</a>
                        @endif
                    @endif
                </div>
                <br>
            </section>

            @if($timeToFinishAssignatura > 0)
                <div >
                    @if($alertLevel<2)
                        <div class="col-sm-12">
                            <div class="row alert alert-danger">
                                <strong>{{ trans("elearning::asignaturas/front_lang.cont") }}
                                </strong><br>
                            </div>
                        </div>
                    @else
                        <div class="col-sm-12">
                            <div class="row alert alert-{{ $alertColor }}">
                                <strong >{{ trans('elearning::asignaturas/front_lang.fecha_fin_curso',
                                            [
                                                "DIAS" => $tiempo_restante->days,
                                                "HORAS" => $tiempo_restante->h,
                                                "MINUTOS" => $tiempo_restante->i
                                            ]) }}
                                </strong><br>
                            </div>
                        </div>
                    @endif

                </div>
            @endif

            <ul class="properties-listing sort-destination p-none row equalizeHeights">
                @foreach($asignatura->modulos()->activos()->orderBy('orden')->get() as $modulo)
                    <?php
                    $colorFondo = $modulo->fondo;
                    $classModulo = "background-color-primary";
                    $opacity = '1';
                    $url = "modulos/detalle_modulo/" . $modulo->url_amigable . "/" . $modulo->id;
                    $strNoContents = "";
                    $abierto = true;

                    if ($tracking->moduloIniciado($modulo)) {
                        $classModulo = "background-color-success";
                        $colorFondo = "";
                    }

                    if (!$tracking->moduloActivo($modulo, false) ||
                        empty($modulo->nombre)) {
                        $opacity = '0.7';
                        $url = "";
                        $abierto = false;
                    }

                    if ($modulo->contenidos()->activos()->count() == 0) {
                        $url = "";
                        $strNoContents = trans("elearning::asignaturas/front_lang.no_contenidos_activos");
                    }
                    ?>
                    <li class="col-md-4 col-sm-6 col-xs-12 p-md">

                        <div class="listing-item course-module" style="opacity: {{ $opacity }};">
                            @if($url!='')<a href="javascript:goToMod('{{ url($url)}}');" class="text-decoration-none">@endif
                            <span class="thumb-info thumb-info-lighten">

                                    @if($modulo->image!='')
                                    <span class="thumb-info-wrapper m-none">
                                        <img src="{{ url("modulos/openImage/".$modulo->id) }}"
                                             class="module-img img-responsive"
                                             alt="">
                                    </span>
                                    @endif

                                    @if($tracking->moduloCompleto($modulo))
                                        <div class="plan-ribbon-wrapper">
                                            <div class="plan-ribbon">
                                                {{ trans("elearning::asignaturas/front_lang.completado") }}
                                            </div>
                                        </div>
                                    @endif

                                <span class="thumb-info-price @if($colorFondo=='') {{ $classModulo }} @endif text-color-light text-lg p-sm pl-md pr-md" style="@if($colorFondo!='') background-color: {{$colorFondo}} !important;@endif">{{ $modulo->nombre }}<br></span>
                                <span class="custom-thumb-info-title b-normal p-lg" style="min-height: 180px;">
                                    <span class="thumb-info-inner text-md">{!! $modulo->descripcion !!}
                                        <div class="gradient-text-cover"></div>
                                    </span>

                                    <ul class="accommodations text-uppercase font-weight-bold p-none text-sm">
                                        <li>
                                            <span
                                                class="accomodation-title">{{ trans("elearning::asignaturas/front_lang.fecha_incio") }}</span>
                                            <span
                                                class="accomodation-value custom-color-1">{{ $tracking->getInformacionConvocatoria($tracking->convocatoria_id,$modulo->id)->fecha_inicio_formatted }}</span>
                                        </li>
                                    </ul>
                                    <ul class="accommodations text-uppercase font-weight-bold p-none text-sm">
                                        <li>
                                            <span
                                                class="accomodation-title">{{ trans("elearning::asignaturas/front_lang.fecha_fin") }}</span>
                                            <span
                                                class="accomodation-value custom-color-4">{{ $tracking->getInformacionConvocatoria($tracking->convocatoria_id,$modulo->id)->fecha_fin_formatted }}</span>
                                        </li>
                                    </ul>
                                        @if($modulo->coordinacion!='')
                                            <ul class="accommodations text-uppercase font-weight-bold p-none text-sm">
                                            <li>
                                                <span
                                                    class="accomodation-title">{{ trans("elearning::asignaturas/front_lang.coordinacion") }}</span>
                                                <span
                                                    class="accomodation-value custom-color-1">{{ $modulo->coordinacion }}</span>
                                            </li>
                                        </ul>
                                        @endif
                                        @if($tracking->moduloRequerido($modulo,false) === false)
                                            <ul class="accommodations text-uppercase font-weight-bold p-none text-sm">
                                            <li>
                                                <span
                                                    class="accomodation-title">{{ trans("elearning::asignaturas/front_lang.bloqueo") }}</span>
                                                <span
                                                    class="accomodation-value custom-color-1">{{ $tracking->getRequerido($modulo) }}</span>
                                            </li>
                                        </ul>
                                        @endif
                                </span>
                                    @if(empty($modulo->nombre))
                                        <div class="alert alert-warning" style="margin-bottom: 0;">
                                            <?php $traducidos = $modulo->getTraduccionesReales(); ?>
                                            {{ trans_choice("general/front_lang.no_lang", $traducidos->count()) }}:
                                            <strong>{{ $modulo->getTraduccionesReales()->implode("name", ", ") }}</strong>
                                        </div>
                                    @elseif($strNoContents!='')
                                        <div class="alert alert-warning"
                                             style="margin-bottom: 0;">{{ $strNoContents }}</div>
                                    @elseif($abierto)
                                        <div class="alert alert-success"
                                             style="margin-bottom: 0;">{{ trans("elearning::asignaturas/front_lang.abierto_cursar") }}</div>
                                    @else
                                        <div class="alert alert-success"
                                             style="margin-bottom: 0;">{{ trans("elearning::asignaturas/front_lang.cerrado_cursar") }}</div>
                                        @endif

                                        </span>
                                @if($url!='')</a>@endif
                        </div>
                    </li>
                @endforeach

            </ul>

        </div>
        <br clear="all">

        @if(config('elearning.basicos.foro'))
            @include("elearning::foro.front_inicio", ["url" => url("foro/" . $asignatura->id)])
        @endif
    </div>
@endsection

@section('foot_page')
    <script>
        function goToMod(url) {
            document.location = url;
        }
    </script>
@endsection
