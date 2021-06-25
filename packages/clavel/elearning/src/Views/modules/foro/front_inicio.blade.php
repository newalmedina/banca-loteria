@extends('front.layouts.control')

@section('control_content')
    <br clear="all"/>
    <div class="container foro_container">
        <h3 class="foro_title mb-none">{{ trans("elearning::foro/front_lang.foro") }}</h3>

        <div class="alert alert-success" id="successHilo" style="display: none;">
            {{ trans("elearning::foro/front_lang.mensaje_correcto") }}
        </div>

        @include('elearning::foro.front_popups')

        <section class="panel">
            <div class="panel-body pb-none">

                <p>{{ trans("elearning::foro/front_lang.info_foro") }}</p>

                @if(auth()->user()->can("frontend-foro-create"))
                    <button type="button" class="mb-xs mt-xs mr-xs btn btn-primary" onclick="openCreateForm(null)" id="NuevoHilo">
                        {{ trans("elearning::foro/front_lang.crear_tema") }}
                    </button>
                    <div class="clearfix"></div>
                @endif

                @if(auth()->user()->can("frontend-foro-list"))
                    <div id="foro_list">
                        {{-- El listado de temas va aqui --}}
                    </div>
                @else
                    <br clear="all">
                    <div class="alert alert-warning">{{ trans("elearning::foro/front_lang.no_tiene_permisos") }}.</div>
                @endif

            </div>
        </section>
    </div>
@endsection

@section("control_foot_page")
    <script>
        $(document).ready(function () {
            loadMensajes();
        });

        function openCreateForm(parent_id) {
            $('#hilo_form').html("");
            let data = getUrlParams("{{ $url }}");
            data["_token"] = "{{ csrf_token() }}";
            data["parent_id"] = parent_id;
            $("#hilo_form").load("{{ url('foro/create') }}", data, function (response, status, xhr) {
                if (status != "error") {
                    $("#modalHilo").modal("toggle");
                }
            });
        }

        function getUrlParams(url) {
            var urlParams = url.split("/"),
                data = {},
                x = 0,
                fields = ["asignatura_id", "modulo_id", "contenido_id"];
            for (let i = 0; i < urlParams.length; i++) {
                if(Number.isSafeInteger(parseInt(urlParams[i]))) {
                    data[fields[x++]] = urlParams[i];
                }
            }
            return data;
        }

        function show_tema(url) {
            $('#foro_list').html("<div class='text-center'><img src='{{ asset("assets/front/img/ajax_loader_vector.gif") }}' width='48'  alt='' /><br>{{ trans("elearning::foro/front_lang.cargando") }}</div>");
            $("#foro_list").load(url);
        }

        function loadMensajes() {
            $('#foro_list').html("<div class='text-center'><img src='{{ asset("assets/front/img/ajax_loader_vector.gif") }}' width='48' alt='' /><br>{{ trans("elearning::foro/front_lang.cargando") }}</div>");
            var url = "{{ $url }}";
            $("#foro_list").load(url);
        }

        function modify_info(iditem) {
            $('#hilo_form').html("");

            var url = "{{ url('foro/edit') }}/" + iditem;
            $("#hilo_form").load(url, function (response, status, xhr) {
                if (status != "error") {
                    $("#modalHilo").modal("toggle");
                }
            });
        }

        function delete_message(iditem) {
            $("#alertModalBody").addClass('bg-warning');
            $("#alertModalBody").html("<i class='fa fa-question-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> <strong>{{ trans("elearning::foro/front_lang.atencion") }}</strong><br><br>{{ trans("elearning::foro/front_lang.atencion_msg") }}");
            $("#alertModalFooter").html('<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('elearning::foro/front_lang.close') }}</button> <button type="button" class="btn btn-primary" onclick="delete_confirm(' + iditem + ');">{{ trans('elearning::foro/front_lang.confirmar') }}</button>');
            $("#modal_alert").modal('toggle');
        }

        function delete_confirm(iditem) {
            var item = String(iditem);

            $.ajax({
                url: "{{ url("foro/destroy") }}/" + iditem,
                success: function (data) {
                    $('#modal_alert').modal('hide');
                    if (data.success) {
                        if(data.parent_id != null) {
                            show_tema("{{ url("foro/show") }}/" + data.parent_id);
                        } else {
                            loadMensajes();
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#alertModalFooter").html('<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('elearning::foro/front_lang.close') }}</button>');
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }
            });
            return false;
        }

        $("body").on('click', '.page-link', function (event) {
            var url = $(this).attr('href');
            $("#foro_list").load(url);
            return false;
            event.PreventDefault();
        });
    </script>
@endsection
