@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li class="active">{{ trans('timetracker::config/admin_lang.title') }}</li>
@stop

@section('content')

    @include('admin.includes.modals')
    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::model($config, $form_data, array('role' => 'form')) !!}
        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("timetracker::config/admin_lang.numeracion") }}</h3></div>
                <div class="box-body">

                    <div class="form-group">
                        {!! Form::label('budget_prefix', trans('timetracker::config/admin_lang.budget_prefix'), array('class' => 'col-sm-2 control-label required-input')) !!}
                        <div class="col-sm-10">
                            {!! Form::text('budget_prefix', null,
                                array('placeholder' => trans('timetracker::config/admin_lang.budget_prefix'),
                                'class' => 'form-control input-xlarge',
                                'id' => 'budget_prefix')) !!}

                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('budget_counter', trans('timetracker::config/admin_lang.budget_counter'), array('class' => 'col-sm-2 control-label required-input')) !!}
                        <div class="col-sm-10">
                            {!! Form::text('budget_counter', null,
                                array('placeholder' => trans('timetracker::config/admin_lang.budget_counter'),
                                'class' => 'form-control input-xlarge',
                                'id' => 'budget_counter')) !!}

                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('budget_digits', trans('timetracker::config/admin_lang.budget_digits'), array('class' => 'col-sm-2 control-label required-input')) !!}
                        <div class="col-sm-10">
                            {!! Form::text('budget_digits', null,
                                array('placeholder' => trans('timetracker::config/admin_lang.budget_digits'),
                                'class' => 'form-control input-xlarge',
                                'id' => 'budget_digits')) !!}

                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('order_prefix', trans('timetracker::config/admin_lang.order_prefix'), array('class' => 'col-sm-2 control-label required-input')) !!}
                        <div class="col-sm-10">
                            {!! Form::text('order_prefix', null,
                                array('placeholder' => trans('timetracker::config/admin_lang.order_prefix'),
                                'class' => 'form-control input-xlarge',
                                'id' => 'order_prefix')) !!}

                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('order_counter', trans('timetracker::config/admin_lang.order_counter'), array('class' => 'col-sm-2 control-label required-input')) !!}
                        <div class="col-sm-10">
                            {!! Form::text('order_counter', null,
                                array('placeholder' => trans('timetracker::config/admin_lang.order_counter'),
                                'class' => 'form-control input-xlarge',
                                'id' => 'order_counter')) !!}

                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('order_digits', trans('timetracker::config/admin_lang.order_digits'), array('class' => 'col-sm-2 control-label required-input')) !!}
                        <div class="col-sm-10">
                            {!! Form::text('order_digits', null,
                                array('placeholder' => trans('timetracker::config/admin_lang.order_digits'),
                                'class' => 'form-control input-xlarge',
                                'id' => 'order_digits')) !!}

                        </div>
                    </div>

                </div>
            </div>

            <div class="box box-solid">

                <div class="box-footer">

                    <a href="{{ url('/admin/timetracker-config') }}" class="btn btn-default">{{ trans('timetracker::config/admin_lang.cancelar') }}</a>
                    <button name="save" type="submit" class="btn btn-info pull-right">{{ trans('timetracker::config/admin_lang.save') }}</button>

                </div>

            </div>

        </div>

        {!! Form::close() !!}
    </div>
@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    {!! JsValidator::formRequest('Clavel\TimeTracker\Requests\ConfigRequest')->selector('#formData') !!}

@stop