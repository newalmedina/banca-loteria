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
    <li><a href="{{ url("admin/asignaturas/".$pregunta->contenido->modulo->asignatura->id."/modulos/") }}">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$pregunta->contenido->modulo->asignatura->{"titulo:es"} }}</a></li>
    <li><a href="{{ url("admin/modulos/".$pregunta->contenido->modulo_id."/contenidos/") }}">{{ trans('elearning::contenidos/admin_lang.contenidos')." ".$pregunta->contenido->modulo->{"nombre:es"} }}</a></li>
    <li><a href="{{ url("admin/contenidos/".$pregunta->contenido->id."/preguntas/") }}">{{ trans('elearning::contenidos/admin_lang.questions')." ".$pregunta->contenido->nombre}}</a></li>
    <li class="active">{{ trans('elearning::preguntas/admin_lang.respuestas')." ".substr(strip_tags($pregunta->nombre),0,10)}}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans('elearning::preguntas/admin_lang.respuestas')}}</h3></div>

                <div class="box-body">
                    <p class="pull-right">
                        <a href="{{ url("admin/contenidos/".$pregunta->contenido_id."/preguntas/") }}" class="btn btn-primary"><i class="glyphicon glyphicon-menu-left" aria-hidden="true"></i> {{ trans('elearning::modulos/admin_lang.volver') }}</a>
                    </p>
                    @if(Auth::user()->can("admin-contenidos-create"))
                        <p>
                            <a href="{{ url('admin/preguntas/'.$pregunta->id.'/respuestas/create') }}" class="btn btn-primary">{{ trans('elearning::preguntas/admin_lang.nueva_respuesta') }}</a>
                        </p>
                    @endif

                    <table id="table_contenidos" class="table table-bordered table-hover table-responsive"  style="width: 99.99%;" aria-hidden="true">
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
            oTable = $('#table_contenidos').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url: "{{ url('admin/preguntas/'.$pregunta->id.'/respuestas/getData') }}",
                    type: "POST"
                },
                order: [[1, "asc"]],
                columns: [
                    {
                        "title": "{!! trans('elearning::contenidos/admin_lang.activo') !!}",
                        orderable: false,
                        searchable: false,
                        data: 'activa',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('elearning::preguntas/admin_lang.correcta') !!}",
                        orderable: false,
                        searchable: false,
                        data: 'correcta',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('elearning::contenidos/admin_lang.nombre') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'nombre',
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

                    $('.sparkline').each(function () {
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
    </script>
@stop
