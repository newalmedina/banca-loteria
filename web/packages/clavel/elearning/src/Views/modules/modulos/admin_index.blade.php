@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/rowreorder/1.2.3/css/rowReorder.dataTables.min.css" rel="stylesheet" type="text/css">
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/asignaturas") }}">{{ trans('elearning::asignaturas/admin_lang.asignaturas') }}</a></li>
    <li class="active">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$asignatura->titulo }}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans("elearning::modulos/admin_lang.modulos_listado")." ".$asignatura->titulo }}</h3></div>

                <div class="box-body">
                    <p class="pull-right">
                        <a href="{{ url('admin/asignaturas') }}" class="btn btn-primary"><i class="glyphicon glyphicon-menu-left" aria-hidden="true"></i> {{ trans('elearning::modulos/admin_lang.volver') }}</a>
                    </p>
                    @if(Auth::user()->can("admin-modulos-create"))
                        <p>
                            <a href="{{ url('admin/asignaturas/'.$asignatura->id.'/modulos/create') }}" class="btn btn-primary">{{ trans('elearning::modulos/admin_lang.nuevo_modulos') }}</a>
                        </p>
                    @endif

                    <table id="table_convocatorias" class="table table-bordered table-hover table-responsive"  style="width: 99.99%;" aria-hidden="true">
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

@endsection

@section("foot_page")

    <!-- DataTables -->
    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>

    <script src="{{ asset("/assets/admin/vendor/jquery-sparkline/js/jquery.sparkline.min.js") }}"></script>

    <script src="https://cdn.datatables.net/rowreorder/1.2.3/js/dataTables.rowReorder.min.js"></script>

    <script type="text/javascript">
        var oTable = '';
        var selected = [];

        $(function () {
            oTable = $('#table_convocatorias').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                "paging": false,
                rowReorder: true,
                ajax: {
                    beforeSend  : function(xhr) {xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}' )},
                    url         : "{{ url('admin/asignaturas/'.$asignatura->id.'/modulos/getData') }}",
                    type        : "POST"
                },
                columns: [

                    {
                        "title"         : "",
                        orderable       : false,
                        searchable      : false,
                        className       : 'reorder',
                        data            : 'orden',
                        name            : 'orden',
                        sWidth          : '20px'
                    },
                    {
                        "title"         : "{!! trans('elearning::modulos/admin_lang.activo') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'activo',
                        name            : 'activo',
                        sWidth          : '50px'
                    },
                    {
                        "title"         : "{!! trans('elearning::modulos/admin_lang.nombre') !!}",
                        orderable       : false,
                        searchable      : true,
                        data            : 'nombre',
                        name            : 'modulo_translations.nombre',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::modulos/admin_lang.url_amigable') !!}",
                        orderable       : false,
                        searchable      : true,
                        data            : 'url_amigable',
                        name            : 'modulo_translations.url_amigable',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::modulos/admin_lang.grafica') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'grafica',
                        sWidth          : '90px'
                    },
                    {
                        "title"         : "{!! trans('elearning::modulos/admin_lang.acciones') !!}",
                        orderable       : false,
                        searchable      : false,
                        sWidth          : '150px',
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

                    $('.sparkline').each(function() {
                        var $this = $(this);
                        $this.sparkline('html', {
                            type: 'line',
                            lineColor: '#92c1dc',
                            fillColor: "#ebf4f9",
                            height: $this.data('height') ? $this.data('height') : '30',
                            width: '80'
                        });
                    });
                },
                oLanguage:
                {!! json_encode(trans('datatable/lang')) !!}

            });

            var state = oTable.state.loaded();
            $('tfoot th',$('#table_convocatorias')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_convocatorias')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" style="width: 100%;" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_convocatorias').on( 'keyup change','tfoot input', function (e) {
                oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
            });

            oTable.on( 'row-reorder', function ( e, diff, edit ) {
                var ord = "";
                var $e = $('#table_convocatorias');

                $e.find(".info-move").each(function() {
                    if(ord!='') ord+=",";
                    ord+= $(this).attr("data-value");
                });

                reorderModulos(ord);
            });

        });

        function reorderModulos(ord) {
            $.ajax({
                url     : "{{ url("admin/asignaturas/".$asignatura->id."/modulos/reordenar/") }}",
                type    : 'POST',
                data: {
                    orden: ord

                },
                headers: {
                    'X-CSRF-TOKEN':"{{ csrf_token() }}"
                },
                success : function(data) {
                    if (data=='OK') {
                        oTable.ajax.reload(null, false);
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                }
            });
        }

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
