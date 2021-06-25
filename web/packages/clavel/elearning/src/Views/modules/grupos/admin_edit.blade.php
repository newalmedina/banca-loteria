@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/grupos") }}">{{ trans('elearning::grupos/admin_lang.grupos') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::model($grupo, $form_data, array('role' => 'form')) !!}

            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('activo', trans('elearning::grupos/admin_lang.activo'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
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
                            {!! Form::label('nombre', trans('elearning::grupos/admin_lang.nombre'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('nombre', null, array('placeholder' => trans('elearning::grupos/admin_lang.nombre'), 'class' => 'form-control', 'nombre')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('codigo', trans('elearning::grupos/admin_lang.codigo'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('codigo', null, array('placeholder' => trans('elearning::grupos/admin_lang.codigo'), 'class' => 'form-control', 'nombre')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('sel_users', trans('elearning::grupos/admin_lang.usuarios'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <select class="form-control select2" name="sel_users[]" multiple="multiple" data-placeholder="{{ trans('elearning::grupos/admin_lang.usuarios') }}" style="width: 100%;">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"  @if($grupo->userSelected($user->id)) selected @endif>({{ $user->id }}) {{ $user->userProfile->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('sel_profesores', trans('elearning::grupos/admin_lang.profesores'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <select class="form-control select2" name="sel_profesores[]" multiple="multiple" data-placeholder="{{ trans('elearning::grupos/admin_lang.profesores') }}" style="width: 100%;">
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->id }}"  @if($grupo->profesorSelected($profesor->id)) selected @endif>({{ $profesor->id }}) {{ $profesor->userProfile->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="box box-solid">

                    <div class="box-footer">

                        <a href="{{ url('/admin/grupos') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
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

    {!! JsValidator::formRequest('Clavel\Elearning\Requests\GruposRequest')->selector('#formData') !!}
@stop
