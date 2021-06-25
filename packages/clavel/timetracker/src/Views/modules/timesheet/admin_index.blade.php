@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset("/assets/admin/vendor/daterangepicker/css/daterangepicker.css") }}" rel="stylesheet"
        type="text/css"/>
@stop

@section('breadcrumb')
    <li class="active">{{ trans('timetracker::timesheet/admin_lang.list') }}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans("timetracker::timesheet/admin_lang.list") }}</h3>
                </div>

                <div class="box-body">
                    @if(Auth::user()->can("admin-timesheet-create"))
                        <p>
                            <a href="{{ url('admin/timesheet/create') }}"
                               class="btn btn-primary">{{ trans('timetracker::timesheet/admin_lang.new') }}</a>
                        </p>
                    @endif

                    <table id="table_timesheet" class="table table-bordered table-hover table-responsive" style="width: 99.99%;" aria-hidden="true">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
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

    <form name="frmGenerateExcel" id="frmGenerateExcel" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="box   box-primary">
            <div class="box-header with-border"><h3
                    class="box-title">{{ trans('timetracker::projects/admin_lang.export') }}</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class=" col-md-6">
                                <div class="form-group">
                                    <label for="proyecto_filtrado"
                                        class="col-sm-4 control-label pull-left" style="margin-top: 8px;">
                                        {{ trans("timetracker::timesheet/admin_lang.projects") }}</label>
                                    <div class="col-sm-8">
                                        <select name="proyecto_filtrado" id="proyecto_filtrado"
                                                class="js-example-placeholder-single js-states form-control" required >
                                            <option value="" ></option>
                                            @foreach($proyectos as $key=>$value)
                                                <option value="{{ $key }}"
                                                        @if($key==$proyecto) selected @endif>{{
                                                    $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class=" col-md-6">
                                <div class="form-group">
                                    <label for="fecha_filtrado"
                                        class="col-sm-4 control-label pull-left" style="margin-top: 8px;">
                                        {{ trans("timetracker::timesheet/admin_lang.date_range") }}</label>
                                    <div class="col-sm-8">

                                        <div class="input-group" style="max-width:240px;">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" id="fecha_filtrado" name="fecha_filtrado">
                                        </div>



                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <a id="exportExcel" class="btn btn-app" style="margin-left: 0px">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                        {{ trans('timetracker::timesheet/admin_lang.export_excel') }}
                                    </a>

                                </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/moment/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/daterangepicker/js/daterangepicker.js')}}"></script>

    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>

    <script type="text/javascript">
        var oTable = '';
        var selected = [];

        $(document).ready(function () {
            $("#proyecto_filtrado").select2();
            $(".js-example-placeholder-single").select2({
                placeholder: "Selecciona un proyecto",
                allowClear: true
            });

            var endDate = new Date();
            var startDate = new Date();
            startDate.setDate(startDate.getDate()-30);

            $('#fecha_filtrado').daterangepicker({
                timePicker: false,
                drops: 'up',
                showDropdowns: true,
                startDate: startDate,
                endDate: endDate,
                autoApply: true,
                locale: {
                    format: 'DD/MM/YYYY',
                    "separator": " - ",
                    "applyLabel": "Aceptar",
                    "cancelLabel": "Cancelar",
                    "fromLabel": "Desde",
                    "toLabel": "Hasta",
                    "customRangeLabel": "Custom",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "Do",
                        "Lu",
                        "Ma",
                        "Mi",
                        "Ju",
                        "Vi",
                        "Sa"
                    ],
                    "monthNames": [
                        "Enero",
                        "Febrero",
                        "Marzo",
                        "Abril",
                        "Mayo",
                        "Junio",
                        "Julio",
                        "Agosto",
                        "Septiembre",
                        "Octubre",
                        "Noviembre",
                        "Diciembre"
                    ],
                    "firstDay": 1
                }
            });

            oTable = $('#table_timesheet').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url: "{{ url('admin/timesheet/getData') }}",
                    type: "POST"
                },
                order: [[0, "desc"]],
                columns: [
                    {
                        "title": "{!! trans('timetracker::timesheet/admin_lang.date') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'date_activity',
                        name: 'timesheet.start_time',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('timetracker::timesheet/admin_lang.start') !!}",
                        orderable: false,
                        searchable: false,
                        data: 'hour_activity_start',
                        name: 'hour_activity_start',
                        sWidth: '80px'
                    },
                    {
                        "title": "{!! trans('timetracker::timesheet/admin_lang.end') !!}",
                        orderable: false,
                        searchable: false,
                        data: 'hour_activity_end',
                        name: 'hour_activity_end',
                        sWidth: '80px'
                    },
                    {
                        "title": "{!! trans('timetracker::timesheet/admin_lang.duration') !!}",
                        orderable: false,
                        searchable: false,
                        data: 'duration',
                        name: 'timesheet.duration',
                        sWidth: '80px'
                    },
                    {
                        "title": "{!! trans('timetracker::timesheet/admin_lang.customer') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'customer_name',
                        name: 'customers.name',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('timetracker::timesheet/admin_lang.project') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'project_name',
                        name: 'projects.name',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('timetracker::timesheet/admin_lang.activity') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'activity_name',
                        name: 'activities.name',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('timetracker::timesheet/admin_lang.user_name') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'user_name',
                        name: 'user_profiles.last_name',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('timetracker::timesheet/admin_lang.actions') !!}",
                        orderable: false,
                        searchable: false,
                        sWidth: '110px',
                        data: 'actions'
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
                oLanguage:{!! json_encode(trans('datatable/lang')) !!}

            });

            var state = oTable.state.loaded();
            $('tfoot th', $('#table_timesheet')).each(function (colIdx) {
                var title = $('tfoot th', $('#table_timesheet')).eq($(this).index()).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if (state) defecto = state.columns[colIdx].search.search;

                    $(this).html('<input type="text" style="width: 100%;" class="form-control input-small input-inline" placeholder="' + oTable.context[0].aoColumns[colIdx].title + ' ' + title + '" value="' + defecto + '" />');
                }
            });

            $('#table_timesheet').on('keyup change', 'tfoot input', function (e) {
                oTable
                    .column($(this).parent().index() + ':visible')
                    .search(this.value)
                    .draw();
            });

        });

        function changeStatus(url) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
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
            strBtn += '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn += '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo(\'' + url + '\');">{{ trans('general/admin_lang.borrar_item') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function deleteinfo(url) {
            $.ajax({
                url: url,
                type: 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                data: {_method: 'delete'},
                success: function (data) {
                    $('#modal_confirm').modal('hide');
                    if (data) {
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


        $("#exportExcel").click(function () {
            if($("#proyecto_filtrado").val() == "") {
                return;
            }
            $("#frmGenerateExcel").attr("action", "{{url('admin/timesheet/generateExcel')}}");
            $("#frmGenerateExcel").submit();
        });

    </script>
@stop
