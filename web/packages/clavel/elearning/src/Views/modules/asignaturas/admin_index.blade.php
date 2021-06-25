@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/rowreorder/1.2.3/css/rowReorder.dataTables.min.css" rel="stylesheet" type="text/css">

@stop

@section('breadcrumb')
    <li class="active">{{ trans('elearning::asignaturas/admin_lang.asignaturas') }}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans("elearning::asignaturas/admin_lang.listado_grupos") }}</h3></div>

                <div class="box-body">
                    @if(Auth::user()->can("admin-asignaturas-create"))
                        <a href="{{ url('admin/asignaturas/create') }}" class="btn btn-primary">{{ trans('elearning::asignaturas/admin_lang.nuevo_pages') }}</a>
                    @endif

                    @if(Auth::user()->can("admin-asignaturas-create"))
                        <a id="importar_asignatura" href="{{ url('admin/asignaturas/importarasignatura') }}" style="margin-left: 30px;" class="btn bg-purple">
                            <i class="fa fa-upload" aria-hidden="true"></i> {{ trans('elearning::asignaturas/admin_lang.importarasignatura') }}
                        </a>
                    @endif

                    <table id="table_asignaturas" class="table table-bordered table-hover table-responsive"  style="width: 99.99%;" aria-hidden="true">
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

    <div class="modal" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="importModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">{{ trans('elearning::asignaturas/admin_lang.importar_asignatura_header') }}</h4>
                </div>
                <div class="modal-body">
                    <form id="import_asignatura_form">
                        <div class="input-group">
                            <input type="text" class="form-control" id="nombrefichero" readonly>
                            <span class="input-group-btn">
                                <div class="btn btn-primary btn-file">
                                    {{ trans('elearning::asignaturas/admin_lang.search_file') }}
                                    {!! Form::file('import_asignatura',array('id'=>'import_asignatura', 'multiple'=>false)) !!}
                                </div>
                            </span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans("general/front_lang.cerrar") }}</button>
                    <button id="import_submit" type="button" class="btn btn-default">
                        {{ trans('elearning::asignaturas/admin_lang.importar_asignatura') }}
                    </button>
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
            oTable = $('#table_asignaturas').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                "paging": false,
                "rowReorder": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/asignaturas/getData') }}",
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
                        "title"         : "{!! trans('elearning::asignaturas/admin_lang.activo') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'activo',
                        name            : 'activo',
                        sWidth          : '50px'
                    },
                    {
                        "title"         : "{!! trans('elearning::asignaturas/admin_lang.nombre') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'titulo',
                        name            : 'asignatura_translations.titulo',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::asignaturas/admin_lang.url_amigable') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'url_amigable',
                        name            : 'asignatura_translations.url_amigable',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::asignaturas/admin_lang.grafica') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'grafica',
                        sWidth          : '90px'
                    },
                    {
                        "title"         : "{!! trans('elearning::asignaturas/admin_lang.acciones') !!}",
                        orderable       : false,
                        searchable      : false,
                        sWidth          : '180px',
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
                oLanguage:{!! json_encode(trans('datatable/lang')) !!}
            });

        var state = oTable.state.loaded();
        $('tfoot th',$('#table_asignaturas')).each( function (colIdx) {
            var title = $('tfoot th',$('#table_asignaturas')).eq( $(this).index() ).text();
            if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                var defecto = "";
                if(state) defecto = state.columns[colIdx].search.search;

                $(this).html( '<input type="text" style="width: 100%;" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
            }
        });

        $('#table_asignaturas').on( 'keyup change','tfoot input', function (e) {
            oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
            });

            oTable.on( 'row-reorder', function ( e, diff, edit ) {
                var ord = "";
                var $e = $('#table_asignaturas');

                $e.find(".info-move").each(function() {
                    if(ord!='') ord+=",";
                    ord+= $(this).attr("data-value");
                });

                reorderAsignatura(ord);
            });
        });

        function reorderAsignatura(ord) {
            $.ajax({
                url     : "{{ url("admin/asignaturas/reordenar") }}",
                type    : 'POST',
                data: {
                    orden: ord

                },
                headers: {
                    'X-CSRF-TOKEN':"{{ csrf_token() }}"
                },
                success : function(data) {
                    if (data=='OK') {
                        oTable.ajax.reload();
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

        function cloneElement(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('elearning::asignaturas/admin_lang.cloneasignatura') }}");
            $("#confirmModalBody").html("{{ trans('elearning::asignaturas/admin_lang.clonate_question') }}<br><br>{{ trans('elearning::asignaturas/admin_lang.clonate_question_2') }}<br><br>{{ trans('elearning::asignaturas/admin_lang.clonate_question_3') }}");
            strBtn += '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn += '<button id="modal_submit_button" type="button" class="btn btn-primary" onclick="javascript:cloneinfo(\'' + url + '\');">{{ trans('elearning::asignaturas/admin_lang.cloneasignatura') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        var ongoingFlag = false;
        function cloneinfo(url) {
            $('#modal_submit_button').attr("disabled", true);
            if (!ongoingFlag) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data) {
                            $('#modal_confirm').modal('hide');
                            $('#modal_submit_button').attr("disabled", false);
                            ongoingFlag = false;
                            oTable.ajax.reload(null, false);
                        } else {
                            $("#modal_alert").addClass('modal-danger');
                            $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                            $("#modal_alert").modal('toggle');
                        }
                        return false;
                    }
                });
            }
            ongoingFlag = true;
            return false;
        }

        function exportElement(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('elearning::asignaturas/admin_lang.exportarasignatura') }}");
            $("#confirmModalBody").html("{{ trans('elearning::asignaturas/admin_lang.exportarasignatura_question') }}");
            strBtn += '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn += '<button id="modal_submit_button" type="button" class="btn btn-primary" onclick="javascript:exportarasignaturainfo(\'' + url + '\');">{{ trans('elearning::asignaturas/admin_lang.exportarasignatura') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function exportarasignaturainfo(url) {
            $('#modal_confirm').modal('hide');
            location.href = url;

        }

        $("#importar_asignatura").click(function (e) {
            e.preventDefault();


            $("#modal_import").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });

            $("#import_asignatura").change(function () {
                $("#nombrefichero").val($(this)[0].files[0].name);
            });


            $("#import_submit").click(function () {

                var btnEnviar = $("#import_submit");

                let file = $("#import_asignatura")[0].files[0];

                if (/^zip?$/.test(file.name.split('.').pop())) {
                    btnEnviar.addClass('disabled');
                    btnEnviar.prepend('<i class="fa fa-spinner fa-spin" aria-hidden="true">&nbsp;</i>');


                    var formData = new FormData();
                    formData.append("plantilla", file);
                    formData.append("_token", "{{ csrf_token() }}");

                    $("#import_submit").attr("disabled", true);

                    $.ajax({
                        url: "{{ url("admin/asignaturas/importarasignatura") }}",
                        type: 'POST',
                        contentType: false,
                        data: formData,
                        processData: false,
                        cache: false
                    }).done(function (data) {
                        var btnEnviar = $("#import_submit");
                        btnEnviar.removeClass('disabled');
                        btnEnviar.attr("disabled", false);
                        btnEnviar.find('i').remove();
                        if(data.result) {

                            $("#modal_import").modal("hide");
                            $("#import_submit").attr("disabled", false);
                            oTable.ajax.reload();

                        } else {
                            alert("{{ trans("elearning::asignaturas/admin_lang.error_import") }}");
                        }
                    });
                } else {
                    alert("{{ trans("elearning::asignaturas/admin_lang.not_valid") }}");
                }
            });

        });
    </script>
@stop
