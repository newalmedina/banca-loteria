@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/menu") }}">{{ trans('basic::menu/admin_lang.listado_menu') }}</a></li>
    <li class="active">{{ trans('basic::menu/admin_lang.menu') }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        <div class="col-xs-12">

            <div class="box box-primary">

                {!! Form::model($menu, $form_data, array('role' => 'form')) !!}

                <div class="box-header  with-border"><h3 class="box-title">{{ trans("basic::menu/admin_lang.info_menu") }}</h3></div>

                <div class="box-body">

                    <div class="form-group">
                        {!! Form::label('slug', trans('basic::menu/admin_lang.menu_nombre_corto'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div class="col-md-10">
                            {!! Form::text('slug', null, array('placeholder' => trans('basic::menu/admin_lang.menu_nombre_corto_insertar'), 'class' => 'form-control', 'id' => 'slug', 'readonly' => true)) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('name', trans('basic::menu/admin_lang.nombre_menu'), array('class' => 'col-sm-2 control-label required-input')) !!}
                        <div class="col-md-10">
                            {!! Form::text('name', null, array('placeholder' => trans('basic::menu/admin_lang.nombre_menu_insertar'), 'class' => 'form-control', 'id' => 'name')) !!}
                        </div>
                    </div>

                </div>

                <div class="box-footer">

                    <a href="{{ url('/admin/menu') }}" class="btn btn-default">{{ trans('basic::menu/admin_lang.cancelar') }}</a>
                    @if($menu->primary=='1')
                        <button type="button" class="btn btn-info pull-right disabled">{{ trans('general/admin_lang.save') }}</button>
                    @else
                        @if((Auth::user()->can('admin-menu-create') && $menu->id=="") || (Auth::user()->can('admin-menu-update') && $menu->id!=""))
                            <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
                        @endif
                    @endif

                </div>

                {!! Form::close() !!}

            </div>

        </div>
    </div>

@endsection

@section("foot_page")


@stop