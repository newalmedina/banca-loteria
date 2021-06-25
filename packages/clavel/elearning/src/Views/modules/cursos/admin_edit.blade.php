@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/cursos") }}">{{ trans('elearning::cursos/admin_lang.cursos') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @if (Session::get('success',"") != "")
        <div class="alert alert-success">
            <button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button>
            <strong>{{ date('d/m/Y H:i:s') }}</strong>
            {{ Session::get('success',"") }}
        </div>
    @endif

    <div class="row">
        {!! Form::model($curso, $form_data, array('role' => 'form')) !!}

            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('activo', trans('elearning::cursos/admin_lang.activo'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('activo', '0', true, array('id'=>'activo_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('activo', '1', false, array('id'=>'activo_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('sel_asignaturas', trans('elearning::cursos/admin_lang.asignaturas'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <select class="form-control select2" name="sel_asignaturas[]" multiple="multiple" data-placeholder="{{ trans('elearning::cursos/admin_lang.asignaturas') }}" style="width: 100%;">
                                    @foreach($asignaturas as $asignatura)
                                        <option value="{{ $asignatura->id }}" @if($curso->asignaturaSelected($asignatura->id)) selected @endif>{{ $asignatura->titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('certificado_id', trans('elearning::cursos/admin_lang.certificado_id'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-md-10">
                                <select name="certificado_id" class="form-control">
                                    <option value="">{{ trans("elearning::cursos/admin_lang.sin_certificado") }}</option>
                                    @foreach($certificados as $certificado)
                                        <option value="{{ $certificado->id }}" @if($certificado->id==$curso->certificado_id) selected @endif>{{ $certificado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">

                        <?php
                        $nX = 1;
                        ?>
                        @foreach ($a_trans as $key => $valor)
                            <li @if($nX==1) class="active" @endif>
                                <a href="#tab_{{ $key }}" data-toggle="tab">
                                    {{ $valor["idioma"] }}
                                    @if($nX==1)- <span class="text-success">{{ trans('general/admin_lang._defecto') }}</span>@endif
                                </a>
                            </li>
                            <?php
                            $nX++;
                            ?>
                        @endforeach

                    </ul><!-- /.box-header -->

                    <div class="tab-content">
                        <?php
                        $nX = 1;
                        ?>
                        @foreach ($a_trans as $key => $valor)
                            <div id="tab_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                                {!!  Form::hidden('userlang['.$key.'][id]', $valor["id"], array('id' => 'id')) !!}
                                {!!  Form::hidden('userlang['.$key.'][curso_id]', $curso->id, array('id' => 'curso_id')) !!}

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][nombre]', trans('elearning::cursos/admin_lang.nombre'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('userlang['.$key.'][nombre]', $curso->{'nombre:'.$key} , array('placeholder' => trans('elearning::cursos/admin_lang.nombre'), 'class' => 'form-control textarea', 'id' => 'nombre_'.$key)) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][url_amigable]', trans('elearning::cursos/admin_lang.url_amigable'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <span class="input-group-addon">{{ url("/") }}/</span>
                                            {!! Form::text('userlang['.$key.'][url_amigable]', "cursos/".$curso->{'url_amigable:'.$key} , array('placeholder' => trans('elearning::cursos/admin_lang._INSERTAR_url_amigable'), 'class' => 'form-control textarea', 'readonly' => true, 'id' => 'url_amigable_'.$key)) !!}
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <?php
                            $nX++;
                            ?>
                        @endforeach
                    </div>
                </div>


                <div class="box box-solid">

                    <div class="box-footer">

                        <a href="{{ url('/admin/cursos') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
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
            $(".select2").select2();
        });
    </script>

    {!! JsValidator::formRequest('Clavel\Elearning\Requests\CursosRequest')->selector('#formData') !!}
@stop
