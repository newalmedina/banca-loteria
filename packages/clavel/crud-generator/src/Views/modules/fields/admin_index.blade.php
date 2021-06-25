@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/crud-generator") }}">{{ trans('crud-generator::modules/admin_lang.list') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.modals')

    <!-- Default box -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("crud-generator::fields/admin_lang.list"). " - ".$module->title }}</h3>
                </div>

                <div class="box-body">
                    <a href="{{ url('admin/crud-generator') }}" class="btn btn-info pull-left"><i class="fa fa-backward" aria-hidden="true"></i> {{ trans('crud-generator::fields/admin_lang.back') }}</a>


                    @if(Auth::user()->can("admin-modulos-crud-create"))
                        <a href="{{ url('admin/crud-generator/'.$module->id.'/fields/create') }}" class="btn btn-success pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i> {{ trans('crud-generator::fields/admin_lang.new') }}</a>
                        <button  onclick="javascript:CreateAll('{{ url('admin/crud-generator/'.$module->id.'/fields/createfull') }}');" class="btn btn-warning pull-right margin-right-lg"><i class="fa fa-list-alt" aria-hidden="true"></i> {{ trans('crud-generator::fields/admin_lang.new_full') }}</button>
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
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
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
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
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
                    url         : "{{ url('admin/crud-generator/'.$module->id.'/fields/list') }}",
                    type        : "POST"
                },
                order: [[ 1, "asc" ]],
                pageLength: 25,
                columns: [
                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.name') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'column_name',
                        name            : 'column_name',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.visual') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'column_title',
                        name            : 'column_title',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.type') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'name',
                        name            : 'crud_field_types.name',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.is_multilang') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'is_multilang',
                        name            : 'is_multilang',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.in_list') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'in_list',
                        name            : 'in_list',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.in_create') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'in_create',
                        name            : 'in_create',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.in_edit') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'in_edit',
                        name            : 'in_edit',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.in_show') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'in_show',
                        name            : 'in_show',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.is_required') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'is_required',
                        name            : 'is_required',
                        sWidth          : ''
                    },                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.order_list') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'order_list',
                        name            : 'order_list',
                        sWidth          : ''
                    },                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.order_create') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'order_create',
                        name            : 'order_create',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('crud-generator::fields/admin_lang.actions') !!}",
                        orderable       : false,
                        searchable      : false,
                        sWidth          : '110px',
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

        function CreateAll(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('crud-generator::fields/admin_lang.create_all_question') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:CreateAllConfirm(\''+url+'\');">{{ trans('crud-generator::fields/admin_lang.confirm') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function CreateAllConfirm(url) {
            $.ajax({
                url     : url,
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                        $("#modal_alert").addClass('modal-success');
                        $("#alertModalHeader").html('{{ trans('crud-generator::fields/admin_lang.fields_created') }}');
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
                        $("#alertModalHeader").html('{{ trans('crud-generator::fields/admin_lang.field_deleted') }}');
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

    </script>

@stop
