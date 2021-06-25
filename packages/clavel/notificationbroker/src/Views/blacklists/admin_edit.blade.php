@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')


@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/blacklists") }}">{{ trans('notificationbroker::blacklists/admin_lang.blacklists') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')


    @include('admin.includes.errors')
    @include('admin.includes.success')


    <div class="row">
        {!! Form::model($blacklist, $form_data, array('role' => 'form')) !!}

        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("notificationbroker::blacklists/admin_lang.info_menu") }}</h3></div>
                <div class="box-body">

                    {{-- Text - to --}}
<div class="form-group">
    {!! Form::label('to', trans('notificationbroker::blacklists/admin_lang.fields.to'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('to', null , array('placeholder' => trans('notificationbroker::blacklists/admin_lang.fields.to_helper'), 'class' => 'form-control', 'id' => 'to')) !!}
    </div>
</div>{{-- Select - slug --}}
<div class="form-group">
    {!! Form::label('slug', trans('notificationbroker::blacklists/admin_lang.fields.slug'),
    array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::select('slug',
        ['' => trans('notificationbroker::blacklists/admin_lang.fields.slug_helper')] +
        [
            'sms' => 'SMS',
'email' => 'eMail'
        ],
        null ,
        ['id'=>'slug', 'class' => 'form-control select2']) !!}
    </div>
</div>

                </div>
            </div>



            <div class="box box-solid">

                <div class="box-footer">

                    <a href="{{ url('/admin/blacklists') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

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

        });

    </script>
    {!! JsValidator::formRequest('Clavel\NotificationBroker\Requests\AdminBlacklistsRequest')->selector('#formData') !!}
@stop
