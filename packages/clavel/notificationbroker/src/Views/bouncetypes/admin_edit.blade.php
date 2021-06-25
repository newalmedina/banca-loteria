@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/bouncetypes") }}">{{ trans('notificationbroker::bouncetypes/admin_lang.bouncetype') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')


    @include('admin.includes.errors')
    @include('admin.includes.success')


    <div class="row">
        {!! Form::model($bouncetype, $form_data, array('role' => 'form')) !!}

        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("notificationbroker::bouncetypes/admin_lang.info_menu") }}</h3></div>
                <div class="box-body">
                    {{-- Text - name --}}
<div class="form-group">
    {!! Form::label('name', trans('notificationbroker::bouncetypes/admin_lang.fields.name'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', null , array('placeholder' => trans('notificationbroker::bouncetypes/admin_lang.fields.name_helper'), 'class' => 'form-control', 'id' => 'name')) !!}
    </div>
</div>
{{-- TextArea - description --}}
<div class="form-group">
    {!! Form::label('description', trans('notificationbroker::bouncetypes/admin_lang.fields.description'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', null , array('class' => 'form-control textarea', 'id' => 'description')) !!}
    </div>
</div>
{{-- Radio yes/no - active --}}
<div class="form-group">
    {!! Form::label('active', trans('notificationbroker::bouncetypes/admin_lang.fields.active'), array('class' => 'col-sm-2 control-label')) !!}
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

                </div>
            </div>

            @if(\View::exists('BounceTypes::admin_edit_lang'))
                @include('BounceTypes::admin_edit_lang')
            @endif

            <div class="box box-solid">
                <div class="box-footer">
                    <a href="{{ url('/admin/bouncetypes') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
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
    {!! JsValidator::formRequest('Clavel\NotificationBroker\Requests\AdminBounceTypesRequest')->selector('#formData') !!}
@stop
