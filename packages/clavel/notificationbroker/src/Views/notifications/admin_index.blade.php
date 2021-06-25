@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset("/assets/admin/vendor/datepicker/css/bootstrap-datepicker.min.css") }}" rel="stylesheet"
          type="text/css"/>

    <style>
        .table {
            width: 99.8%;
        }
        .fa-spin-custom {
            -webkit-animation: spin 1000ms infinite linear;
            animation: spin 1000ms infinite linear;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(359deg);
                transform: rotate(359deg);
            }
        }


    </style>
@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.modals')
    <div class="row">
        <div class="col-xs-12">

            <div class="box box-primary">

                <div class="box-header"><h3 class="box-title">{{ trans("notificationbroker::notifications/admin_lang.listado") }}</h3></div>
                <div class="box-body">
                    <form name="frmSender" id="frmSender" method="get" action="{{ url("admin/notifications") }}"
                          class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group" style="margin-top: 10px">
                                    <label for="date_ini" class="col-lg-4 control-label"
                                           style="font-weight: normal; text-align: left;">{{ trans("notificationbroker::notifications/admin_lang.filtro_fecha_select") }}</label>
                                    <div class="col-lg-8 input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar" hidden="true" aria-hidden="true"></i></span>
                                        <input type="text" name="date_ini" id="date_ini" value="{{ $date_ini }}"
                                               class="form-control">
                                        <span class="input-group-btn">
                                            <button id="modify_date" class="btn bg-olive btn-flat" type="button"><i
                                                    class="fa fa-search" aria-hidden="true"></i> {{ trans('notificationbroker::notifications/admin_lang.buscar') }}</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-top: 15px">
                                    <div class="col-sm-offset-1 col-sm-10">
                                        <label id='only_certified'>
                                            {!! Form::checkbox('only_certified', 1, $only_certified, array( 'class' => 'minimal', 'id' => 'only_certified')) !!}
                                            {!! trans('notificationbroker::notifications/admin_lang.only_certified')!!}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table_notifications" class="table table-bordered table-hover" aria-hidden="true">
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
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                            </tfoot>
                        </table>
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
    <script type="text/javascript"
            src="{{ asset('assets/admin/vendor/datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript"
            src="{{ asset('assets/admin/vendor/datepicker/locales/bootstrap-datepicker.'.config('app.locale'). '.min.js')}}"></script>

    <script type="text/javascript">
        var oTable = '';
        var selected = [];

        $(function () {
            var FromEndDate = new Date();

            $("#date_ini").datepicker({
                isRTL: false,
                format: 'dd/mm/yyyy',
                endDate: FromEndDate,
                autoclose: true,
                language: 'es'
            });

            $("#modify_date").click(function () {
                $("#frmSender").submit();
            });


            oTable = $('#table_notifications').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                "pageLength": 100,
                searchDelay: 1000,
                ajax: {
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}')
                    },
                    url: "{{ url('admin/notifications/getData') }}",
                    type: "POST",
                    data: function (d) {
                        d.date_ini = "{{ $date_ini }}";
                        d.only_certified = "{{ $only_certified }}";
                        // d.custom = $('#myInput').val();
                        // etc
                    }
                },
                order: [[2, "desc"]],
                columns: [
                    {
                        "className":      'details-control',
                        "title": "{!! trans('notificationbroker::notifications/admin_lang.detalle') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'id',
                        name            : 'id',
                        sWidth          : '50px'
                    },
                    {
                        "title": "{!! trans('notificationbroker::notifications/admin_lang.validado') !!}",
                        orderable: false,
                        searchable: false,
                        width: '50px',
                        data: 'info'
                    },
                    {
                        "title": "{!! trans('notificationbroker::notifications/admin_lang.sent_at') !!}",
                        orderable: true,
                        searchable: false,
                        data: 'sent_at',
                        name: 'notifications_broker.sent_at',
                        sWidth: '150px'
                    },
                    {
                        "title": "{!! trans('notificationbroker::notifications/admin_lang.tipo') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'slug_type',
                        name: 'notifications_broker.slug_type',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('notificationbroker::notifications/admin_lang.guid') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'guid',
                        name: 'notifications_broker.guid',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('notificationbroker::notifications/admin_lang.credits') !!}",
                        orderable: true,
                        searchable: false,
                        data: 'credits',
                        name: 'notifications_broker.credits',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('notificationbroker::notifications/admin_lang.status') !!}",
                        orderable: true,
                        searchable: false,
                        data: 'name',
                        name: 'notifications_broker.status_slug',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('notificationbroker::notifications/admin_lang.retries') !!}",
                        orderable: true,
                        searchable: false,
                        data: 'retries',
                        name: 'notifications_broker.retries',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('notificationbroker::notifications/admin_lang.to') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'receiver',
                        name: 'notifications_broker.receiver',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('notificationbroker::notifications/admin_lang.response_info') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'response_info',
                        name: 'notifications_broker.response_info',
                        sWidth: ''
                    },
                    {
                        "title": "",
                        orderable: false,
                        searchable: false,
                        width: '50px',
                        data: 'actions'

                    }
                ],
                "fnDrawCallback": function (oSettings) {
                    $('[data-toggle="popover"]').mouseover(function () {
                        $(this).popover("show");
                    });

                    $('[data-toggle="popover"]').mouseout(function () {
                        if ($(this).children(".fa").is(':hover') === false && $(this).is(':hover') === false) $(this).popover("hide");
                    });
                },
                oLanguage:
                {!! json_encode(trans('datatable/lang')) !!}

            });

            var state = oTable.state.loaded();
            $('tfoot th', $('#table_notifications')).each(function (colIdx) {
                var title = $('tfoot th', $('#table_notifications')).eq($(this).index()).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if (state) defecto = state.columns[colIdx].search.search;

                    $(this).html('<input type="text" class="form-control input-small input-inline" placeholder="' + oTable.context[0].aoColumns[colIdx].title + ' ' + title + '" value="' + defecto + '" style="width:100%;" />');
                }
            });

            $('#table_notifications').on('change', 'tfoot input', function (e) {
                oTable
                    .column($(this).parent().index() + ':visible')
                    .search(this.value)
                    .draw();
            });

            $('#table_notifications tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = oTable.row( tr );
                var btn = tr.children("td").children("#btn_detail");

                if ( row.child.isShown() ) {
                    row.child.hide();
                    tr.removeClass('shown');
                    btn.addClass("bg-olive");
                    btn.removeClass("bg-purple");
                    btn.children("i").removeClass("fa-minus");
                    btn.children("i").addClass("fa-plus");
                }
                else {
                    format ( row, tr, btn.attr("data-value") )
                    btn.addClass("bg-purple");
                    btn.removeClass("bg-olive");
                    btn.children("i").removeClass("fa-plus");
                    btn.children("i").addClass("fa-minus");
                }
            });


        });

        function viewCertificate(url) {
            window.open(url);
        }

        function format ( row, tr, id ) {

            row.child('<div style="text-align:center;"><i class="fa fa-refresh fa-spin-custom" style="margin-right:15px;"></i> {{trans("notificationbroker::notifications/admin_lang.cargando")}}</div>').show();
            tr.addClass('shown');

            var url = '{{ url('admin/notifications/getDetail/') }}/' + id;
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    row.child(data).show();
                }
            });

        }


    </script>
@stop
