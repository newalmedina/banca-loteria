@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

    <style>
        .table {
            width: 99.8%;
        }
    </style>
@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.modals')

    <div class="row">
        <div class="col-xs-12">

            <div class="box box-primary">

                <div class="box-header"><h3 class="box-title">{{ trans("notificationbroker::notifications-group/admin_lang.listado") }}</h3></div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table_notifications" class="table table-bordered table-hover" aria-hidden="true">
                            <thead>
                            <tr>
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
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection

@section("foot_page")
    <!-- DataTables -->
    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>

    <script type="text/javascript">
        var oTable = '';
        var selected = [];

        $(function () {

            oTable = $('#table_notifications').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    beforeSend  : function(xhr) {xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}' )},
                    url         : "{{ url('admin/notifications-group/getData') }}",
                    type        : "POST"
                },
                order: [[ 0, "desc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('notificationbroker::notifications-group/admin_lang.created_at') !!}",
                        orderable       : true,
                        searchable      : false,
                        data            : 'created_at',
                        name            : 'notifications_broker_group.created_at',
                        sWidth          : '150px'
                    },
                    {
                        "title"         : "{!! trans('notificationbroker::notifications-group/admin_lang.fichero_group') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'fichero_group',
                        name            : 'notifications_broker_group.fichero_group',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('notificationbroker::notifications-group/admin_lang.sender') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'sender',
                        name            : 'sender',
                        sWidth          : ''
                    }
                ],
                "fnDrawCallback": function ( oSettings ) {
                    $('[data-toggle="popover"]').mouseover(function() {
                        $(this).popover("show");
                    });

                    $('[data-toggle="popover"]').mouseout(function() {
                        if($(this).children(".fa").is(':hover') === false && $(this).is(':hover') === false) $(this).popover("hide");
                    });
                },
                oLanguage:
                {!! json_encode(trans('datatable/lang')) !!}

            });

            var state = oTable.state.loaded();
            $('tfoot th',$('#table_notifications')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_notifications')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" style="width:100%;" />' );
                }
            });

            $('#table_notifications').on( 'keyup change','tfoot input', function (e) {
                oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
            });

        });


    </script>
@stop
