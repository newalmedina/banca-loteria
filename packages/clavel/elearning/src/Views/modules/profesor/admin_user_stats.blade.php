@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    @if($path=='docencia')
        <li><a href="{{ url("/admin/profesor") }}">{{ trans('elearning::profesor/admin_lang.zona_profesor') }}</a></li>
        @if(!empty($asignatura))
            <li><a href="{{ url("admin/profesor/detalle/asignatura/".$asignatura->id) }}">{{ $asignatura->titulo }}</a></li>
        @endif
        @if(!empty($modulo))
            <li><a href="{{ url("admin/profesor/detalle/modulo/".$modulo->id) }}">{{ $modulo->nombre }}</a></li>
        @endif
    @else
        <li><a href="{{ url("/admin/alumnos") }}">{{ trans('elearning::profesor/admin_lang.alumnado') }}</a></li>
    @endif
@stop

@section('head_page')
    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet"
          type="text/css"/>

    <style>
        #table_usuarios {
            width: 100% !important;
        }
        .d-flex {
            display: flex;
        }
        .justify-content-center {
            justify-content: center;
        }
        .justify-content-space-around {
            justify-content: space-around;
        }
        .flex-wrap {
            flex-wrap: wrap;
        }
        .flex-grow-1 {
            flex-grow: 1;
        }
        .min-w-33 {
            min-width: 33% !important;
        }
        .min-w-50 {
            min-width: 50% !important;
        }
        .width-unset {
            width: unset !important;
        }
        .w-100 {
            width: 100% !important;
        }
        .box-title {
            text-transform: none !important;
        }
        .box .overlay {
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }
        .px-sm {
            padding-left: 8px;
            padding-right: 8px;
        }
        .py-sm {
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .max-h-overflow-y {
            max-height: 20em;
            overflow-y: auto;
        }
    </style>
@stop

@section('content')

    @include('admin.includes.modals')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("elearning::profesor/admin_lang.asignaturas") }}</h3>
                </div>
                <div class="box-body">
                    <div class="d-flex justify-content-center flex-wrap">
                        @forelse($trackAsignaturas as $track)
                            <div class="px-sm min-w-33 width-unset flex-grow-1">
                                <div id="trackbox-{{ $track->asignatura->id }}_{{ $track->convocatoria->id }}" class="box box-{{ $track->completado ? "success" : "warning" }} asignatura-box collapsed-box w-100">
                                    <div class="box-header with-border">
                                        <h5 class="box-title"><strong>{{ $track->asignatura->titulo }}</strong></h5>
                                        <div class="box-tools pull-right">
                                            <span class="label label-info">{{ $track->convocatoria->nombre }}</span>&nbsp;&nbsp;
                                            @if($track->completado)
                                                <span class="label label-success">{{ trans("elearning::profesor/admin_lang.completado") }}</span>&nbsp;&nbsp;
                                            @endif
                                            <span class="badge bg-{{ $track->aprobado ? "green" : "red" }}">
                                                <i class="fa fa-{{ $track->aprobado ? "check" : "close" }}" aria-hidden="true"></i>
                                            </span>
                                            <button id="button_asig_{{ $track->asignatura->id }}_{{ $track->convocatoria->id }}"
                                                    onclick="getUserStats(this)" data-target="{{ $track->asignatura->id }}_{{ $track->convocatoria->id }}"
                                                    data-scope="asignatura" class="btn btn-box-tool">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div id="overlay-{{ $track->asignatura->id }}_{{ $track->convocatoria->id }}" class="overlay asignatura-overlay" style="display: none">
                                            <i class="fa fa-refresh fa-spin" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                    <div class="box-body no-padding bg-gray disabled" style="display: none;">
                                        <div id="trackbody-{{ $track->asignatura->id }}_{{ $track->convocatoria->id }}" class="asignatura-body col-sm-12">{{-- *** --}}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning">
                                {{ trans("elearning::profesor/admin_lang.no_asignaturas") }}
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-12">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.includes.modals')
@endsection

@section("foot_page")
    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>

    <script src="{{ asset("/assets/admin/vendor/jquery-sparkline/js/jquery.sparkline.min.js") }}"></script>
    <script src="{{ asset('assets/')}}/admin/vendor/chart.js/js/Chart.min.js"></script>

    <script type="text/javascript">
        $(function () {
            @if($trackAsignaturas->count() === 1)
                {{-- Si solo tenemos una unica asignatura abrimos la caja de la asignatura que hay --}}
                $_this = $("#button_asig_{{ $trackAsignaturas->first()->asignatura->id }}_{{ $trackAsignaturas->first()->convocatoria->id }}");
                getUserStats($_this[0]);
            @endif
        });

        function getUserStats(e) {
            id = $(e).attr("data-target");
            scope = $(e).attr("data-scope");
            $_boxTarget = $('#trackbox-'+ id + '.' + scope + '-box');
            $_boxBodyTarget = $('#trackbody-' + id + '.' + scope + '-body');

            if ($_boxBodyTarget.html().length <= 0) {
                $_overlayTarget = $('#overlay-' + id + '.' + scope + '-overlay');
                $_overlayTarget.show();

                identificadores = id.split("_");
                if(identificadores.length > 1) {
                    $_boxBodyTarget.load(`{{ url("admin/profesor/user-stats/$user->id/") }}/${scope}/${identificadores[0]}/${identificadores[1]}`, function () {
                        $_overlayTarget.hide();
                        $_boxTarget.boxWidget("toggle");
                    });
                } else {
                    $_overlayTarget.hide();
                    alert("Missing information about the course");
                }
            } else {
                $_boxTarget.boxWidget("toggle");
            }
        }

        function recalcular(url) {
            var strBtn = "";
            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('elearning::profesor/admin_lang.reset-contenido') }}");
            strBtn += '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn += '<button type="button" class="btn btn-primary" onclick="javascript:confirm_recalcular(\'' + url + '\');">{{ trans('elearning::profesor/admin_lang.reset') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function confirm_recalcular(url) {
            $.get(url, function (data) {
                if(data.id) {
                    $(`#nota_${data.id}`).text(data.nota);

                    if(data.aprobado === true) {
                        var infoAprobado = "{{ trans('general/admin_lang.yes') }}";
                        var classAprobado = "success";
                    } else {
                        var infoAprobado = "{{ trans('general/admin_lang.no') }}";
                        var classAprobado = "danger";
                    }

                    $(`#aprobado_${data.id}`).removeClass("label-success label-danger").addClass(`label-${classAprobado}`).text(infoAprobado);
                    $('#modal_confirm').modal('hide');

                    $("#modal_alert").addClass('modal-success');
                    $("#alertModalBody").html("<i class='fa fa-check-circle ' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('elearning::profesor/admin_lang.success') }}");
                    $("#alertModalFooter").html('<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>');
                    $("#modal_alert").modal('toggle');
                } else {
                    alert("Unexpected Error");
                }
            });
        }
    </script>
@stop
