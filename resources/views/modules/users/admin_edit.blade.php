@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/users") }}">{{ trans('users/lang.usuarios') }}</a></li>
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
                    <li class="active"><a id="tab_1" data-toggle="tabajax" href="{{ url('admin/users/edit_user/'.$id) }}" data-target="#tab_1-1">{{ trans('users/lang.datos_personales') }}</a></li>
                    @if($id!=0 && (Auth::user()->can('admin-users-update') || Auth::user()->can('admin-users-read')))
                        <li><a id="tab_2" data-toggle="tabajax" href="{{ url('admin/users/roles/'.$id) }}" data-target="#tab_2-2">{{ trans('users/lang.roles') }}</a></li>
                        <li><a id="tab_3" data-toggle="tabajax" href="{{ url('admin/users/social/'.$id) }}" data-target="#tab_3-3">{{ trans('users/lang.social_title') }}</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div id="tab_1-1" class="tab-pane active"></div><!-- /.tab-pane -->
                    @if($id!=0)
                        <div id="tab_2-2" class="tab-pane"></div><!-- /.tab-pane -->
                        <div id="tab_3-3" class="tab-pane"></div><!-- /.tab-pane -->
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