@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

    <style>
        #table_usuarios {
            width:100% !important;
        }
    </style>
@stop

@section('breadcrumb')
    <li><a href="{{ url("/admin/profesor") }}">{{ trans('elearning::profesor/admin_lang.zona_profesor') }}</a></li>
    <li><a href="{{ url("admin/profesor/detalle/asignatura/".$modulo->asignatura->id) }}">{{ $modulo->asignatura->titulo }}</a></li>
    <li><a href="{{ url("admin/profesor/detalle/modulo/".$modulo->id) }}">{{ $modulo->nombre }}</a></li>
    <li class="active">{{ $contenido->nombre }}</li>
@stop

@section('content')
    @include('admin.includes.modals')
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                @include('elearning::profesor.partials.admin_detalle_contenido_stats',
                    [
                    'a_tracking' => $a_tracking,
                    'stats'=>$stats_contenido]
                    )

                @foreach($convocatorias as $convocatoria)
                    @php
                    $activa = "";

                    $object_rango = $convocatoria->getRagoModulo($modulo->id);

                    if($convocatoria->fecha_inicio<=\Carbon\Carbon::today()->format('Y-m-d') &&
                        $convocatoria->fecha_fin>=Carbon\Carbon::today()->format('Y-m-d')) {
                        $activa = " (Activa)";
                    }

                    if(!is_null($object_rango)) {
                        $convocatoria->consultar=$object_rango->consultar;
                        $convocatoria->fecha_inicio=$object_rango->fecha_inicio;
                        $convocatoria->fecha_fin=$object_rango->fecha_fin;
                        $convocatoria->porcentaje=$object_rango->porcentaje;
                    }

                    @endphp
                    <div class="col-lg-3 col-md-6 col-xs-12 col-sm-6">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">{{ $convocatoria->nombre.$activa }}</h3>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-8">
                                    {{ $convocatoria->fecha_inicio_formatted }} -
                                    {{ $convocatoria->fecha_fin_formatted }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="pull-right">
                    {!! $convocatorias->render() !!}
                </div>
            </div>
        </div>
    </div>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li>
                <a href="#tab_users" data-toggle="tab">
                    {{ trans("elearning::profesor/admin_lang.usuarios") }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div id="tab_users" class="tab-pane active">

                @if(auth()->user()->can("admin-alumnos-all"))
                    <div style="margin-bottom: 10px;">
                        <button id="Listado" class="mb-xs mt-xs mr-xs btn btn-info" data-value="@if(Session::has('todo_los_alumnos')){{"1"}}@else{{"0"}}@endif">
                            @if (Session::has('todo_los_alumnos'))
                                {{ trans('elearning::alumnos/admin_lang.ver_mis_alumnos') }}
                            @else
                                {{ trans('elearning::alumnos/admin_lang.ver_todos') }}
                            @endif
                        </button>
                    </div>
                @endif
                <table id="table_usuarios" class="table table-bordered table-hover" aria-hidden="true">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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

    @if (!empty($contenido->evaluacion))
    <div class="row">
        <div class="col-xs-12">
            <div class="box ">
                <div class="box-header"><h3 class="box-title">{{ trans("users/lang.export") }}</h3></div>
                <div class="box-body">
                    <a href="{{ url('admin/profesor/asignatura/'.$modulo->asignatura->id.'/generateExcelPreguntas/'.$modulo->id. "/contenido/".$contenido->id) }}" class="btn btn-app">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        {{ trans('elearning::profesor/admin_lang.export_modulo') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">{{ trans('elearning::contenidos/admin_lang.leyenda') }}</h3>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <i class="fa fa-repeat" aria-hidden="true"></i>
                    {{ trans("elearning::profesor/admin_lang.leyenda_reset_content") }}
                </div>
                <div class="col-md-3">
                    <i class="fa fa-list" aria-hidden="true"></i>
                    {{ trans("elearning::profesor/admin_lang.leyenda_detalle_examen") }}
                </div>
            </div>
            <div class="clearfix"></div>
            <br />
        </div>
    </div>
@endsection

@section("foot_page")
    <!-- DataTables -->
    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>
    <script type="text/javascript">
        var oTable = '';

        $(function () {
            oTable = $('#table_usuarios').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/profesor/asignatura/'.$modulo->asignatura->id.
                                        '/getDataUsersContenido/'.$modulo->id.'/'.$contenido->id) }}",
                    type        : "POST"
                },
                order: [[ 0, "asc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('elearning::profesor/admin_lang.nombre') !!}",
                        orderable       : false,
                        searchable      : true,
                        data: 'first_name',
                        name: 'user_profiles.first_name',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::profesor/admin_lang.fecha_lectura') !!}",
                        orderable       : true,
                        searchable      : false,
                        data            : 'fecha_lectura',
                        name            : 'track_contenido.fecha_lectura',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::profesor/admin_lang.convocatoria') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'convocatoria',
                        name            : 'asignatura_convocatorias.nombre',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::profesor/admin_lang.obligatorio') !!}",
                        orderable       : true,
                        searchable      : false,
                        data            : 'obligatorio',
                        name            : 'track_contenido.obligatorio',
                        sWidth          : '80px'
                    }
                    ,{
                        'title' : "{!! trans('elearning::profesor/admin_lang.acciones') !!}",
                        orderable : false,
                        searchable : false,
                        sWidth : '80px',
                        data            : 'actions'
                    }
                ],
                "fnDrawCallback": function ( oSettings ) {
                    var selector = $('[data-toggle="popover"]');
                    selector.mouseover(function() {
                        $(this).popover("show");
                    });

                    selector.mouseout(function() {
                        $(this).popover("hide");
                    });
                },
                oLanguage: {!! json_encode(trans('datatable/lang')) !!}
            });

            var state = oTable.state.loaded();
            $('tfoot th',$('#table_usuarios')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_usuarios')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_usuarios').on( 'keyup change','tfoot input', function (e) {
                oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
            });
        });


        function Reset(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('elearning::profesor/admin_lang.init-contenido') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo(\''+url+'\');">{{ trans('elearning::profesor/admin_lang.reset') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function deleteinfo(url) {
            $.ajax({
                type		: 'POST',
                url			: url,
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                success		: function() {
                    $('#modal_confirm').modal('hide');
                    oTable.ajax.url( "{{ url('admin/profesor/asignatura/'.$modulo->asignatura->id.'/getDataUsersContenido/'.$modulo->id.'/'.$contenido->id) }}" ).load();
                    $.ajax({
                        type		: 'POST',
                        url			: "{{ url('admin/profesor/contenido/'.$contenido->id.'/reloadStats') }}",
                        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                        success		: function(data) {
                            $('#information_stats').html(data);
                        }
                    });
                }
            });

        }

        @if(auth()->user()->can("admin-alumnos-all"))
        $('#Listado').on('click', function(event) {
            var hastodos = $(this).attr("data-value");
            var btn = $(this);

            event.preventDefault(); // To prevent following the link (optional)
            $.ajax({
                async		: true,
                type        : 'GET',
                url         : "{{ url('admin/alumnos/setListado') }}",
                success       : function ( data ) {

                    if (hastodos=='0') {
                        btn.html("{{ trans('elearning::alumnos/admin_lang.ver_mis_alumnos') }}");
                        btn.attr("data-value", '1');
                    } else {
                        btn.html("{{ trans('elearning::alumnos/admin_lang.ver_todos') }}");
                        btn.attr("data-value", "0");
                    }

                    oTable.page( 0 ).draw( true );

                }
            });
        });
        @endif
    </script>
@stop
