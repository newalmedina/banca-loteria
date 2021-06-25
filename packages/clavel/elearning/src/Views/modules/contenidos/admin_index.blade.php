@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- DataTables -->
    <style>
        body.dragging, body.dragging * {
            cursor: move !important;
        }

        ol.vertical {
            margin: 0px;
            min-height: 10px;
            list-style-type: none;
            padding: 0px;
        }

        ol.vertical ol {
            list-style-type: none;
            padding-right: 0px;
            margin: 0px;
        }

        ol.vertical li {
            background: #fff none repeat scroll 0 0;
            border-bottom: 1px solid #f4f4f4;
            color: #333;
            display: block;
            padding-right: 0px;
            margin-top: 5px;
        }

        .dragged {
            position: absolute;
            background-color: #c0c0c0 !important;
            opacity: 0.9;
            z-index: 2000;
        }

        ol.vertical li.placeholder {
            position: relative;
            background-color: #fcfaf2 !important;
            border: solid 1px #fcefa1 !important;
            height: 40px;
            /** More li styles **/
        }

        ol.vertical li.placeholder:before {
            position: absolute;
            /** Define arrowhead **/
        }

        ol.vertical ol li:last-child {
            border-bottom: none;
        }

        ol.vertical ol li:first-child {
            border-top: 1px solid #f4f4f4;
        }

        #spinner {
            background: rgba(0, 0, 0, 0.1);
            position: absolute;
            width: 100%;
            padding: 50px;
            text-align: center;
            display: none;
        }

        .itemSort {
            cursor: move !important;
            color: #337ab7;
            font-size: 18px;
            background: transparent !important;
        }
    </style>

@stop

@section('breadcrumb')

    <li><a href="{{ url("admin/asignaturas/") }}">{{ trans('elearning::asignaturas/admin_lang.asignaturas') }}</a></li>
    <li>
        <a href="{{ url("admin/asignaturas/".$modulo->asignatura->id."/modulos/") }}">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$modulo->asignatura->{"titulo:es"} }}</a>
    </li>
    <li class="active">{{ trans('elearning::contenidos/admin_lang.contenidos_listado')." ".$modulo->nombre}}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3
                        class="box-title">{{ trans('elearning::contenidos/admin_lang.contenidos_listado')." ".$modulo->nombre}}</h3>
                </div>

                <div class="box-body">

                    <div class="box-body">

                        <p class="pull-right">
                            <a href="{{ url("admin/asignaturas/".$modulo->asignatura->id."/modulos/") }}"
                               class="btn btn-primary"><i
                                    class="glyphicon glyphicon-menu-left" aria-hidden="true"></i> {{ trans('elearning::modulos/admin_lang.volver') }}
                            </a>
                        </p>
                        @if(Auth::user()->can("admin-contenidos-create"))
                            <p>
                                <a href="{{ url('admin/modulos/'.$modulo->id.'/contenidos/create') }}"
                                   class="btn btn-primary">{{ trans('elearning::contenidos/admin_lang.nuevo_contenido') }}</a>
                            </p>
                        @endif

                        <div id="spinner" class="overlay"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>
                        @if($contenidos->count()>0)
                            <?php $actDepth = 0; ?>

                            <ol class="serialization vertical">
                                @foreach($contenidos as $contenido)

                                    @if($actDepth!=$contenido->depth)
                                        @if($actDepth>$contenido->depth)
                                            @for($nX=$actDepth;$nX>$contenido->depth; $nX--)
                                                {!! "</ol>" !!}
                                                {!! "</li>" !!}
                                            @endfor
                                        @endif
                                        <?php $actDepth = $contenido->depth; ?>
                                    @endif

                                    <li id="{{ $contenido->id }}" class="list_folder"
                                        data-name="{{ $contenido->getTranslatedNombre() }}" data-id="{{ $contenido->id }}">
                                        <div style="padding: 10px 15px;">

                                            <button class="btn btn-sm itemSort">
                                                <i class="fa fa-arrows" aria-hidden="true"></i>
                                            </button>

                                            <button id="btn_{{ $contenido->id }}"
                                                    class="btn @if($contenido->activo) btn-success @else btn-danger @endif btn-sm @if(!Auth::user()->can("admin-modulos-update")) disabled @endif"
                                                    onclick="javascript:changeStatus('{{ $contenido->id }}');">
                                                <i class="fa @if($contenido->activo) fa-eye @else fa-eye-slash @endif" aria-hidden="true"></i>
                                            </button>
                                            <span style="margin-left: 20px;">{!!$contenido->getTranslatedNombre()!!}</span>

                                            @if(Auth::user()->can("admin-contenidos-delete"))
                                                <button class="btn btn-danger btn-sm pull-right"
                                                        onclick="javascript:deleteElement('{{ url('admin/modulos/'.$modulo->id.'/contenidos/'.$contenido->id.'/destroy') }}');">
                                                    <i class="fa fa-trash" aria-hidden="true"></i></button>
                                            @endif

                                            @if(Auth::user()->can("admin-contenidos-update"))
                                                <button class="btn btn-primary btn-sm pull-right"
                                                        style="margin-right: 10px;"
                                                        onclick="javascript:window.location='{{ url('admin/modulos/'.$modulo->id.'/contenidos/'.$contenido->id.'/edit') }}';">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i></button>
                                            @endif

                                            @if(Auth::user()->can("admin-contenidos-update") && $contenido->tipo->slug == 'eval')
                                                <button class="btn bg-maroon btn-sm pull-right"
                                                        style="margin-right: 10px;"
                                                        onclick="javascript:window.location='{{ url('admin/contenidos/'.$contenido->id.'/preguntas') }}';">
                                                    <i class="fa fa-question" aria-hidden="true"></i></button>
                                                @if($contenido->preguntas()->count()==0)
                                                    <button class="btn btn-success btn-sm pull-right" style="margin-right: 10px;" onclick="javascript:window.location='{{ url('admin/contenidos/'.$contenido->id.'/preguntas/wizard') }}';"><i class="fa fa-magic" aria-hidden="true"></i></button>
                                                @endif
                                            @endif
                                        </div>

                                        @if($contenido->descendants()->count()>0)
                                            {!! "<ol class=''>" !!}
                                        @else
                                            <ol class=''></ol>
                                            {!! "</li>" !!}
                                        @endif

                                        @endforeach

                                        @if($actDepth>0)
                                            @for($nX=$actDepth;$nX>0; $nX--)
                                                {!! "</ol>" !!}
                                                {!! "</li>" !!}
                                            @endfor
                                        @endif

                                        {!! "</ol>" !!}
                                        @else
                                            <p>{{ trans("basic::menu/admin_lang.not_data_found") }}</p>
                        @endif
                    </div>

                </div>
            </div>

            <div class="box box-info">

                <div class="box-header"><h3
                        class="box-title">{{ trans('elearning::contenidos/admin_lang.leyenda') }}</h3></div>

                <div class="box-body">

                    <div class="row">
                        <div class="col-md-3"><i
                                 class="fa fa-eye text-success" aria-hidden="true"></i> {{ trans("elearning::contenidos/admin_lang.leyenda_activo") }}
                        </div>
                        <div class="col-md-3"><i
                                 class="fa fa-eye-slash text-danger" aria-hidden="true"></i> {{ trans("elearning::contenidos/admin_lang.leyenda_inactivo") }}
                        </div>
                        <div class="col-md-3"><i
                                 class="fa fa-magic text-success" aria-hidden="true"></i> {{ trans("elearning::contenidos/admin_lang.leyenda_wizard") }}
                        </div>
                        <div class="col-md-3"><i
                                class="fa fa-question text-maroon" hidden="true" aria-hidden="true"></i> {{ trans("elearning::contenidos/admin_lang.leyenda_question") }}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <br clear="all">
                    <div class="row">
                        <div class="col-md-3"><i
                                class="fa fa-pencil text-primary" hidden="true" aria-hidden="true"></i> {{ trans("elearning::contenidos/admin_lang.leyenda_modifica") }}
                        </div>
                        <div class="col-md-3"><i
                                class="fa fa-trash text-danger" hidden="true" aria-hidden="true"></i> {{ trans("elearning::contenidos/admin_lang.leyenda_borrar") }}
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

            <script src="{{ asset("/assets/admin/vendor/jquery-sortable/js/jquery-sortable-min.js") }}"></script>

            <script type="text/javascript">
                var oTable = '';
                var selected = [];

                $(function () {
                    var group = $("ol.vertical").sortable({
                        handle: ".itemSort",
                        group: 'serialization',
                        onDrop: function ($item, container, _super) {
                            $("#spinner").fadeIn(500);
                            var data = $item.attr("data-id");
                            var parent = $("#" + data).parent("OL").parent("LI").attr("data-id");
                            var previous = $("#" + data).prev().attr("data-id");

                            if (parent == undefined) parent = '0';
                            if (previous == undefined) previous = '0';

                            var url = "{{ url('admin/modulos/'.$modulo->id."/reordenarArbol/") }}/" + data + "/" + parent + "/" + previous;

                            $.get(url, function (data) {
                                $("#spinner").fadeOut(500);
                            });

                            _super($item, container);
                        }
                    });

                    $("#spinner").css("height", ($(".serialization").height()));
                    $("#spinner").css("width", ($(".serialization").width()));
                });

                function changeStatus(contenido_id, actual, nuevo) {
                    $.ajax({
                        url: '{{ url('admin/modulos/'.$modulo->id.'/contenidos/cambiar_estado/') }}/' + contenido_id,
                        type: 'GET',
                        success: function (data) {
                            if (data) {
                                $obj = $("#btn_" + contenido_id);
                                if ($obj.hasClass("btn-success")) {
                                    $obj.removeClass("btn-success").addClass("btn-danger");
                                    $obj.children("I").removeClass("fa-eye").addClass("fa-eye-slash");
                                } else {
                                    $obj.removeClass("btn-danger").addClass("btn-success");
                                    $obj.children("I").removeClass("fa-eye-slash").addClass("fa-eye");
                                }

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
                        type: 'GET',
                        success: function (data) {
                            $('#modal_confirm').modal('hide');
                            if (data) {
                                window.location.reload();
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
