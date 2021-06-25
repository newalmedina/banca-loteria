@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')


    <!-- Default box -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("acceso/lang.listado_accesos") }}</h3>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table_acceso" class="table table-bordered table-striped table-responsive" style="width: 99.99%;" aria-hidden="true">
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

                <div class="box-header"><h3 class="box-title">{{ trans("acceso/lang.export") }}</h3></div>

                <div class="box-body">

                    <a href="{{ url('admin/acceso/generateExcel') }}" class="btn btn-app">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        {{ trans('acceso/lang.exportar_accesos') }}
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
            oTable = $('#table_acceso').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "processing": true,
                "responsive": true,
                "serverSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/acceso/list') }}",
                    type        : "POST"
                },
                order: [[7, "desc"]],
                columns: [
                    {
                        "title"         : "{!! trans('acceso/lang.usuario') !!}",
                        orderable       : false,
                        searchable      : false,
                        data: 'userCheck', name: 'logaccess_failed.usuario',
                        width          : '',
                        class          : 'text-center',
                    },
                    {
                        "title"         : "{!! trans('acceso/lang.username') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'username', name: 'logaccess_failed.username',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('acceso/lang.nombre') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'first_name', name: 'user_profiles.first_name',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('acceso/lang.apellidos') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'last_name', name: 'user_profiles.last_name',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('acceso/lang.email') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'email', name: 'users.email',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('acceso/lang.ip') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'ip_address', name: 'logaccess_failed.ip_address',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('acceso/lang.password_failed') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'password', name: 'logaccess_failed.password',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('acceso/lang.fecha_intento') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'creado', name: 'logaccess_failed.created_at',
                        width          : ''
                    },

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
            $('tfoot th',$('#table_acceso')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_acceso')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" style="width: 100%;" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_acceso').on('keyup change', 'tfoot input', function (e) {
                oTable
                    .column($(this).parent().index() + ':visible')
                    .search(this.value)
                    .draw();
            });
        });

    </script>
@stop
