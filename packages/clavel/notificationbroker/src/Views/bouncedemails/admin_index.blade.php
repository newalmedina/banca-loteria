@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet"
          type="text/css"/>

@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.modals')

    <!-- Default box -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('notificationbroker::bouncedemails/admin_lang.listado') }}</h3>
                </div>

            {{--
            <div class="box-body">
                @if(Auth::user()->can("admin-bouncedemails-create"))
                    <a href="{{ url('admin/bouncedemails/create') }}" class="btn btn-success pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i> {{ trans('notificationbroker::bouncedemails/admin_lang.nueva') }}</a>
                @endif
            </div>
            --}}

            <!-- /.box-header -->
                <div class="box-body">
                    <table id="table_bouncedemails" class="table table-bordered table-striped" aria-hidden="true">
                        <thead>
                        <tr>
                            <th scope="col">
                            <th scope="col">
                            <th scope="col">
                            <th scope="col">
                            <th scope="col">
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th scope="col">
                            <th scope="col">
                            <th scope="col">
                            <th scope="col">
                            <th scope="col">
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

    <div class="row">
        <div class="col-xs-12">
            <div class="box ">
                <div class="box-header"><h3
                        class="box-title">{{ trans("notificationbroker::bouncedemails/admin_lang.export") }}</h3></div>
                <div class="box-body">
                    <a href="{{ url('admin/bouncedemails/export') }}" class="btn btn-app">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        {{ trans('notificationbroker::bouncedemails/admin_lang.exportar_datos') }}
                    </a>
                </div>
            </div>
        </div>
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
            oTable = $('#table_bouncedemails').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "processing": true,
                "responsive": true,
                "serverSide": true,
                "pageLength": 100,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url: "{{ url('admin/bouncedemails/list') }}",
                    type: "POST"
                },
                order: [[1, "asc"]],
                columns: [

                    {
                        "title": "{!! trans('notificationbroker::bouncedemails/admin_lang.fields.active') !!}",
                        orderable: false,
                        searchable: false,
                        data: 'active',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('notificationbroker::bouncedemails/admin_lang.fields.email') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'email',
                        name: 'c.email',
                        sWidth: ''
                    },                    {
                        "title": "{!! trans('notificationbroker::bouncedemails/admin_lang.fields.bounce_code') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'bounce_code',
                        name: 'c.bounce_code',
                        sWidth: ''
                    },                    {
                        "title": "{!! trans('notificationbroker::bouncedemails/admin_lang.fields.bounce_type') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'name',
                        name: 'bouncetypes.name',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('notificationbroker::bouncedemails/admin_lang.acciones') !!}",
                        orderable: false,
                        searchable: false,
                        sWidth: '110px',
                        data: 'actions'
                    }

                ],
                "fnDrawCallback": function (oSettings) {
                    $('[data-toggle="popover"]').mouseover(function () {
                        $(this).popover("show");
                    });

                    $('[data-toggle="popover"]').mouseout(function () {
                        $(this).popover("hide");
                    });
                },
                oLanguage:
                {!! json_encode(trans('datatable/lang')) !!}

            });

            var state = oTable.state.loaded();
            $('tfoot th', $('#table_bouncedemails')).each(function (colIdx) {
                var title = $('tfoot th', $('#table_bouncedemails')).eq($(this).index()).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if (state) defecto = state.columns[colIdx].search.search;

                    $(this).html('<input type="text" class="form-control input-small input-inline" placeholder="' + oTable.context[0].aoColumns[colIdx].title + ' ' + title + '" value="' + defecto + '" />');
                }
            });

            $('#table_bouncedemails').on('keyup change', 'tfoot input', function (e) {
                oTable
                    .column($(this).parent().index() + ':visible')
                    .search(this.value)
                    .draw();
            });

        });

        function changeStatus(url) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
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
            strBtn += '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn += '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo(\'' + url + '\');">{{ trans('general/admin_lang.borrar_item') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function deleteinfo(url) {
            $.ajax({
                url: url,
                type: 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                data: {_method: 'delete'},
                success: function (data) {
                    $('#modal_confirm').modal('hide');
                    if (data) {
                        $("#modal_alert").addClass('modal-success');
                        $("#alertModalHeader").html("Borrado de posts");
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
