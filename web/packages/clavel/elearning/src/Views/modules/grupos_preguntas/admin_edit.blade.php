@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    @parent

    <link href="{{ asset("/assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}" rel="stylesheet" type="text/css" />
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
    <li><a href="{{ url("admin/asignaturas/".$contenido->modulo->asignatura->id."/modulos/") }}">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$contenido->modulo->asignatura->{"titulo:es"} }}</a></li>
    <li><a href="{{ url("admin/modulos/".$contenido->modulo_id."/contenidos/") }}">{{ trans('elearning::modulos/admin_lang.contenidos')." ".$contenido->modulo->{"nombre:es"} }}</a></li>
    <li class="active"><a href="{{ url("admin/contenidos/".$contenido->id."/preguntas/") }}">{{ trans('elearning::grupos_preguntas/admin_lang.titulo')." ".$contenido->nombre}}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::model($grupoPegunta, $form_data, array('role' => 'form')) !!}

        {!! Form::hidden('contenido_id', $contenido->id, array('id' => 'modulo_id')) !!}
        {!! Form::hidden('save_and_new', false, array('id' => 'save_and_new')) !!}
         <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("elearning::grupos_preguntas/admin_lang.grupos") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('titulo', trans('elearning::grupos_preguntas/admin_lang.nombre'), array('class' => 'col-md-2 control-label')) !!}
                        <div class="col-md-4">
                            {!! Form::text('titulo',$grupoPegunta->titulo,array('class'=>'form-control col-sm-10','id'=>'titulo','placeholder'=>trans('elearning::grupos_preguntas/admin_lang.nombre'))) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('color', trans('elearning::grupos_preguntas/admin_lang.color'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-4">
                            <div class="input-group my-colorpicker2 colorpicker-element">
                                {!! Form::text('color', null, array('placeholder' => trans('elearning::grupos_preguntas/admin_lang.color'), 'class' => 'form-control', 'id' => 'color')) !!}

                                <div class="input-group-addon">
                                    <em style="background-color: rgb(136, 119, 119);"></em>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-solid">
                <div class="box-footer">
                    <a href="{{ url('/admin/contenidos/'.$contenido->id."/grupos_preguntas/") }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
                    <button type="button" class="btn btn-warning pull-right" style="margin-right: 10px;"
                            onclick="saveAndNew()">
                        {{ trans('general/admin_lang.save_and_new') }}
                    </button>
                </div>
            </div>
         </div>


        {!! Form::close() !!}
    </div>
@endsection

@section('foot_page')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/assets/admin/vendor/colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>

    <script>
        $(document).ready(function() {
            $(".my-colorpicker2").colorpicker();
        });

        function saveAndNew() {
            var formData = document.getElementById('formData');
            var save_and_new = document.getElementById('save_and_new');
            save_and_new.value = true;

            formData.submit();
        }
    </script>
    {!! JsValidator::formRequest('Clavel\Elearning\Requests\GrupoPreguntaRequest')->selector('#formData') !!}

@stop
