@extends('admin.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')
<link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet"
    type="text/css" />
@stop

@section('breadcrumb')
<li class="active">{{ $page_title }}</li>
@stop

@section('content')
@include('admin.includes.modals')
@include('admin.includes.errors')
@include('admin.includes.success')

<!-- Modal Modulos a generar-->
<div class="modal fade" id="generateModuleModal" tabindex="-1" role="dialog" aria-labelledby="generateModuleModal"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">{!! __("crud-generator::modules/admin_lang.select_actions")
                    !!}</h4>
            </div>
            <div class="modal-body">
                <form id="formModuleGenerate" action="#">

                    {!! Form::hidden('module_id', '', array('id' => 'module_id')) !!}

                    <form role="form" action="/" id="generateModuleModal-form">

                        <div class="form-group checkbox">
                            <label for="checkall">
                                {!! Form::checkbox('checkall', 1, true, array('id'=>'checkall')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_checkall') }}
                            </label>
                        </div>

                        <hr>
                        <div class="form-group checkbox">
                            <label for="clean_all">
                                {!! Form::checkbox('clean_all', 1, true, array('id'=>'clean_all', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_clean_all') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="model">
                                {!! Form::checkbox('model', 1, true, array('id'=>'model', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_model') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="views">
                                {!! Form::checkbox('views', 1, true, array('id'=>'views', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_views') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="controller">
                                {!! Form::checkbox('controller', 1, true, array('id'=>'controller', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_controller') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="requests">
                                {!! Form::checkbox('requests', 1, true, array('id'=>'requests', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_requests') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="resources">
                                {!! Form::checkbox('resources', 1, true, array('id'=>'resources', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_resources') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="test">
                                {!! Form::checkbox('test', 1, true, array('id'=>'test', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_test') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="api">
                                {!! Form::checkbox('api', 1, true, array('id'=>'api', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_api') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="menu">
                                {!! Form::checkbox('menu', 1, true, array('id'=>'menu', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_menu') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="routes">
                                {!! Form::checkbox('routes', 1, true, array('id'=>'routes', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_routes') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="translations">
                                {!! Form::checkbox('translations', 1, true, array('id'=>'translations', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_translations') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="database">
                                {!! Form::checkbox('database', 1, true, array('id'=>'database', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_database') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="seeds">
                                {!! Form::checkbox('seeds', 1, true, array('id'=>'seeds', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_seeds') }}
                            </label>
                        </div>

                        <div class="form-group checkbox">
                            <label for="post">
                                {!! Form::checkbox('post', 1, true, array('id'=>'post', 'class' => 'module_checkbox')) !!}
                                {{ trans('crud-generator::modules/admin_lang.generate_post') }}
                            </label>
                        </div>

                        {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left"
                    data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>
                <button type="submit" class="btn btn-primary"
                    id="generateModule">{{ trans('crud-generator::modules/admin_lang.generar') }}</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Generando-->
<div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-labelledby="generateModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="loader"></div>
                <div clas="loader-txt">
                    <p>{!! trans("crud-generator::modules/admin_lang.generate_module") !!}<br><br><small>{!!
                            trans("crud-generator::modules/admin_lang.paciencia") !!}</small></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Limpiando-->
<div class="modal fade" id="cleanModal" tabindex="-1" role="dialog" aria-labelledby="cleanModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="loader"></div>
                <div clas="loader-txt">
                    <p>{!! trans("crud-generator::modules/admin_lang.clean_module") !!}<br><br><small>{!!
                            trans("crud-generator::modules/admin_lang.paciencia") !!}</small></p>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Default box -->
<div class="row">
    <div class="col-xs-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans("crud-generator::modules/admin_lang.list") }}</h3>
            </div>

            <div class="box-body">
                @if(Auth::user()->can("admin-modulos-crud-create"))
                <a href="{{ url('admin/crud-generator/create') }}" class="btn btn-success pull-right"><i
                        class="fa fa-plus-circle" aria-hidden="true"></i>
                    {{ trans('crud-generator::modules/admin_lang.new') }}</a>
                @endif
            </div>

            <!-- /.box-header -->
            <div class="box-body">
                <table id="table_crud_generator" class="table table-bordered table-striped" aria-hidden="true">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>

@endsection

@section("foot_page")
<!-- DataTables -->
<script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>

<!-- page script -->
<script type="text/javascript">
    var oTable = '';
        var selected = [];

        $(function () {
            oTable = $('#table_crud_generator').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "processing": true,
                "responsive": true,
                "serverSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/crud-generator/list') }}",
                    type        : "POST"
                },
                order: [[ 1, "asc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('crud-generator::modules/admin_lang.active') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'active',
                        sWidth          : '50px'
                    },
                    {
                        "title"         : "{!! trans('crud-generator::modules/admin_lang.name') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'title',
                        name            : 'title',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::modules/admin_lang.model') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'model',
                        name            : 'model',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::modules/admin_lang.actions') !!}",
                        orderable       : false,
                        searchable      : false,
                        sWidth          : '190px',
                        data            : 'actions'
                    }

                ],
                "fnDrawCallback": function ( oSettings ) {
                    $('[data-toggle="popover"]').mouseover(function() {
                        $(this).popover("show");
                    });

                    $('[data-toggle="popover"]').mouseout(function() {
                        $(this).popover("hide");
                    });
                },
                oLanguage:
                {!! json_encode(trans('datatable/lang')) !!}

            });

            var state = oTable.state.loaded();
            $('tfoot th',$('#table_crud_generator')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_crud_generator')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_crud_generator').on( 'keyup change','tfoot input', function (e) {
                oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
            });

        });

        function changeStatus(url) {
            $.ajax({
                url     : url,
                type    : 'GET',
                success : function(data) {
                    if (data) {
                        oTable.ajax.reload(null, false);
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                }
            });
        }

        function deleteElement(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('general/admin_lang.delete_question') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo(\''+url+'\');">{{ trans('general/admin_lang.borrar_item') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function deleteinfo(url) {
            $.ajax({
                url     : url,
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                data: {_method: 'delete'},
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                        $("#modal_alert").addClass('modal-success');
                        $("#alertModalHeader").html('{{ trans('crud-generator::modules/admin_lang.module_delete') }}');
                        $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                        $("#modal_alert").modal('toggle');
                        oTable.ajax.reload(null, false);
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }

            });
            return false;
        }

        function doGenerate(id) {
            $('#module_id').val(id);

            $("#generateModuleModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
        }

        $('#checkall').on('change', function(e) {
            $('.module_checkbox').prop('checked', $(this).prop("checked"));
        });

        $('.module_checkbox').change(function(){
            if($('.module_checkbox:checked').length == $('.module_checkbox').length){
                   $('#checkall').prop('checked',true);
            }else{
                   $('#checkall').prop('checked',false);
            }
        });


        $('#generateModule').on('click', function(e) {
            e.preventDefault();
            $("#generateModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $('#generateModuleModal').modal('hide');


            var dataString = $('#formModuleGenerate').serialize();

            $.ajax({
                url: "{{url('admin/crud-generator/generate')}}",
                type: "POST",
                data: dataString,
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                success       : function ( data ) {
                    $('#generateModal').modal('hide');
                    if(data) {
                        if(data.status === 'ok') {
                            $("#modal_alert").addClass('modal-success');
                            $("#alertModalHeader").html('{{ trans('crud-generator::modules/admin_lang.titulo_generate') }}');
                            $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('crud-generator::modules/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;

                }
            });
            return false;

        });



        function doClean(id) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('crud-generator::modules/admin_lang.seguro_limpiar') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:goClean('+id+');">{{ trans('crud-generator::modules/admin_lang.clean') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function goClean(id) {
            $("#cleanModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $('#modal_confirm').modal('hide');

            $.ajax({
                url: "{{url('admin/crud-generator/clean/')}}/"+id,
                type: "GET",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                success       : function ( data ) {
                    $('#cleanModal').modal('hide');
                    if(data) {
                        if(data.status === 'ok') {
                            $("#modal_alert").addClass('modal-success');
                            $("#alertModalHeader").html('{{ trans('crud-generator::modules/admin_lang.titulo_clean') }}');
                            $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('crud-generator::modules/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;

                }
            });
            return false;
        }

</script>

@stop
