@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
@stop

@section('breadcrumb')

    <li><a href="{{ url("admin/asignaturas/") }}">{{ trans('elearning::asignaturas/admin_lang.asignaturas') }}</a></li>
    <li><a href="{{ url("admin/asignaturas/".$contenido->modulo->asignatura->id."/modulos/") }}">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$contenido->modulo->asignatura->{"titulo:es"} }}</a></li>
    <li><a href="{{ url("admin/modulos/".$contenido->modulo_id."/contenidos/") }}">{{ trans('elearning::contenidos/admin_lang.contenidos')." ".$contenido->modulo->{"nombre:es"} }}</a></li>
    <li class="active">{{ trans('elearning::grupos_preguntas/admin_lang.titulo')." ".$contenido->nombre}}</li>
@stop

@section('content')

    @include('admin.includes.modals')
    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans('elearning::grupos_preguntas/admin_lang.grupos')}}</h3></div>

                <div class="box-body">
                    <p class="pull-right">
                        <a href="{{ url("admin/contenidos/".$contenido->id."/preguntas") }}" class="btn btn-primary"><i class="glyphicon glyphicon-menu-left" aria-hidden="true"></i> {{ trans('elearning::modulos/admin_lang.volver') }}</a>
                    </p>
                    @if(Auth::user()->can("admin-contenidos-create"))
                        <p>
                            <a href="{{ url('admin/contenidos/'.$contenido->id.'/grupos_preguntas/create') }}" class="btn btn-primary" style="margin-right: 40px;">{{ trans('elearning::grupos_preguntas/admin_lang.nuevo') }}</a>
                        </p>
                    @endif

                    <table id="table_grupos_preguntas" class="table table-bordered table-hover" aria-hidden="true">
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


@endsection

@section("foot_page")
    <!-- DataTables -->
    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>

    <script type="text/javascript">
        var oTable = '';
        var selected = [];

        $(function () {
            oTable = $('#table_grupos_preguntas').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url: "{{ url('admin/contenidos/'.$contenido->id.'/grupos_preguntas/getData') }}",
                    type: "POST"
                },
                order: [[1, "asc"]],
                columns: [
                    {
                        "title": "{!! trans('elearning::grupos_preguntas/admin_lang.color') !!}",
                        orderable: true,
                        searchable: false,
                        data: 'color',
                        name: 'grupos_preguntas.color',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('elearning::grupos_preguntas/admin_lang.nombre') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'titulo',
                        name: 'grupos_preguntas.titulo',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('elearning::grupos_preguntas/admin_lang.acciones') !!}",
                        orderable: false,
                        searchable: false,
                        data: 'actions',
                        name: 'actions',
                        sWidth: '100px'
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
                oLanguage: {!! json_encode(trans('datatable/lang')) !!}
            });

            var state = oTable.state.loaded();
            $('tfoot th',$('#table_grupos_preguntas')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_grupos_preguntas')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" style="width: 100%;" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_grupos_preguntas').on( 'keyup change','tfoot input', function (e) {
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
