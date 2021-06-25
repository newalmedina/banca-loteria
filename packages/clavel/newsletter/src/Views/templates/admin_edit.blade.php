@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/templates") }}">{{ trans('Newsletter::admin_lang_template.templates') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop


@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::model($templates, $form_data, array('role' => 'form')) !!}
            {!! Form::hidden('id', null, array('id' => 'id')) !!}

            <div class="col-xs-12">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ $page_title }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('slug', trans('Newsletter::admin_lang_template.slug'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                {!! Form::text('slug', null, array('placeholder' => trans('Newsletter::admin_lang_template.slug'), 'class' => 'form-control', 'id' => 'slug', 'readonly' => true)) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('nombre', trans('Newsletter::admin_lang_template.nombre'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('nombre', null, array('placeholder' => trans('Newsletter::admin_lang_template.nombre'), 'class' => 'form-control', 'maxlength' => '255')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('active', trans('Newsletter::admin_lang_template.active'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('active', 0, true, array('id'=>'active_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('active', 1, false, array('id'=>'active_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer">

                        <a href="{{ url('/admin/templates') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                    </div>

                </div>

            </div>

        {!! Form::close() !!}
    </div>

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    {!! JsValidator::formRequest('App\Modules\Newsletter\Requests\AdminTemplatesRequest')->selector('#formData') !!}
@stop