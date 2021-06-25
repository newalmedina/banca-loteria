@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop


@section('head_page')
    @parent
    <link href="{{ asset("/assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}" rel="stylesheet" type="text/css" />
@stop


@section('breadcrumb')
    <li><a href="{{ url("admin/customers") }}">{{ trans('timetracker::customers/admin_lang.list') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::model($customer, $form_data, array('role' => 'form')) !!}

            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('name', trans('timetracker::customers/admin_lang.name'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('name', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.name'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'name')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('code', trans('timetracker::customers/admin_lang.code'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('code', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.code'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'code')) !!}

                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('tax_id', trans('timetracker::customers/admin_lang.tax_id'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('tax_id', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.tax_id'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'tax_id')) !!}

                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('description', trans('timetracker::customers/admin_lang.description'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::textarea('description', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.description'),
                                    'class' => 'form-control textarea',
                                    'rows' => 3,
                                    'id' => 'description')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('company', trans('timetracker::customers/admin_lang.company'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('company', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.company'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'company')) !!}

                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('contact', trans('timetracker::customers/admin_lang.contact'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('contact', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.contact'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'contact')) !!}

                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('address', trans('timetracker::customers/admin_lang.address'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::textarea('address', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.address'),
                                    'class' => 'form-control textarea',
                                    'rows' => 2,
                                    'id' => 'address')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('country', trans('timetracker::customers/admin_lang.country'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('country', $country_list, !empty($customer->country) ? $customer->country : null , ['id'=>'country', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('currency', trans('timetracker::customers/admin_lang.currency'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('currency', $currency_list, !empty($customer->currency) ? $customer->currency : null , ['id'=>'currency', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('phone', trans('timetracker::customers/admin_lang.phone'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
                                    {!! Form::text('phone', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.phone'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'phone')) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('fax', trans('timetracker::customers/admin_lang.fax'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></span>
                                    {!! Form::text('fax', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.fax'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'fax')) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('mobile', trans('timetracker::customers/admin_lang.mobile'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-mobile" aria-hidden="true"></i></span>
                                    {!! Form::text('mobile', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.mobile'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'mobile')) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('email', trans('timetracker::customers/admin_lang.email'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-at" aria-hidden="true"></i></span>
                                    {!! Form::text('email', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.email'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'email')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('homepage', trans('timetracker::customers/admin_lang.homepage'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-home" aria-hidden="true"></i></span>
                                    {!! Form::text('homepage', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.homepage'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'homepage')) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('timezone', trans('timetracker::customers/admin_lang.timezone'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('timezone', $timezone_list, !empty($customer->timezone) ? $customer->timezone : null , ['id'=>'timezone', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('color', trans('timetracker::customers/admin_lang.color'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <div class="input-group my-colorpicker2 colorpicker-element">
                                    {!! Form::text('color', null, array('placeholder' => trans('timetracker::customers/admin_lang.color'), 'class' => 'form-control', 'id' => 'color')) !!}

                                    <div class="input-group-addon">
                                        <em style="background-color: rgb(136, 119, 119);"></em>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('fixed_rate', trans('timetracker::customers/admin_lang.fixed_rate'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">

                                    {!! Form::text('fixed_rate', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.fixed_rate'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'fixed_rate')) !!}
                                    <span class="input-group-addon"><i class="fa fa-euro" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('hourly_rate', trans('timetracker::customers/admin_lang.hourly_rate'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">

                                    {!! Form::text('hourly_rate', null,
                                    array('placeholder' => trans('timetracker::customers/admin_lang.hourly_rate'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'hourly_rate')) !!}
                                    <span class="input-group-addon"><i class="fa fa-euro" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('active', trans('timetracker::customers/admin_lang.active'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
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

                        <a href="{{ url('/admin/customers') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
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

    {!! JsValidator::formRequest('Clavel\TimeTracker\Requests\CustomerRequest')->selector('#formData') !!}
@stop
