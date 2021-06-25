@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    @parent
    <style>
        #bs-modal-images, #bs-modal-code {
            z-index: 99999999;
        }

        .select2-container--default .select2-selection--multiple {
            height: auto !important;
        }
    </style>
@stop

@section('breadcrumb')

    <li><a href="{{ url("admin/asignaturas/") }}">{{ trans('elearning::asignaturas/admin_lang.asignaturas') }}</a></li>
    <li><a href="{{ url("admin/asignaturas/".$pregunta->contenido->modulo->asignatura->id."/modulos/") }}">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$pregunta->contenido->modulo->asignatura->{"titulo:es"} }}</a></li>
    <li><a href="{{ url("admin/modulos/".$pregunta->contenido->modulo_id."/contenidos/") }}">{{ trans('elearning::contenidos/admin_lang.contenidos')." ".$pregunta->contenido->modulo->{"nombre:es"} }}</a></li>
    <li><a href="{{ url("admin/contenidos/".$pregunta->contenido->id."/preguntas/") }}">{{ trans('elearning::contenidos/admin_lang.questions')." ".$pregunta->contenido->nombre}}</a></li>
    <li><a href="{{ url("admin/preguntas/".$pregunta->id."/respuestas/") }}">{{ trans('elearning::preguntas/admin_lang.respuestas')." ".substr(strip_tags($pregunta->nombre),0,10)}}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::model($respuesta, $form_data, array('role' => 'form')) !!}
            {!! Form::hidden('pregunta_id', $pregunta->id, array('id' => 'pregunta_id')) !!}
            {!! Form::hidden('correcta', true, array('id' => 'correcta')) !!}
            {!! Form::hidden('activa', true, array('id' => 'activa')) !!}
             <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">
                        <div class="form-group">
                            {!! Form::label('puntos_correcta', trans('elearning::preguntas/admin_lang.puntos_correcta'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-2">
                                {!! Form::text('puntos_correcta',$respuesta->puntos_correcta,array('class'=>'form-control col-sm-6','id'=>'orden','placeholder'=>trans('elearning::preguntas/admin_lang.puntos_correcta'))) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('puntos_incorrecta', trans('elearning::preguntas/admin_lang.puntos_incorrecta'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-2">
                                {!! Form::text('puntos_incorrecta',$respuesta->puntos_incorrecta,array('class'=>'form-control col-sm-6','id'=>'orden','placeholder'=>trans('elearning::preguntas/admin_lang.puntos_incorrecta'))) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('orden', trans('elearning::preguntas/admin_lang.orden'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-2">
                                {!! Form::text('orden',$respuesta->orden,array('class'=>'form-control col-sm-6','id'=>'orden','placeholder'=>trans('elearning::preguntas/admin_lang.orden'))) !!}
                            </div>
                        </div>
                    </div>
                </div>

                 @foreach ($a_trans as $key => $valor)
                     {!! Form::hidden('userlang['.$key.'][id]', $valor["id"], array('id' => 'id')) !!}
                     {!! Form::hidden('userlang['.$key.'][respuesta_id]', $respuesta->id, array('id' => 'pregunta_id')) !!}
                     {!! Form::hidden('userlang['.$key.'][nombre]', $respuesta->{'nombre:'.$key} , array('id' => 'nombre_'.$key)) !!}
                     {!! Form::hidden('userlang['.$key.'][comentario]', $respuesta->{'comentario:'.$key} , array('id' => 'comentario_'.$key)) !!}
                 @endforeach

                <div class="box box-solid">
                    <div class="box-footer">
                        <a href="{{ url('/admin/contenidos/'.$pregunta->contenido_id."/preguntas/") }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
                    </div>
                </div>
             </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('foot_page')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script>
        $(document).ready(function() {

        });

    </script>
    {!! JsValidator::formRequest('Clavel\Elearning\Requests\PreguntaRequest')->selector('#formData') !!}
@stop
