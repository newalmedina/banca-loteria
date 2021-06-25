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

                <div class="box-header"><h3 class="box-title">{{ trans("notificationbroker::plantillas/admin_lang.listado_plantillas") }}</h3></div>

                <div class="box-body">

                    @if (auth()->user()->can('admin-plantillas-create'))
                        <p>
                            <a href="{{ url('admin/plantillas/create') }}" class="btn btn-primary">{{ trans('notificationbroker::plantillas/admin_lang.nueva_plantilla') }}</a>
                        </p>
                    @endif

                    <div class="table-responsive">
                        <table id="table_plantillas" class="table table-bordered table-hover" aria-hidden="true">
                            <thead>
                            <tr>
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
            oTable = $('#table_plantillas').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    beforeSend  : function(xhr) {xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}' )},
                    url         : "{{ url('admin/plantillas/getData') }}",
                    type        : "POST"
                },
                order: [[ 2, "asc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('notificationbroker::plantillas/admin_lang.active') !!}",
                        orderable       : false,
                        searchable      : false,
                        sWidth          : '75px',
                        data            : 'active'
                    },
                    {
                        "title"         : "{!! trans('notificationbroker::plantillas/admin_lang.titulo') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'titulo',
                        name            : 'titulo',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('notificationbroker::plantillas/admin_lang.tipo') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'tipo',
                        name            : 'tipo',
                        sWidth          : '100px'
                    },
                    {
                        "title"         : "{!! trans('notificationbroker::plantillas/admin_lang.archivo') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'archivo',
                        name            : 'archivo',
                        sWidth          : '200px'
                    },
                    {
                        "title"         : "{!! trans('notificationbroker::plantillas/admin_lang.subject') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'subject',
                        name            : 'subject',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('notificationbroker::plantillas/admin_lang.acciones') !!}",
                        orderable       : false,
                        searchable      : false,
                        sWidth          : '75px',
                        data            : 'actions'
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
            $('tfoot th',$('#table_plantillas')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_plantillas')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" style="width:100%;" />' );
                }
            });

            $('#table_plantillas').on( 'keyup change','tfoot input', function (e) {
                oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
            });

        });

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
                        oTable.ajax.reload();
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
