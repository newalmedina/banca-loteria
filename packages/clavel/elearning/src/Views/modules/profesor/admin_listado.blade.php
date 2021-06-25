@extends('admin.layouts.default')

@section('title')
    @parent Inicio
@stop

@section('breadcrumb')

@stop

@section('content')
    @if(auth()->user()->can("admin-alumnos-all"))
            <div class="col-md-12" style="margin-bottom: 15px;">
                <button id="Listado" class="mb-xs mt-xs mr-xs btn btn-info" data-value="@if(Session::has('todo_los_alumnos')){{"1"}}@else{{"0"}}@endif">
                    @if (Session::has('todo_los_alumnos'))
                        {{ trans('elearning::alumnos/admin_lang.ver_mis_alumnos') }}
                    @else
                        {{ trans('elearning::alumnos/admin_lang.ver_todos') }}
                    @endif
                </button>
            </div>
    @endif
    <div class="col-md-12">
        @if($asignaturas->count()>0)
            <div class="bs-example row" data-example-id="thumbnails-with-custom-content">
                <div class="col-md-12">
                    <div style="display: flex; flex-wrap: wrap; justify-content: space-between">
                        @foreach($asignaturas as $asignatura)
                            <div style="width: 32%; min-width: 150px">
                                <div class="thumbnail">
                                    <div class="caption">
                                        <h3>{{ $asignatura->titulo }}</h3>
                                        <hr>
                                        <p>{!! $asignatura->breve !!}</p>
                                        <p><a href="{{ url('admin/profesor/detalle/asignatura/'.$asignatura->id) }}"
                                              class="btn btn-primary"
                                              role="button">{{ trans("elearning::asignaturas/admin_lang.mostrar") }}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pull-right">
                        {!! $asignaturas->render() !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <div class="box ">
                        <div class="box-header"><h3 class="box-title">{{ trans("users/lang.export") }}</h3></div>
                        <div class="box-body">
                            <a href="{{ url('admin/profesor/generateExcel') }}"
                               class="btn btn-app">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                {{ trans('users/lang.exportar_usuarios') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-xs-12">
                    <div class="alert alert-danger" role="alert">
                        <strong>{{ trans("elearning::asignaturas/front_lang.Attencion") }}</strong> {{ trans("elearning::asignaturas/front_lang.no_asignaturas") }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section("foot_page")
    <script type="text/javascript">
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

                }
            });
        });
        @endif
    </script>
@stop
