@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

@stop

@section('breadcrumb')
    <li class="active">{{ trans('elearning::codigos/admin_lang.codigos') }}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <!-- Vista previa -->
    <div class="modal modal-preview fade in" id="bs-modal-preview">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('basic::pages/admin_lang.preview') }}</h4>
                </div>
                <div id="content-preview" class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans("elearning::codigos/admin_lang.listado_codigos") }}</h3></div>

                <div class="box-body">
                    @if(Auth::user()->can("admin-codigos-create"))
                        <p>
                            <a href="{{ url('admin/codigos/create') }}" class="btn btn-primary">{{ trans('elearning::codigos/admin_lang.nuevo_codigos') }}</a>
                            <a href="{{ url('admin/codigos/create_massive') }}" class="btn btn-primary">{{ trans('elearning::codigos/admin_lang.nuevo_codigo_masivo') }}</a>
                        </p>
                    @endif

                    <table id="table_codigos" class="table table-bordered table-hover table-responsive"  style="width: 99.99%;" aria-hidden="true">
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

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-body">

                    <a href="{{ url('admin/codigos/generateExcel') }}" class="btn btn-app">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        {{ trans('elearning::codigos/admin_lang.exportar_codigos') }}
                    </a>

                    <a href="{{ url('admin/codigos/generateExcelQrCode') }}" class="btn btn-app">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        {{ trans('elearning::codigos/admin_lang.exportar_codigos_qrcode') }}
                    </a>

                    <a id="importar_codigos" href="#" class="btn btn-app">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        {{ trans('elearning::codigos/admin_lang.importar_codigos') }}
                    </a>

                </div>

            </div>

        </div>
    </div>

    <div class="modal" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="importModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ trans('elearning::codigos/admin_lang.importar_codigos_header') }}</h4>
                </div>
                <div class="modal-body">
                    <form id="import_codigos_form">
                        <div class="input-group">
                            <input type="text" class="form-control" id="nombrefichero" readonly>
                            <span class="input-group-btn">
                                        <div class="btn btn-primary btn-file">
                                            {{ trans('elearning::modulos/admin_lang.search_logo') }}
                                            {!! Form::file('import_codigos',array('id'=>'import_codigos', 'multiple'=>false)) !!}
                                        </div>
                                    </span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ url('admin/codigos/generateExcel_plantilla') }}" class="btn btn-default text-left">
                        {{ trans('elearning::codigos/admin_lang.template') }}
                    </a>

                    <button id="import_submit" type="button"
                            class="btn btn-default">{{ trans('elearning::codigos/admin_lang.importar_codigos') }}</button>
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

            $("#import_codigos").change(function () {
                $("#nombrefichero").val($(this)[0].files[0].name);
            });

            $("#importar_codigos").click(function (e) {
                e.preventDefault();

                $("#modal_import").modal("show");

                $("#import_submit").click(function () {
                    let file = $("#import_codigos")[0].files[0];

                    if (/^xls(x)?$/.test(file.name.split('.').pop())) {
                        let formData = new FormData();
                        formData.append("plantilla", file);
                        formData.append("_token", "{{ csrf_token() }}");

                        $("#import_submit").attr("disabled", true);

                        $.ajax({
                            url: "{{ url("admin/codigos/importCodigos") }}",
                            type: 'POST',
                            contentType: false,
                            data: formData,
                            processData: false,
                            cache: false
                        }).done(function (data) {
                            if(data.result) {
                                if(data.existentes.length) {
                                    alert("{{ trans("elearning::codigos/admin_lang.codigos_existentes") }}:\n" + data.existentes.join(", "));
                                } else {
                                    $("#modal_import").modal("hide");
                                    $("#import_submit").attr("disabled", false);
                                    oTable.ajax.reload();
                                }
                            } else {
                                alert("{{ trans("elearning::codigos/admin_lang.error_import") }}");
                            }
                        });
                    } else {
                        alert("{{ trans("elearning::codigos/admin_lang.not_valid") }}");
                    }
                });

            });

            oTable = $('#table_codigos').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/codigos/getData') }}",
                    type        : "POST"
                },
                order: [[ 2, "asc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('elearning::codigos/admin_lang.active') !!}",
                        orderable       : false,
                        searchable      : false,
                        data: 'active', name            : 'active',
                        sWidth          : '50px'
                    },
                    {
                        "title"         : "{!! trans('elearning::codigos/admin_lang.ilimitado') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'ilimitado',name            : 'ilimitado',
                        sWidth          : '50px'
                    },
                    {
                        "title"         : "{!! trans('elearning::codigos/admin_lang.codigo') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'codigo',name            : 'codigo',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::codigos/admin_lang.status') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'status', name            : 'status',
                        sWidth          : '100px'
                    },
                    {
                        "title"         : "{!! trans('elearning::codigos/admin_lang.acciones') !!}",
                        orderable       : false,
                        searchable      : false,
                        sWidth          : '140px',
                        data            : 'actions',
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
        $('tfoot th',$('#table_codigos')).each( function (colIdx) {
            var title = $('tfoot th',$('#table_codigos')).eq( $(this).index() ).text();
            if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                var defecto = "";
                if(state) defecto = state.columns[colIdx].search.search;

                $(this).html( '<input type="text" style="width: 100%;" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
            }
        });

        $('#table_codigos').on( 'keyup change','tfoot input', function (e) {
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

        function showQrCode(url) {
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
