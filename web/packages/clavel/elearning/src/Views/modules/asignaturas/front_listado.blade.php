@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')



    <div class="container">

        <div class="col-md-3 col-md-push-9">

            <div class="list-group">
                <a href="{{ url("asignaturas") }}" class="list-group-item @if(empty($filtro)) active @endif">
                    <span class="badge">{{ $total_asignaturas }}</span>
                    {{ trans("elearning::asignaturas/front_lang.todos_los_cursos") }}
                </a>
                @foreach($cursos_categoria as $curso)
                    <a href="{{ url("asignaturas/".$curso->id) }}" class="list-group-item @if($filtro==$curso->id) active @endif">
                        <span class="badge">{{ $curso->asignaturaPivot()->active()->count() }}</span>
                        <span class="font-size-xs">{{ $curso->nombre }}</span>
                    </a>
                @endforeach
            </div>
            @if(!empty($filtro) && $curso->checkCertificado())
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>{{ trans("elearning::asignaturas/front_lang.course_certificate") }}</p>
                        <div style="text-align: center;">
                            <a href="{{ url("/curso/$filtro/certificado-curso") }}"
                               class="btn btn-info">{!! trans("elearning::asignaturas/front_lang.certificado") !!}</a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-body font-size-xs">
                    <p>{{ trans("elearning::asignaturas/front_lang.my_courses") }}</p>
                    <div style="text-align: center;">
                        <a href="{{ url("mis-asignaturas") }}" class="btn btn-info">{{ trans("elearning::asignaturas/front_lang.mis_asignaturas") }}</a>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-9 col-md-pull-3">

            @if($asignaturas->count()>0)

                <div class="bs-example" data-example-id="thumbnails-with-custom-content">
                    <?php
                    $nX = 0;
                    ?>
                    @foreach($asignaturas as $asignatura)
                        @if($nX%3==0)
                            @if($nX>0)
                                </div>
                            @endif
                            <div class="row">
                        @endif
                        <div class="col-sm-6 col-md-4">
                            <div class="thumbnail box-shadow-custom" STYLE="overflow: hidden">

                                @if($asignatura->image!='')
                                    <img style="height: 150px; width: 100%; display: block;" src="{{ url("asignaturas/openImage/".$asignatura->id) }}" alt="">
                                @else
                                    <div style="height: 150px; width: 100%; display: block; background-color: #F4F4F4; text-align: center">
                                        <i class="fa fa-laptop" style="font-size: 64px; margin-top: 50px;" aria-hidden="true"></i>
                                    </div>
                                @endif

                                @if(!$asignatura->getActiva()["activa"])
                                    <div class="plan-ribbon-wrapper ">
                                        <div class="plan-ribbon plan-ribbon-danger">
                                            {{ trans("elearning::asignaturas/front_lang.cerrado") }}
                                        </div>
                                    </div>
                                @endif

                                <div class="caption">
                                    <h4 class="text-headline margin-v-0-10">{{ $asignatura->titulo }}</h4>
                                    <div class="p-box">
                                        {!! $asignatura->breve !!}
                                        <div class="gradient-text-cover"></div>
                                    </div>
                                    <hr>
                                    <div class="row" style="margin-top: 25px;">
                                        <div class="col-md-5">
                                            <a href="{{ url('asignaturas/detalle/'.$asignatura->url_amigable."/".$asignatura->id) }}" class="btn btn-primary" role="button">
                                                {{ trans("elearning::asignaturas/front_lang.mostrar") }}
                                            </a>
                                        </div>
                                        @if($asignatura->modulos()->activos()->count()==0)
                                            <div class="col-md-7 text-warning" style="font-size: 13px;">{{ trans("elearning::asignaturas/front_lang.no_disponible") }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $nX++;
                        ?>
                    @endforeach
                </div>
                @if($nX>0)
                    </div>
                @endif

                <div class="pull-right">
                    {!! $asignaturas->render() !!}
                </div>

            @else

                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-danger" role="alert">
                            <strong>{{ trans("elearning::asignaturas/front_lang.Attencion") }}</strong> {{ trans("elearning::asignaturas/front_lang.no_asignaturas") }}
                        </div>
                    </div>
                </div>

            @endif

        </div>

    </div>

    <br clear="all">

@endsection
