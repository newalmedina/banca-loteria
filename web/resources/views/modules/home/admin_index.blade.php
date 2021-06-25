@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ asset("/assets/admin/vendor/jvectormap/css/jquery-jvectormap-1.2.2.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/assets/admin/vendor/datepicker/css/bootstrap-datepicker.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
@stop

@section('pre-content')
    <div class="alert bg-gray alert-dismissible" style="margin-bottom: 0;">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <form name="frmSender" id="frmSender" method="get" action="{{ url("admin") }}" class="form-horizontal">

            <div class="form-group" style="margin-top: 10px">
                <label for="date_ini" class="col-lg-2 control-label" style="font-weight: normal; text-align: left;">{{ trans("dashboard/admin_lang.filtro_fecha_select") }}</label>
                <div class="col-lg-2 input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                    <input type="text" name="date_ini" id="date_ini" value="{{ $date_ini }}" class="form-control">
                    <span class="input-group-btn">
                        <button id="modify_date" class="btn bg-olive btn-flat" type="button"><i class="fa fa-search" aria-hidden="true"></i> {{ trans('dashboard/admin_lang.buscar') }}</button>
                    </span>
                </div>
            </div>

        </form>
        <div class="text-info"><i class="fa fa-info" aria-hidden="true"></i> <em>{{ trans("dashboard/admin_lang.show_stats") }}</em></div>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-user" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans('dashboard/admin_lang.usuarios_hoy')}}</span>
                    <span class="info-box-number">{{ $a_contadores["today"] }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-users" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans('dashboard/admin_lang.usuarios_alguna_vez')}}</span>
                    <span class="info-box-number">{{ $a_contadores["anything"] }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-user-plus"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans('dashboard/admin_lang.visitas_mas_veces')}}</span>
                    <span class="info-box-number">{{ $a_contadores["moreday"] }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-user-times"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans('dashboard/admin_lang.visitas_una_veces')}}</span>
                    <span class="info-box-number">{{ $a_contadores["onlyday"] }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
        <!-- Left col -->
        <div class="col-lg-8">
            <!-- MAP & BOX PANE -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('dashboard/admin_lang.visitors_map')}}</h3>

                    <div class="box-tools pull-right">
                        <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-minus"></i>
                        </button>
                        <button data-widget="remove" class="btn btn-box-tool" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-md-9 col-sm-8">
                            <div class="pad">
                                <!-- Map will be created here -->
                                <div id="world-map-markers" style="height: 325px;"></div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-md-3 col-sm-4">
                            <div style="min-height: 280px" class="pad box-pane-right bg-primary">
                                <div class="description-block margin-bottom">
                                    <div class="sparkbar pad" data-color="#fff">
                                        <?php
                                        $nX = 0;
                                        ?>
                                        @foreach($a_contadores["total_semana"]["total"] as $value)
                                            @if($nX>0) , @endif
                                            {{ $value }}
                                            <?php
                                            $nX++;
                                            ?>
                                        @endforeach
                                    </div>
                                    <h5 class="description-header">{{ $a_contadores["anything"] }}</h5>
                                    <span class="description-text">{{ trans('dashboard/admin_lang.visitas')}}</span>
                                </div>
                                <!-- /.description-block -->
                                <div class="description-block margin-bottom">
                                    <div class="sparkbar pad" data-color="#fff">
                                        <?php
                                        $nX = 0;
                                        ?>
                                        @foreach($a_contadores["total_semana"]["anonimos"] as $value)
                                            @if($nX>0) , @endif
                                            {{ $value }}
                                            <?php
                                            $nX++;
                                            ?>
                                        @endforeach
                                    </div>
                                    <h5 class="description-header">{{ $a_contadores["anonimus"] }}%</h5>
                                    <span class="description-text">{{ trans('dashboard/admin_lang.anonimos')}}</span>
                                </div>
                                <!-- /.description-block -->
                                <div class="description-block">
                                    <div class="sparkbar pad" data-color="#fff">
                                        <?php
                                        $nX = 0;
                                        ?>
                                        @foreach($a_contadores["total_semana"]["registrados"] as $value)
                                            @if($nX>0) , @endif
                                            {{ $value }}
                                            <?php
                                            $nX++;
                                            ?>
                                        @endforeach
                                    </div>
                                    <h5 class="description-header">{{ $a_contadores["registers"] }}%</h5>
                                    <span class="description-text">{{ trans('dashboard/admin_lang.registrados')}}</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>

        <div class="col-lg-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("dashboard/admin_lang.browser") }}</h3>

                    <div class="box-tools pull-right">
                        <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-minus"></i>
                        </button>
                        <button data-widget="remove" class="btn btn-box-tool" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="chart-responsive">
                                <canvas id="pieChart" height="325"></canvas>
                            </div>
                            <!-- ./chart-responsive -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-4">
                            <ul class="chart-legend clearfix">
                                @foreach($a_contadores["browser"] as $value)
                                    <li><i class="fa fa-circle-o" style="color:{{ $value["color"] }}"></i> {{ $value["name"] }} @if($value["is_mobile"]) - <i class="fa fa-mobile text-purple" style="font-size: 20px;"></i> @endif</li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->

            </div>
            <!-- /.box -->

        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("dashboard/admin_lang.ultimas_visitas_paginas") }}</h3>

                    <div class="box-tools pull-right">
                        <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-minus"></i>
                        </button>
                        <button data-widget="remove" class="btn btn-box-tool" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="box-body">

                    <table id="table_routes" class="table table-bordered table-hover" aria-hidden="true">
                        <thead>
                        <tr>
                            <th>{{ trans("dashboard/admin_lang.titulo_pagina") }}</th>
                            <th>{{ trans("dashboard/admin_lang.nombre_menu") }}</th>
                            <th>{{ trans("dashboard/admin_lang.clicks") }}</th>
                            <th>{{ trans("dashboard/admin_lang.estado") }}</th>
                            <th>{{ trans("dashboard/admin_lang.sevenlastdays") }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($a_contadores["routes"] as $key => $value)
                            <tr>
                                <td>{{ $value["titulo"] }}</td>
                                <td>
                                    @if($value["status"]=='1')
                                        <a href="{{ url($value["route"]) }}" target="_blank">
                                            @endif
                                            {{ $value["route"] }}
                                            @if($value["status"]=='1')
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $value["clicks"] }}</td>
                                <td>
                                    @if($value["status"]=='1')
                                        <span class="label label-success">{{ trans("dashboard/admin_lang.activo") }}</span>
                                    @else
                                        <span class="label label-warning">{{ trans("dashboard/admin_lang.desactivo") }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="sparkline" data-color="#00a65a" data-height="20">
                                        <?php
                                        $nX = 0;
                                        ?>
                                        @foreach($value["anteriores"] as $value2)
                                            @if($nX>0) , @endif
                                            {{ $value2 }}
                                            <?php
                                            $nX++;
                                            ?>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('foot_page')
    <script src="{{ asset('assets/')}}/admin/vendor/jquery-sparkline/js/jquery.sparkline.min.js"></script>
    <script src="{{ asset('assets/')}}/admin/vendor/chart.js/js/Chart.min.js"></script>
    <script src="{{ asset('assets/')}}/admin/vendor/jvectormap/js/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="{{ asset('assets/')}}/admin/vendor/jvectormap/js/jquery-jvectormap-world-mill-en.js"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/datepicker/locales/bootstrap-datepicker.'.config('app.locale'). '.min.js')}}"></script>
    <!-- DataTables -->
    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>

    <script>
        var FromEndDate = new Date();

        $(document).ready(function() {


            /* DATATABLE */
            oTable = $('#table_routes').DataTable({
                order: [[ 2, "desc" ]],
                columns: [
                    {
                        width          : ''
                    },
                    {
                        width          : ''
                    },
                    {
                        width          : '100px'
                    },
                    {
                        width          : '100px'
                    },
                    {
                        orderable       : false,
                        searchable      : false,
                        width          : '100px'
                    }
                ],
                oLanguage:
                {!! json_encode(trans('datatable/lang')) !!}

            });

            /* DATEPICKER */
            $("#date_ini").datepicker({
                isRTL: false,
                format: 'dd/mm/yyyy',
                endDate: FromEndDate,
                autoclose:true,
                language: 'es'
            });

            $("#modify_date").click (function() {
                $("#frmSender").submit();
            });

            //-------------
            //- PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
            var pieChart = new Chart(pieChartCanvas);
            var PieData = [
                <?php
                $nX = 0;
                ?>
                @foreach($a_contadores["browser"] as $value)
                @if($nX>0) , @endif
                    {
                    value: {{ $value["total"] }},
                    color: "{{ $value["color"] }}",
                    highlight: "{{ $value["color"] }}",
                    label: "{{ $value["name"] }}"
                }
                <?php
                $nX++;
                ?>
                @endforeach
            ];

            var pieOptions = {
                //Boolean - Whether we should show a stroke on each segment
                segmentShowStroke: true,
                //String - The colour of each segment stroke
                segmentStrokeColor: "#fff",
                //Number - The width of each segment stroke
                segmentStrokeWidth: 1,
                //Number - The percentage of the chart that we cut out of the middle
                percentageInnerCutout: 50, // This is 0 for Pie charts
                //Number - Amount of animation steps
                animationSteps: 100,
                //String - Animation easing effect
                animationEasing: "easeOutBounce",
                //Boolean - Whether we animate the rotation of the Doughnut
                animateRotate: true,
                //Boolean - Whether we animate scaling the Doughnut from the centre
                animateScale: false,
                //Boolean - whether to make the chart responsive to window resizing
                responsive: true,
                // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio: false,
                //String - A legend template
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
                //String - A tooltip template
                tooltipTemplate: "<%=value %> <%=label%> {{ trans("dashboard/admin_lang.usuarios") }}"
            };
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            pieChart.Doughnut(PieData, pieOptions);
            //-----------------
            //- END PIE CHART -
            //-----------------


            /* jVector Maps
             * ------------
             * Create a world map with markers
             */
            //jvectormap data
            var visitorsData = {
            <?php
            $nX=0;
            ?>
            @foreach($a_contadores["mapa_country"] as $value)
            @if($nX>0) {!! "," !!} @endif
                        {!! '"'.$value["name"].'": '.$value["total"] !!}
            <?php
            $nX++;
            ?>
            @endforeach

    };

        //World map by jvectormap
        $('#world-map-markers').vectorMap({
            map: 'world_mill_en',
            backgroundColor: "transparent",
            regionStyle: {
                initial: {
                    fill: '#e4e4e4',
                    "fill-opacity": 1,
                    stroke: 'none',
                    "stroke-width": 0,
                    "stroke-opacity": 1
                }
            },
            series: {
                regions: [{
                    values: visitorsData,
                    scale: ["#92c1dc", "#123245"],
                    normalizeFunction: 'polynomial'
                }]
            },
            onRegionLabelShow: function (e, el, code) {
                if (typeof visitorsData[code] != "undefined")
                    el.html(el.html() + ': ' + visitorsData[code] + ' {{ trans("dashboard/admin_lang.accesis") }}');
            },
            markerStyle: {
                initial: {
                    fill: '#00a65a',
                    stroke: '#111'
                }
            },
            markers: [
                <?php
                $nX=0;
                ?>
                @foreach($a_contadores["mapa"] as $value)
                @if($nX>0) , @endif
                            {
                    latLng: [{{ $value["lat"]  }}, {{ $value["lon"] }}],
                    name: $('<textarea />').html('{{ $value["name"] }} ({{ $value["total"] }}  {{ trans("dashboard/admin_lang.accesis") }})').text()
                }
                <?php
                $nX++;
                ?>
                @endforeach

            ]
        });

        //-----------------
        //- SPARKLINE BAR -
        //-----------------
        $('.sparkbar').each(function () {
            var $this = $(this);
            $this.sparkline('html', {
                type: 'bar',
                height: $this.data('height') ? $this.data('height') : '30',
                barColor: $this.data('color')
            });
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

        });
    </script>

@stop
