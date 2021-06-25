@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {{ Form::open(array('url' => 'admin/settings', 'method' => 'POST', 'id' => 'formFolder')) }}
        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("Settings::admin_lang.info_menu") }}</h3></div>
                <div class="box-body">

                    @foreach($settings as $setting)
                        <div class="form-group">
                            {!! Form::label($setting->key, $setting->display_name, array('class' => 'col-sm-2 control-label required-input')) !!}
                            {!! Form::text($setting->key, $setting->value, array('placeholder' => $setting->display_name, 'class' => 'form-control', 'id' => $setting->key)) !!}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="box box-solid">

                <div class="box-footer">

                    <a href="{{ url('/admin/settings') }}" class="btn btn-default">{{ trans('Settings::admin_lang.cancelar') }}</a>
                    <button name="save" type="submit" class="btn btn-info pull-right">{{ trans('Settings::admin_lang.save') }}</button>

                </div>

            </div>

        </div>

        {!! Form::close() !!}
    </div>

@endsection

@section("foot_page")

@stop