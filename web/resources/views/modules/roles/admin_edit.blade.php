@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/roles") }}">{{ trans('roles/lang.roles') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')
    @include('admin.includes.modals')


    <div class="row">
        <div class="col-xs-12">

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a id="tab_1" data-toggle="tabajax" href="{{ url('admin/roles/edit_role/'.$id) }}" data-target="#tab_1-1">{{ trans('roles/lang.datos_basicos_roles') }}</a></li>
                    @if($id!=0 && (Auth::user()->can('admin-roles-update') || Auth::user()->can('admin-roles-read')))
                        <li><a id="tab_2" data-toggle="tabajax" href="{{ url('admin/roles/permissions/'.$id) }}" data-target="#tab_2-2">{{ trans('roles/lang.permisos_roles') }}</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div id="tab_1-1" class="tab-pane active"></div><!-- /.tab-pane -->
                    @if($id!=0)
                        <div id="tab_2-2" class="tab-pane"></div><!-- /.tab-pane -->
                    @endif
                </div><!-- /.tab-content -->
            </div>

        </div>
    </div>

@endsection

@section("foot_page")
    <script type="text/javascript">
        $(document).ready(function() {
            $('[data-toggle="tabajax"]').click(function(e) {
                loadTab($(this));
                return false;
            });

            @if (Session::get('tab',"") != "")
            loadTab($("#{!! Session::get('tab') !!}"));
            @else
            loadTab($("#tab_1"));
            @endif

        });

        function loadTab(obj) {
            var $this = obj,
                loadurl = $this.attr('href'),
                targ = $this.attr('data-target');

            $.get(loadurl, function(data) {
                $(targ).html(data);
            });

            $this.tab('show');
        }
    </script>
@stop