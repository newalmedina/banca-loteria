@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

@stop

@section('breadcrumb')
    <li class="active">{{ trans('elearning::grupos/admin_lang.grupos') }}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans("elearning::grupos/admin_lang.listado_grupos") }}</h3></div>

                <div class="box-body">
                    @if(Auth::user()->can("admin-grupos-create"))
                        <p>
                            <a href="{{ url('admin/grupos/create') }}" class="btn btn-primary">{{ trans('elearning::grupos/admin_lang.nuevo_pages') }}</a>
                        </p>
                    @endif

                    <table id="table_grupos" class="table table-bordered table-hover table-responsive"  style="width: 99.99%;" aria-hidden="true">
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
            oTable = $('#table_grupos').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/grupos/getData') }}",
                    type        : "POST"
                },
                order: [[ 1, "asc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('elearning::grupos/admin_lang.activo') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'activo',
                        name            : 'activo',
                        sWidth          : '50px'
                    },
                    {
                        "title"         : "{!! trans('elearning::grupos/admin_lang.nombre') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'nombre',
                        name            : 'nombre',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::grupos/admin_lang.codigo') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'codigo',
                        name            : 'codigo',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::grupos/admin_lang.acciones') !!}",
                        orderable       : false,
                        searchable      : false,
                        sWidth          : '90px',
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
        $('tfoot th',$('#table_grupos')).each( function (colIdx) {
            var title = $('tfoot th',$('#table_grupos')).eq( $(this).index() ).text();
            if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                var defecto = "";
                if(state) defecto = state.columns[colIdx].search.search;

                $(this).html( '<input type="text" style="width: 100%;" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
            }
        });

        $('#table_grupos').on( 'keyup change','tfoot input', function (e) {
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
                type    : 'GET',
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
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

        function showPreview(url) {
            $("#content-preview").html('<div id="spinner" class="overlay" style="text-align: center"><i class="fa fa-refresh fa-spin" style="font-size: 64px;" aria-hidden="true"></i></div>');
            $('#bs-modal-preview').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            $("#content-preview").load(url);
        }
    </script>
@stop
