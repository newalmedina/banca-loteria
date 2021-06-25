@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section("head_page")
    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('carrousel')
    <img src="/assets/front/img/124A8841.jpg" alt="">
@stop

@section('content')

    <div class="container menuModulos">
        <div class="row">
            <div class="col-md-3">
                <div class="thumbnail box-shadow-custom ">
                    @if(Auth::user()->userProfile->photo!='')
                        <img src="{{ url('profile/getphoto/'.Auth::user()->userProfile->photo) }}" id="image_ouptup" width="100%" alt="">
                    @endif
                    <div style="padding: 10px;">
                        <h4>{{ Auth::user()->userProfile->full_name }}</h4>
                        <hr>
                        @if($track_asignaturas->count()>0)
                            <div class="chart-responsive">
                                <canvas id="pieChart" height="125"></canvas>
                            </div>

                            <ul class="list list-icons list-primary list-borders">
                                <li><i class="fa fa-circle-o" style="color:#51b451" aria-hidden="true"></i> {{ trans("elearning::misasignaturas/front_lang.Aprobados") }}</li>
                                <li><i class="fa fa-circle-o" style="color:#0088cc;" aria-hidden="true"></i> {{ trans("elearning::misasignaturas/front_lang.Pendientes") }}</li>
                                <li><i class="fa fa-circle-o" style="color:#d2322d;" aria-hidden="true"></i> {{ trans("elearning::misasignaturas/front_lang.Suspendidos") }}</li>
                            </ul>
                        @else
                            <p class="text-warning">{{ trans("elearning::misasignaturas/front_lang.no_cursado") }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item active">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#asignaturas" role="tab" aria-controls="asignaturas" aria-selected="true">Asignaturas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#cursos" role="tab" aria-controls="cursos" aria-selected="false">Cursos</a>
                    </li>
                </ul>
                <div class="tab-content mis_cursos" id="myTabContent">
                    <div class="tab-pane fade in active" id="asignaturas" role="tabpanel" aria-labelledby="asignaturas-tab">
                        <table id="table_mis_asignaturas" class="table table-bordered table-hover" aria-hidden="true">
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
                    <div class="tab-pane fade" id="cursos" role="tabpanel" aria-labelledby="cursos-tab">
                        <table id="table_mis_cursos" class="table table-bordered table-hover" aria-hidden="true">
                            <thead>
                            <tr>
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
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br clear="all">
    <br clear="all">

@endsection


@section('foot_page')
    <script src="{{ asset('assets/front/vendor/chartjs/Chart.min.js')}}"></script>
    <script src="{{ asset("/assets/front/js/datatable.js") }}" type="text/javascript"></script>
    <script>
        var oTable = '';
        var selected = [];

        $(document).ready(function() {
            @if($track_asignaturas->count()>0)
            //-------------
            //- PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
            var pieChart = new Chart(pieChartCanvas);

            var PieData = [
                {
                    value: {{ $aprobadas }},
                    color: "#51b451",
                    highlight: "#51b451",
                    label: "{{ trans("elearning::misasignaturas/front_lang.Aprobados") }}"
                },{
                    value: {{ $pendientes }},
                    color: "#0088cc",
                    highlight: "#0088cc",
                    label: "{{ trans("elearning::misasignaturas/front_lang.Pendientes") }}"
                },{
                    value: {{ $suspendidas }},
                    color: "#d2322d",
                    highlight: "#d2322d",
                    label: "{{ trans("elearning::misasignaturas/front_lang.Suspendidos") }}"
                }
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
                    tooltipTemplate: "<%=value %> <%=label%>"
                };
                //Create pie or douhnut chart
                // You can switch between pie and douhnut using the method below.
                pieChart.Doughnut(PieData, pieOptions);
                //-----------------
                //- END PIE CHART -
                //-----------------
            @endif

            oTable = $('#table_mis_asignaturas').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('mis-asignaturas/getData') }}",
                    type        : "POST"
                },
                order: [[ 0, "asc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('elearning::misasignaturas/front_lang.asignatura') !!}",
                        orderable       : true,
                        searchable      : true,
                        name            : 'asignatura_translations.titulo',
                        data            : 'titulo',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::misasignaturas/front_lang.fecha_incio') !!}",
                        orderable       : true,
                        searchable      : false,
                        name            : 'fecha_inicio',
                        data            : 'fecha_inicio',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::misasignaturas/front_lang.creditos') !!}",
                        orderable       : true,
                        searchable      : false,
                        name            : 'asignatura_translations.creditos',
                        data            : 'creditos',
                        sWidth          : '90px'
                    },
                    {
                        "title"         : "{!! trans('elearning::misasignaturas/front_lang.grafica_1') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'grafica_1',
                        sWidth          : '90px'
                    },
                    {
                        "title"         : "{!! trans('elearning::misasignaturas/front_lang.nota') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'grafica_2',
                        sWidth          : '90px'
                    },
                    {
                        "title"         : "{!! trans('elearning::misasignaturas/front_lang.acciones') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'actions',
                        sWidth          : '100px'
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
        $('tfoot th',$('#table_mis_asignaturas')).each( function (colIdx) {
            var title = $('tfoot th',$('#table_mis_asignaturas')).eq( $(this).index() ).text();
            if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                var defecto = "";
                if(state) defecto = state.columns[colIdx].search.search;

                $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
            }
        });

        $('#table_mis_asignaturas').on( 'keyup change','tfoot input', function (e) {
            oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
        });

            oTable2 = $('#table_mis_cursos').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('mis-cursos/getData') }}",
                    type        : "POST"
                },
                order: [[ 0, "asc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('elearning::asignaturas/front_lang.cursos') !!}",
                        orderable       : true,
                        searchable      : true,
                        name            : 'curso_translations.nombre',
                        data            : 'nombre',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::misasignaturas/front_lang.acciones') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'actions',
                        sWidth          : '180px'
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
        });
    </script>
@endsection
