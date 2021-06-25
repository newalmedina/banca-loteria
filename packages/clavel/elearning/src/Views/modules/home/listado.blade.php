@extends('front.layouts.default')

@section('title')
    @parent Inicio
@stop

@section('breadcrumb')
    <li><a href="{{ url("/") }}">{{ trans('elearning::inicio/front_lang.listado') }}</a></li>
@stop

@section('content')

    <div class="container">

        <div class="col-md-3 col-md-push-9">

            <div class="list-group">
                @foreach($cursos_categoria as $curso)
                    <!--active-->
                    <a href="#" class="list-group-item"><span class="badge">{{ $curso->asignaturaPivot()->count() }}</span>{{ $curso->nombre }}</a>
                @endforeach
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <p>{{ trans("elearning::inicio/front_lang.my_courses") }}</p>
                    <div style="text-align: center;">
                        <button type="button" class="btn btn-success">{{ trans("elearning::inicio/front_lang.mis_asignaturas") }}</button>
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
                            <div class="thumbnail">
                                <img style="height: 100px; width: 100%; display: block;" src="{{ url("inicio/getAnnex/".$asignatura->id) }}" alt="">
                                <div class="caption">
                                    <h3>{{ $asignatura->titulo }}</h3>
                                    <hr>
                                    <p>{!! $asignatura->breve !!}</p>
                                    <p>
                                        <a href="{{ url('asignaturas/cursos/detalle/'.$asignatura->url_amigable) }}" class="btn btn-primary" role="button">{{ trans("elearning::inicio/front_lang.mostrar") }}</a>
                                    </p>
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
                            <strong>{{ trans("elearning::inicio/front_lang.Attencion") }}</strong> {{ trans("elearning::inicio/front_lang.no_asignaturas") }}
                        </div>
                    </div>
                </div>

            @endif

        </div>

    </div>

    <br clear="all">

@endsection
