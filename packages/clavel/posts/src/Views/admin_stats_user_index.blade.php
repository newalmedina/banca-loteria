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
    @include('admin.includes.modals')

    <!-- Default box -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("posts::admin_lang.user_visits") }}</h3>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table_posts" class="table table-bordered table-striped" aria-hidden="true">
                        <thead>
                        <tr>
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

                <div class="box-header"><h3 class="box-title">{{ trans("posts::admin_lang.export") }}</h3></div>

                <div class="box-body">

                    <a href="{{ url('admin/posts/stats/'.$post->id.'/users/export') }}" class="btn btn-app">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        {{ trans('posts::admin_lang.exportar_usuarios') }}
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
            oTable = $('#table_posts').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "processing": true,
                "responsive": true,
                "serverSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/posts/stats/'.$post->id.'/users/list') }}",
                    type        : "POST"
                },
                order: [[ 0, "desc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('posts::admin_lang.visits') !!}",
                        orderable       : true,
                        searchable      : false,
                        data            : 'visits',
                        name            : 's.visits',
                        sWidth          : '50px'
                    },
                    {
                        "title"         : "{!! trans('users/lang.nombre_usuario') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'first_name', name: 'user_profiles.first_name',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('users/lang._APELLIDOS_USUARIO') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'last_name', name: 'user_profiles.last_name',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('users/lang.email_usuario') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'email', name: 'users.email',
                        width          : '200px'
                    },
                    {
                        "title"         : "{!! trans('users/lang.username') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'username', name: 'users.username',
                        width          : '200px'
                    },                    {
                        "title"         : "{!! trans('posts::admin_lang.primer_acceso') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'created_at',
                        name            : 's.created_at',
                        sWidth          : '70px'
                    },                    {
                        "title"         : "{!! trans('posts::admin_lang.ultimo_acceso') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'updated_at',
                        name            : 's.updated_at',
                        sWidth          : '70px'
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
            $('tfoot th',$('#table_posts')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_posts')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_posts').on( 'keyup change','tfoot input', function (e) {
                oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
            });

        });



    </script>

@stop
