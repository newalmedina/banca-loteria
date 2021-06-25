@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <style>
        .select2-container--default .select2-selection--multiple {
            height: auto !important;
        }
    </style>
@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <div class="modal fade" tabindex="-1" role="dialog" id="modal_grupos">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{!! trans('elearning::alumnos/admin_lang.grupos') !!}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="respuesta_grupos"></div>
                <div class="modal-footer">
                    <a id="cancel" data-dismiss="modal" class="btn btn-default pull-left">{{ trans('general/admin_lang.close') }}</a>
                    <a id="sender" class="btn btn-info pull-right" href="javascript:senderForm();">{{ trans('general/admin_lang.save') }}</a>
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
                    <h4 class="modal-title">{{ trans('elearning::alumnos/admin_lang.importar_alumnos_header') }}</h4>
                </div>
                <div class="modal-body">
                    <form id="import_alumnos_form">
                        <div class="row form-group">
                            <div class="col-md-12">
                                {!! Form::label('grupo_id', trans('elearning::alumnos/admin_lang.grupos'), array('class' => 'control-label')) !!}
                                <select class="form-control select2" id="grupo_id" name="grupo_id" style="width: 100%;">
                                    {{-- Sólo si tengo permiso para ver todos los alumnos puedo NO asignar grupo --}}
                                    @if(auth()->user()->can("admin-alumnos-all"))
                                        <option value="">{{ trans('elearning::alumnos/admin_lang.sin_grupo') }}</option>
                                    @endif
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" class="form-control" id="nombrefichero" readonly>
                            <span class="input-group-btn">
                                <div class="btn btn-primary btn-file">
                                    {{ trans('elearning::alumnos/admin_lang.search_file') }}
                                    {!! Form::file('import_alumnos',array('id'=>'import_alumnos', 'multiple'=>false)) !!}
                                </div>
                            </span>
                        </div>
                    </form>
                    <a href="{{ asset("/assets/data/plantilla_importacion_alumnos.xlsx") }}" target="_blank">
                        {{ trans('elearning::alumnos/admin_lang.template') }}
                    </a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans("general/front_lang.cerrar") }}</button>
                    <button id="import_submit" type="button" class="btn btn-default">
                        {{ trans('elearning::alumnos/admin_lang.importar_alumnos') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="box   box-primary">
        <div class="box-header with-border"><h3 class="box-title">{{ trans("elearning::alumnos/admin_lang.fitros_y_exportacion") }}</h3></div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <form name="frmFilter" id="frmFilter" method="post" action="{{ url("admin/alumnos/saveFilter") }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="grupos" class="col-sm-2 control-label pull-left">{{ trans("elearning::alumnos/admin_lang.grupos") }}</label>
                                <div class="col-sm-6">
                                    <select multiple name="grupos[]" class="form-control select2 select2-selection--multiple" data-placement="Grupos">
                                        @foreach($grupos as $grupo)
                                            <option value="{{ $grupo->id }}" @if (in_array($grupo->id, $grupos_seleccionados)) selected @endif>{{ $grupo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info" style="margin-top: 6px;">{{ trans('elearning::alumnos/admin_lang.filtrar') }}</button>
                        <a href="{{url('admin/alumnos/clearFilter')}}" class="btn btn-danger" style="margin-top: 6px;">{{ trans('elearning::alumnos/admin_lang.limpiar') }}</a>
                        <a href="{{url('admin/alumnos/generateExcel')}}" class="btn btn-primary" style="margin-top: 6px; margin-left: 50px;"><i class="fa fa-file-excel-o" style="margin-right: 10px" aria-hidden="true"></i> {{ trans('elearning::alumnos/admin_lang.exportar_usuarios') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Default box -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('elearning::alumnos/admin_lang.listado_alumnos') }}</h3>
                </div>

                <div class="box-body">

                    @if(auth()->user()->can("admin-alumnos-all"))
                        <div class="clearfix"></div>
                        <button id="Listado" class="mb-xs mt-xs mr-xs btn btn-info" data-value="@if(Session::has('todo_los_alumnos')){{"1"}}@else{{"0"}}@endif">
                            @if (Session::has('todo_los_alumnos'))
                                {{ trans('elearning::alumnos/admin_lang.ver_mis_alumnos') }}
                            @else
                                {{ trans('elearning::alumnos/admin_lang.ver_todos') }}
                            @endif
                        </button>
                    @endif

                    {{-- Si puedo ver todos los alumnos o solo mis alumnos pero teniendo al menos un grupo
                        donde asignarlos  --}}
                    @if(auth()->user()->can("admin-alumnos-all") || (
                        !auth()->user()->can("admin-alumnos-all") && sizeof($grupos)>0))
                        <a id="importar_alumnos" href="{{ url('admin/alumnos/import') }}" style="margin-left: 10px;" class="btn bg-purple">
                            <i class="fa fa-upload" aria-hidden="true"></i> {{ trans('elearning::alumnos/admin_lang.import') }}
                        </a>
                    @endif

                    @if(Auth::user()->can("admin-alumnos-create"))
                        <a href="{{ url('admin/alumnos/create') }}" class="btn btn-success pull-right">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i> {{ trans('elearning::alumnos/admin_lang.nuevo') }}
                        </a>
                    @endif
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table_alumnos" class="table table-bordered table-striped table-responsive" style="width: 99.99%;" aria-hidden="true">
                        <thead>
                        <tr>
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
                        </tr>
                        </tfoot>
                    </table>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>

    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans('elearning::alumnos/admin_lang.export') }}</h3></div>

                <div class="box-body">

                    <a href="{{ url('admin/alumnos/generateExcel') }}" class="btn btn-app">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        {{ trans('elearning::alumnos/admin_lang.exportar_usuarios') }}
                    </a>

                </div>

            </div>

        </div>
    </div>

@endsection

@section("foot_page")
    <!-- DataTables -->
    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>

    <!-- page script -->
    <script type="text/javascript">
        var oTable = '';
        var selected = [];

        $(function () {
            $(".select2").select2();

            $("#import_alumnos").change(function () {
                $("#nombrefichero").val($(this)[0].files[0].name);
            });

            $("#importar_alumnos").click(function (e) {
                e.preventDefault();



                $("#modal_import").modal({
                    backdrop: "static", //remove ability to close modal with click
                    keyboard: false, //remove option to close with keyboard
                    show: true //Display loader!
                });

                $("#import_submit").click(function () {

                    var btnEnviar = $("#import_submit");
                    btnEnviar.addClass('disabled');
                    btnEnviar.prepend('<i class="fa fa-spinner fa-spin" aria-hidden="true">&nbsp;</i>');

                    let file = $("#import_alumnos")[0].files[0];

                    if (/^xls(x)?$/.test(file.name.split('.').pop())) {
                        var formData = new FormData();
                        formData.append("plantilla", file);
                        formData.append("_token", "{{ csrf_token() }}");
                        formData.append("group_id", $("#grupo_id").val());

                        $("#import_submit").attr("disabled", true);

                        $.ajax({
                            url: "{{ url("admin/alumnos/importAlumnos") }}",
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
                                if(data.existentes.length) {
                                    alert("{{ trans("elearning::alumnos/admin_lang.alumnos_existentes") }}:\n" + data.existentes.join(", "));
                                } else {
                                    $("#modal_import").modal("hide");
                                    $("#import_submit").attr("disabled", false);
                                    oTable.ajax.reload();
                                }
                            } else {
                                alert("{{ trans("elearning::alumnos/admin_lang.error_import") }}");
                            }
                        });
                    } else {
                        alert("{{ trans("elearning::alumnos/admin_lang.not_valid") }}");
                    }
                });

            });

            oTable = $('#table_alumnos').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "processing": true,
                "responsive": true,
                "serverSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/alumnos/getData') }}",
                    type        : "POST"
                },
                order: [[ 1, "asc" ]],
                columns: [
                    {
                        orderable       : false,
                        searchable      : false,
                        width          : '20px',
                        data: 'active',
                    },
                    {
                        "title"         : "{!! trans('elearning::alumnos/admin_lang.nombre') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'first_name', name: 'user_profiles.first_name',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::alumnos/admin_lang.apellidos') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'last_name', name: 'user_profiles.last_name',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('elearning::alumnos/admin_lang.email') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'email', name: 'users.email',
                        width          : '200px'
                    },
                    {
                        "title"         : "{!! trans('elearning::alumnos/admin_lang.username') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'username', name: 'users.username',
                        width          : '200px'
                    },
                    {
                        "title"         : "{!! trans('elearning::alumnos/admin_lang.grupos') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'grupos', name: 'users.grupos',
                        width          : '200px'
                    },
                    {
                        "title"         : "{!! trans('elearning::alumnos/admin_lang.acciones') !!}",
                        orderable       : false,
                        searchable      : false,
                        width          : '150px',
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
                oLanguage:
                {!! json_encode(trans('datatable/lang')) !!}

            });

            var state = oTable.state.loaded();
            $('tfoot th',$('#table_alumnos')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_alumnos')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" style="width: 100%;" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_alumnos').on( 'keyup change','tfoot input', function (e) {
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

            $("#confirmModalLabel").html("{{ trans('elearning::alumnos/admin_lang.user_warning_title') }}");
            $("#confirmModalBody").html("{{ trans('elearning::alumnos/admin_lang.user_delete_question') }}");
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
                        if(data.success) {
                            $("#modal_alert").addClass('modal-success');
                            $("#alertModalHeader").html('{{ trans('elearning::alumnos/admin_lang.borrado') }}');
                            $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                            oTable.ajax.reload(null, false);
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('elearning::alumnos/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }
            });
            return false;
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

        $('body').on('click', '.acciones_grupos', function(e) {

            e.preventDefault();

            $("#modal_grupos").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
            });

            gruposAlumno($(this).data('id'));

        });

        function gruposAlumno(ord) {

            $('#respuesta_grupos').html('<span class="fa fa-spinner fa-spin text-center"></span>');

            $.ajax({
                url     : "{{ url("admin/alumnos/getGrupos") }}",
                type    : 'POST',
                data: {
                    orden: ord

                },
                headers: {
                    'X-CSRF-TOKEN':"{{ csrf_token() }}"
                },
                success : function(data) {
                    $('#respuesta_grupos').html(data);
                }
            });
        }

        function senderForm() {

            $("#sender").html("<div class='overlay'><i class='fa fa-refresh fa-spin'></i> {{ trans('elearning::asignaturas/admin_lang.save_in') }}</div>");
            $("#sender").addClass("disabled");

            var form=$("#formDataGrupos");

            $.ajax({
                url     : "{{ url("admin/alumnos/storeGrupos") }}",
                type    : 'POST',
                data:form.serialize(),
                headers: {
                    'X-CSRF-TOKEN':"{{ csrf_token() }}"
                },
                success : function(data) {
                    $('#modal_grupos').modal('hide');
                    if (data=='OK') {

                        $("#sender").text('{{ trans('general/admin_lang.save') }}');
                        $("#sender").removeClass("disabled");

                        oTable.ajax.reload(null, false);
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                }
            });
        }

    </script>
@stop
