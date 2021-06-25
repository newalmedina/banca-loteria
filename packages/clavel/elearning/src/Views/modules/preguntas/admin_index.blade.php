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
    <li class="active">{{ trans('elearning::contenidos/admin_lang.questions')." ".$contenido->nombre}}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <!-- Modal para la Modificación de estado de proyectos -->
    <div id="modalGrupoPreguntas" class="modal fade" role="dialog" aria-labelledby="modalGrupoPreguntas">
        <div class="modal-dialog modal-lg">
            <div id="content_block" class="modal-content">
                <div class="modal-header">

                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">{{ trans('elearning::contenidos/admin_lang.grupo_preguntas') }}</h4>
                </div>
                <div id="container_grupo_preguntas">

                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal para la Modificación de estado de proyectos -->

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans('elearning::contenidos/admin_lang.questions')}}</h3></div>

                <div class="box-body">
                    <p class="pull-right">
                        <a href="{{ url("admin/modulos/".$contenido->modulo_id."/contenidos/") }}" class="btn btn-primary"><i class="glyphicon glyphicon-menu-left" aria-hidden="true"></i> {{ trans('elearning::modulos/admin_lang.volver') }}</a>
                    </p>
                    @if(Auth::user()->can("admin-contenidos-create"))
                        <p>
                            <a href="{{ url('admin/contenidos/'.$contenido->id.'/preguntas/create') }}" class="btn btn-primary" style="margin-right: 40px;">{{ trans('elearning::preguntas/admin_lang.nueva_pregunta') }}</a>
                            <a href="{{ url('admin/contenidos/'.$contenido->id.'/preguntas/wizard') }}" class="btn btn-success" style="margin-right: 40px;"><i class="fa fa-magic" aria-hidden="true"></i>&nbsp;{{ trans('elearning::preguntas/admin_lang.creacion_rapida') }}</a>
                            <a href="{{ url('admin/contenidos/'.$contenido->id.'/grupos_preguntas') }}" class="btn btn-warning" style="margin-right: 40px;"><i class="fa fa-object-group" aria-hidden="true"></i>&nbsp;{{ trans('elearning::preguntas/admin_lang.grupos_preguntas') }}</a>
                        </p>
                    @endif

                    <table id="table_contenidos" class="table table-bordered table-hover table-responsive"  style="width: 99.99%;" aria-hidden="true">
                        <thead>
                        <tr>
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
            oTable = $('#table_contenidos').DataTable({
                "stateSave": true,
                "stateDuration": 86400, // 1 day
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url: "{{ url('admin/contenidos/'.$contenido->id.'/preguntas/getData') }}",
                    type: "POST"
                },
                order: [[2, "asc"]],
                columns: [
                    {
                        "title": "{!! trans('elearning::contenidos/admin_lang.activo') !!}",
                        orderable: false,
                        searchable: false,
                        data: 'activa',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('elearning::contenidos/admin_lang.preg_obligatorio') !!}",
                        orderable: false,
                        searchable: false,
                        data: 'obligatoria',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('elearning::contenidos/admin_lang.nombre') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'nombre',
                        name: 'pregunta_translations.nombre',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('elearning::contenidos/admin_lang.grupo') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'grupo_pregunta_id',
                        name: 'preguntas.grupo_pregunta_id',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('elearning::contenidos/admin_lang.acciones') !!}",
                        orderable: false,
                        searchable: false,
                        data: 'actions',
                        sWidth: '150px'
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

        function openGrupoPreguntas(url) {
            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#container_grupo_preguntas").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin fa-4x"></i></div>');
            $("#modalGrupoPreguntas").modal("toggle");
            $("#container_grupo_preguntas").load(url);
        }
    </script>
@stop
