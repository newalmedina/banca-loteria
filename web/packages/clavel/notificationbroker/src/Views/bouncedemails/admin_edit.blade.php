@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/bouncedemails") }}">{{ trans('notificationbroker::bouncedemails/admin_lang.bouncedemail') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')


    @include('admin.includes.errors')
    @include('admin.includes.success')


    <div class="row">
        {!! Form::model($bouncedemail, $form_data, array('role' => 'form')) !!}

        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("notificationbroker::bouncedemails/admin_lang.info_menu") }}</h3></div>
                <div class="box-body">
                    {{-- Email - email --}}
<div class="form-group">
    {!! Form::label('email', trans('notificationbroker::bouncedemails/admin_lang.fields.email'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </span>
            {!! Form::text('email', null , array('placeholder' => trans('notificationbroker::bouncedemails/admin_lang.fields.email_helper'), 'class' => 'form-control', 'id' => 'email')) !!}
        </div>
    </div>
</div>
{{-- TextArea - description --}}
<div class="form-group">
    {!! Form::label('description', trans('notificationbroker::bouncedemails/admin_lang.fields.description'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', null , array('class' => 'form-control textarea', 'id' => 'description')) !!}
    </div>
</div>
{{-- Radio yes/no - active --}}
<div class="form-group">
    {!! Form::label('active', trans('notificationbroker::bouncedemails/admin_lang.fields.active'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-md-10">
        <div class="radio-list">
            <label class="radio-inline">
                {!! Form::radio('active', 0, true, array('id'=>'active_0')) !!}
                {{ trans('general/admin_lang.no') }}</label>
            <label class="radio-inline">
                {!! Form::radio('active', 1, false, array('id'=>'active_1')) !!}
                {{ trans('general/admin_lang.yes') }} </label>
        </div>
    </div>
</div>
{{-- Text - bounce_code --}}
<div class="form-group">
    {!! Form::label('bounce_code', trans('notificationbroker::bouncedemails/admin_lang.fields.bounce_code'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('bounce_code', null , array('placeholder' => trans('notificationbroker::bouncedemails/admin_lang.fields.bounce_code_helper'), 'class' => 'form-control', 'id' => 'bounce_code')) !!}
    </div>
</div>
{{-- belongsToRelationship - bounce_type --}}
<div class="form-group">
    {!! Form::label('bounce_type_id', trans('notificationbroker::bouncedemails/admin_lang.fields.bounce_type'), array('class' => 'col-sm-2 control-label')) !!}

    <div class="col-sm-10">
        @php
            $items = [];
        @endphp
        @foreach($bounce_types as $id => $bounce_type)
            @php
            $items+= [ $id => $bounce_type]
            @endphp
        @endforeach
        {!! Form::select('bounce_type_id',
        ['' => trans('notificationbroker::bouncedemails/admin_lang.fields.bounce_type_helper')] +
        $items
        ,
        null ,
        ['id'=>'bounce_type_id', 'class' => 'form-control select2']) !!}

    </div>
</div>

                </div>
            </div>

            @if(\View::exists('BouncedEmails::admin_edit_lang'))
                @include('BouncedEmails::admin_edit_lang')
            @endif

            <div class="box box-solid">
                <div class="box-footer">
                    <a href="{{ url('/admin/bouncedemails') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    {{-- <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>--}}
                </div>
            </div>

        </div>


        {!! Form::close() !!}
    </div>


@endsection

@section("foot_page")

    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        $(document).ready(function() {
            $(".select2").select2();


        });

    </script>
    {!! JsValidator::formRequest('Clavel\NotificationBroker\Requests\AdminBouncedEmailsRequest')->selector('#formData') !!}
@stop
