@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    @parent
    <link href="{{ asset("/assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}" rel="stylesheet" type="text/css" />
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/activities") }}">{{ trans('timetracker::activities/admin_lang.list') }}</a></li>
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
        {!! Form::model($activity, $form_data, array('role' => 'form')) !!}

            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('name', trans('timetracker::activities/admin_lang.name'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('name', null,
                                    array('placeholder' => trans('timetracker::activities/admin_lang.name'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'name')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('description', trans('timetracker::activities/admin_lang.description'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::textarea('description', null,
                                    array('placeholder' => trans('timetracker::activities/admin_lang.description'),
                                    'class' => 'form-control textarea',
                                    'rows' => 3,
                                    'id' => 'description')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('fixed_rate', trans('timetracker::activities/admin_lang.fixed_rate'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">

                                    {!! Form::text('fixed_rate', null,
                                    array('placeholder' => trans('timetracker::activities/admin_lang.fixed_rate'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'fixed_rate')) !!}
                                    <span class="input-group-addon"><i class="fa fa-euro" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('hourly_rate', trans('timetracker::activities/admin_lang.hourly_rate'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">

                                    {!! Form::text('hourly_rate', null,
                                    array('placeholder' => trans('timetracker::activities/admin_lang.hourly_rate'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'hourly_rate')) !!}
                                    <span class="input-group-addon"><i class="fa fa-euro" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('color', trans('timetracker::activities/admin_lang.color'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <div class="input-group my-colorpicker2 colorpicker-element">
                                    {!! Form::text('color', null, array('placeholder' => trans('timetracker::activities/admin_lang.color'), 'class' => 'form-control', 'id' => 'color')) !!}

                                    <div class="input-group-addon">
                                        <em style="background-color: rgb(136, 119, 119);"></em>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('active', trans('timetracker::activities/admin_lang.active'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
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
                    </div>
                </div>



                <div class="box box-solid">

                    <div class="box-footer">

                        <a href="{{ url('/admin/activities') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

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
    </script>

    {!! JsValidator::formRequest('Clavel\TimeTracker\Requests\ActivityRequest')->selector('#formData') !!}
@stop
