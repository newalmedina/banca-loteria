@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/assets/admin/vendor/daterangepicker/css/daterangepicker.css") }}" rel="stylesheet"
          type="text/css"/>

@stop

@section('breadcrumb')
    <li class="active">{{ trans('timetracker::mytimes/admin_lang.list') }}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <!-- Modal para la Modificación de descripcion -->
    <div id="modalDescription" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalDescription">
        <div class="modal-dialog modal-lg">
            <div id="content_block" class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('timetracker::mytimes/admin_lang.add_description') }}</h4>
                </div>
                <div id="container_description">

                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal para la Modificación de descripcion -->

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans("timetracker::mytimes/admin_lang.list") }}</h3></div>

                @if(sizeof($last_projects) > 0)
                <div class="box-body">
                    <!-- You may notice a .margin class added
                    here but that's only to make the content
                    display correctly without having to use a table-->
                    <p>
                        @foreach($last_projects as $project)
                        <button type="button" class="btn margin"
                           onclick="javascript:restartTimeSheetActivity(
                               '{{ url('admin/mytimes/restart-activity/'.$project->customer_id.'/'.
                               $project->project_id.'/'.$project->activity_id) }}');"
                                style="background-color: {{ (!empty($project->color)?$project->color:'') }}">
                            <i class="fa fa-tag" aria-hidden="true"></i>&nbsp;
                            {{ $project->customer_name. " / " .$project->project_name. " / " . $project->activity_name }}
                        </button>

                        @endforeach

                    </p>
                </div>
                @endif

                <div class="box-body">
                    @if(Auth::user()->can("admin-mytimes-create"))
                        <p>
                            <a href="{{ url('admin/mytimes/create') }}" class="btn btn-primary">{{ trans('timetracker::mytimes/admin_lang.new') }}</a>
                        </p>
                    @endif

                    <table id="table_mytimes" class="table table-bordered table-hover table-responsive"  hidden="true" style="width: 99.99%;" aria-hidden="true">
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
                <div class="box-header"><h3 class="box-title">{{ trans('timetracker::mytimes/admin_lang.export') }}</h3></div>
                <div class="box-body">

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <form name="frmGenerateExcel" id="frmGenerateExcel" method="post"
                                      class="form-horizontal">
                                    {{ csrf_field() }}
                                    <a id="exportExcel" class="btn btn-app" style="margin-left: 0px">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                        {{ trans('timetracker::mytimes/admin_lang.export_task') }}
                                    </a>
                                    <a id="exportMyActivity" class="btn btn-app">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                        {{ trans('timetracker::mytimes/admin_lang.myactivity') }}
                                    </a>
                                    <a id="exportMyDay" class="btn btn-app">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                        {{ trans('timetracker::mytimes/admin_lang.myDay') }}
                                    </a>
                                    <a id="exportMyWeek" class="btn btn-app">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                        {{ trans('timetracker::mytimes/admin_lang.myWeek') }}
                                    </a>
                                    <!-- Date range -->
                                    <div class="input-group" style="max-width:240px;">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="exportExcelRange" name="exportExcelRange">
                                    </div>
                                    <!-- /.input group -->

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("foot_page")

    <script type="text/javascript" src="{{ asset('assets/admin/vendor/moment/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/daterangepicker/js/daterangepicker.js')}}"></script>

    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>


    <script type="text/javascript">
        var oTable = '';
        var selected = [];

        var FromEndDate = new Date();

        /* DATEPICKER */
        $(document).ready(function () {
            $('#exportExcelRange').daterangepicker({
                timePicker: false,
                drops: 'up',
                showDropdowns: true,
                endDate: FromEndDate,
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

            $("#exportExcel").click(function () {
                $("#frmGenerateExcel").attr("action", "{{url('admin/mytimes/generateExcel')}}");
                $("#frmGenerateExcel").submit();
            });
            $("#exportMyActivity").click(function () {
                $("#frmGenerateExcel").attr("action", "{{url('admin/mytimes/generateMyActivity')}}");
                $("#frmGenerateExcel").submit();
            });
            $("#exportMyDay").click(function () {
                $("#frmGenerateExcel").attr("action", "{{url('admin/mytimes/generateMyDay')}}");
                $("#frmGenerateExcel").submit();
            });
            $("#exportMyWeek").click(function () {
                $("#frmGenerateExcel").attr("action", "{{url('admin/mytimes/generateMyWeek')}}");
                $("#frmGenerateExcel").submit();
            });

        });

        $(function () {
            oTable = $('#table_mytimes').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/mytimes/getData') }}",
                    type        : "POST"
                },
                order: [[ 0, "desc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('timetracker::mytimes/admin_lang.date') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'date_activity',
                        name            : 'timesheet.start_time',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('timetracker::mytimes/admin_lang.start') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'hour_activity_start',
                        name            : 'hour_activity_start',
                        sWidth          : '80px'
                    },
                    {
                        "title"         : "{!! trans('timetracker::mytimes/admin_lang.end') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'hour_activity_end',
                        name            : 'hour_activity_end',
                        sWidth          : '80px'
                    },
                    {
                        "title"         : "{!! trans('timetracker::mytimes/admin_lang.duration') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'duration',
                        name            : 'timesheet.duration',
                        sWidth          : '80px'
                    },
                    {
                        "title"         : "{!! trans('timetracker::mytimes/admin_lang.customer') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'customer_name',
                        name            : 'customers.name',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('timetracker::mytimes/admin_lang.project') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'project_name',
                        name            : 'projects.name',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('timetracker::mytimes/admin_lang.activity') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'activity_name',
                        name            : 'activities.name',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('timetracker::mytimes/admin_lang.actions') !!}",
                        orderable       : false,
                        searchable      : false,
                        sWidth          : '110px',
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
                },
                oLanguage:{!! json_encode(trans('datatable/lang')) !!}

            });

            var state = oTable.state.loaded();
            $('tfoot th',$('#table_mytimes')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_mytimes')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" style="width: 100%;"  class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_mytimes').on( 'keyup change','tfoot input', function (e) {
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

        function restartTimeSheet(url) {
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

        function restartTimeSheetActivity(url) {
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

        function showPreview(url) {
            $("#content-preview").html('<div id="spinner" class="overlay" style="text-align: center"><i class="fa fa-refresh fa-spin" style="font-size: 64px;" aria-hidden="true"></i></div>');
            $('#bs-modal-preview').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            $("#content-preview").load(url);
        }

        function openDescription(id) {
            var url = "{{ url('admin/mytimes/description') }}/" + id;
            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#container_description").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
            $("#modalDescription").modal("toggle");
            $("#container_description").load(url);
        }
    </script>
@stop
