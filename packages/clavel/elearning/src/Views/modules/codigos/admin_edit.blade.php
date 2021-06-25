@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/codigos") }}">{{ trans('elearning::codigos/admin_lang.codigos') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::model($codigos, $form_data, array('role' => 'form')) !!}
        {!! Form::hidden("id", $codigos->id) !!}

            <div class="col-md-12">

                @if($codigos->id=='')
                    <div class="row">
                        <div class="col-md-5">

                            <div class="box box-primary">
                                <div class="box-header  with-border"><h3 class="box-title">{{ trans("elearning::codigos/admin_lang.set_one_code") }}</h3></div>
                                <div class="box-body">
                                    <p style="padding-bottom: 20px;" class="text-warning">{{ trans("elearning::codigos/admin_lang.text_info_unic_code") }}</p>
                                    <div class="form-group">
                                        {!! Form::label('codigo', trans('elearning::codigos/admin_lang.codigo'), array('class' => 'col-sm-2 control-label')) !!}
                                        <div class="col-sm-10">
                                            {!! Form::text('codigo', null, array('placeholder' => trans('elearning::codigos/admin_lang.codigo'), 'class' => 'form-control', 'id'=>'codigo')) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                @endif

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        @if($codigos->id!='')
                            <div class="form-group">
                                {!! Form::label('codigo', trans('elearning::codigos/admin_lang.codigo'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('codigo', null, array('placeholder' => trans('elearning::codigos/admin_lang.codigo'), 'class' => 'form-control', 'nombre')) !!}
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            {!! Form::label('roles', trans('elearning::codigos/admin_lang.roles'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <select class="form-control select2" name="sel_roles[]" multiple="multiple" data-placeholder="{{ trans('elearning::codigos/admin_lang.roles') }}" style="width: 100%;">
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}" @if($codigos->rolSelected($rol->id)) selected @endif>{{ $rol->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('roles', trans('elearning::codigos/admin_lang.asignaturas'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <select class="form-control select2" name="sel_asignaturas[]" multiple="multiple" data-placeholder="{{ trans('elearning::codigos/admin_lang.asignaturas') }}" style="width: 100%;">
                                    @foreach($asignaturas as $asignatura)
                                        <option value="{{ $asignatura->id }}" @if($codigos->asignaturaSelected($asignatura->id)) selected @endif>{{ $asignatura->titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('active', trans('elearning::codigos/admin_lang.active'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('active', '0', true, array('id'=>'active_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('active', '1', false, array('id'=>'active_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('activo', trans('elearning::codigos/admin_lang.ilimitado'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('ilimitado', '0', true, array('id'=>'ilimitado_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('ilimitado', '1', false, array('id'=>'ilimitado_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="box box-solid">

                    <div class="box-footer">

                        <a href="{{ url('/admin/codigos') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
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

    {!! JsValidator::formRequest('Clavel\Elearning\Requests\CodigosRequest')->selector('#formData') !!}
@stop
