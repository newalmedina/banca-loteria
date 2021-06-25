@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link href="{{ asset("/assets/admin/vendor/datepicker/css/bootstrap-datepicker.min.css") }}" rel="stylesheet"
          type="text/css"/>

@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/posts/stats") }}">{{ trans("posts::admin_lang.stats_news") }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop


@section('pre-content')
    <div class="alert bg-gray alert-dismissible" style="margin-bottom: 0;">
        <form name="frmSender" id="frmSender" method="get" action="{{ url("admin") }}" class="form-horizontal">
            {{ csrf_field() }}
            <div class="form-group" style="margin-top: 10px">
                <label for="date_ini" class="col-lg-2 control-label"
                       style="font-weight: normal; text-align: left;">{{ trans("dashboard/admin_lang.filtro_fecha_select") }}</label>
                <div class="col-lg-2 input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar" hidden="true" aria-hidden="true"></i></span>
                    <input type="text" name="date_ini" id="date_ini" value="{{ $date_ini }}" class="form-control">
                    <span class="input-group-btn">
                        <button id="modify_date" class="btn bg-olive btn-flat" type="button"><i
                                    class="fa fa-search" aria-hidden="true"></i> {{ trans('dashboard/admin_lang.buscar') }}</button>
                    </span>
                </div>
            </div>

        </form>
        <div class="text-info"><i class="fa fa-info" aria-hidden="true"></i> <em>{{ trans("dashboard/admin_lang.show_stats") }}</em></div>
    </div>
@endsection

@section('content')

    <div class="box box-solid">
        <div class="box-header ui-sortable-handle" style="cursor: move;">
            <i class="fa fa-th" aria-hidden="true"></i>

            <h3 class="box-title">Registro de visitas diarias por noticia</h3>

        </div>
        <div class="box-body">
            <!-- preparing a DOM with width and height for ECharts -->
            <div id="main" style="width:100%; height:480px;"></div>
        </div>
        <!-- /.box-body -->

    </div>


@endsection

@section("foot_page")
    <script src="{{ asset('assets/admin/vendor/moment/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/datepicker/locales/bootstrap-datepicker.'.config('app.locale'). '.min.js')}}"></script>

    <script type="text/javascript" src="{{ asset('assets/admin/vendor/echarts/echarts.min.js')}}"></script>

    <script>
        var FromEndDate = new Date();

        $(document).ready(function () {


            /* DATEPICKER */
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

            // based on prepared DOM, initialize echarts instance
            var myChart = echarts.init(document.getElementById('main'));

            option = {
                tooltip: {
                    trigger: 'axis',
                    formatter: function (params) {
                        var colorSpan = color => '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:' + color + '"></span>';

                        var fecha = moment(params[0].axisValue,'YYYYMMDD').format('DD/MM/YYYY')
                        let rez = '<p>' + fecha + '</p>';
                        //console.log(params); //quite useful for debug
                        params.forEach(item => {
                            //console.log(item); //quite useful for debug
                            var xx = '<p>'   + colorSpan(item.color) + ' ' + item.seriesName + ': ' + item.data + '</p>'
                            rez += xx;
                        });

                        return rez;
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: [{!!  $registros['date'] !!}],
                    axisLabel: {
                        formatter: (function(value){
                            return moment(value,'YYYYMMDD').format('DD/MM/YYYY');
                        })
                    }
                },
                yAxis: {
                    type: 'value',
                    max: {!! $max_y !!},
                    min: 0
                },
                series: [
                    {
                        name:'Visitas',
                        type:'line',
                        stack: 'A',
                        data:[{!! $registros['data'] !!}]
                    }
                ]
            };

            // use configuration item and data specified to show chart
            myChart.setOption(option);


        });
    </script>

@stop
