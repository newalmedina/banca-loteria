@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li><a href="{{ url("/asignaturas") }}">{{ trans("elearning::asignaturas/front_lang.listado") }}</a></li>
    <li>
        <a href="{{ url("/asignaturas/contenido/".$modulo->asignatura->url_amigable."/".$modulo->asignatura_id) }}">{{ $modulo->asignatura->titulo }}</a>
    </li>
    <li class="active">{{ $page_title }}</li>
@stop

@section("head_page")
    <style>
        .list-group-item {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@stop

@section('content')
    <?php $asignatura = $modulo->asignatura; ?>
    @include("front.includes.modal_curso", compact("asignatura"))

    <div class="container menuModulos">
        <div class="row">
            <div class="col-md-3 col-md-push-9">
                <div >
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
                                        <strong >{{ trans('elearning::contenidos/front_lang.fecha_fin_curso',
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
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h5>{{ trans("elearning::modulos/front_lang.progreso") }}</h5>
                        <?php
                            $petc = number_format((count($completados) * 100) / $modulo->contenidos()->activos()->where("tipo_contenido_id", "<>", 1)->count(), 2);
                        ?>
                        <div class="circular-bar circular-bar-sm">
                            <div class="circular-bar-chart" data-percent="{{ $petc }}"
                                 data-plugin-options="{'size': 85, 'lineWidth': 3, 'barColor': '#0088CC'}">
                                <label class="text-primary font-weight-bold">{{ $petc }}%</label>
                            </div>
                        </div>

                        <h5>{{ trans("elearning::modulos/front_lang.informacion") }}</h5>

                        <dl>
                            <dt>{{ trans("elearning::modulos/front_lang.convocatoria") }}</dt>
                            <dd>{{ $modulo->convocatoria_posible->fecha_inicio_formatted }}
                                - {{ $modulo->convocatoria_posible->fecha_fin_formatted }}</dd>
                            <dt>{{ trans("elearning::modulos/front_lang.porcentaje") }}</dt>
                            <dd>{{ $modulo->convocatoria_posible->porcentaje }}%</dd>
                            @if($modulo->coordinacion!='')
                                <dt>{{ trans("elearning::modulos/front_lang.coordinacion") }}</dt>
                                <dd>{{ $modulo->coordinacion }}</dd>
                            @endif
                        </dl>
                        <div style="text-align: center;">
                            <button type="button" id="goback"
                                    class="btn btn-primary">{{ trans("elearning::modulos/front_lang.volver") }}</button>
                        </div>
                    </div>
                    @if(!empty($modulo->titulo))
                        <div class="panel-footer">
                            <div class="row">
                                <div class="col-xs-2"><a href="#" data-toggle="modal" data-target="#more_info"
                                                         class="btn btn-info"><i class="fa fa-info" aria-hidden="true"></i> + Info</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-9 col-md-pull-3">
                @if($modulo->image!='')
                    <img style="width: 100%; display: block;" src="{{ url("modulos/openImage/".$modulo->id) }}" alt="">
                @else
                    <div
                        style="height: 150px; width: 100%; display: block; background-color: #F4F4F4; text-align: center">
                        <i class="fa fa-laptop" style="font-size: 64px; margin-top: 50px;" aria-hidden="true"></i>
                    </div>
                @endif
                <br clear="all">
                @if(!empty($modulo->nombre))
                    <h3>{{ $modulo->nombre }}</h3>
                    {!! $modulo->descripcion !!}
                @else
                    <div class="alert alert-warning">
                        <?php $traducidos = $modulo->getTraduccionesReales(); ?>
                        {{ trans_choice("general/front_lang.no_lang", $traducidos->count()) }}:
                        <strong>{{ $modulo->getTraduccionesReales()->implode("name", ", ") }}</strong>
                    </div>
                @endif
                <br clear="all">

                <p>{{ trans("elearning::modulos/front_lang.listado_de_asignaturas") }}</p>

                @if($modulo->contenidos()->activos()->count()>0)
                    <div class="panel-body list-group" style="padding: 0px;">
                        @foreach($modulo->contenidos()->activos()->orderBy('lft')->get() as $contenido)
                            <?php
                            $output = "";
                            for ($i = 0; $i < $contenido->depth; $i++) {
                                $output .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            }
                            ?>
                            <p class="list-group-item">
                                @if($contenido->tipo->slug!='tema')
                                    <span class="badge" style="background-color: transparent; color:#777">
                                        @if(isset($completados[$contenido->id]))
                                            <i class="fa fa-check-circle-o pull-right text-success" aria-hidden="true"
                                               style="font-size: 18px;"></i>
                                        @else
                                            <i class="fa fa-circle pull-right" style="font-size: 18px;" aria-hidden="true"></i>
                                        @endif
                                    </span>
                                @endif
                                {!! $output !!}
                                @if($contenido->tipo->slug!='tema')<a
                                    href="{{ url('contenido/detalle-contenido/'.$contenido->getTranslatedURL().'/'.$contenido->id) }}">@endif
                                    {{ $contenido->nombre }}
                                    @if(empty($contenido->nombre))
                                        <?php $traducidos = $contenido->getTraduccionesReales(); ?>
                                        {{ trans_choice("general/front_lang.no_lang", $traducidos->count()) }}:
                                        <strong>{{ $contenido->getTraduccionesReales()->implode("name", ", ") }}</strong>
                                    @endif
                                    @if($contenido->tipo->slug!='tema')</a>@endif
                                @if($contenido->obligatorio)
                                    <span
                                        class="label label-info label-sm">{{ trans("elearning::modulos/front_lang.obligaotrio") }}</span>
                                @endif
                            </p>
                        @endforeach
                    </div>
                @endif
                <br clear="all">
            </div>
        </div>
    </div>

@endsection


@section('foot_page')
    <script>
        $(document).ready(function () {
            $("#goback").click(function (e) {
                e.preventDefault();
                window.location = "{{ url('asignaturas/contenido/'.$modulo->asignatura->url_amigable.'/'.$modulo->asignatura->id) }}";
            });
        });
    </script>

@endsection
