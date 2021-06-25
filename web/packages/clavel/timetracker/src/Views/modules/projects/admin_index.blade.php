@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet"
          type="text/css"/>

@stop

@section('breadcrumb')
    <li class="active">{{ trans('timetracker::projects/admin_lang.list') }}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <!-- Modal para la Modificación de estado de proyectos -->
    <div id="modalEstadoProyecto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalEstadoProyecto">
        <div class="modal-dialog modal-lg">
            <div id="content_block" class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('timetracker::projects/admin_lang.project_state') }}</h4>
                </div>
                <div id="container_project_state">

                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal para la Modificación de estado de proyectos -->

    <!-- Modal para la Modificación de tipo de proyectos -->
    <div id="modalTipoProyecto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalTipoProyecto">
        <div class="modal-dialog modal-lg">
            <div id="content_block" class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('timetracker::projects/admin_lang.project_type') }}</h4>
                </div>
                <div id="container_project_type">

                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal para la Modificación de tipo de proyectos -->


    <div class="row">
        <div class="col-xs-12">

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans("timetracker::projects/admin_lang.list") }}</h3>
                </div>

                <div class="box-body">
                    <div class="margin">


                        <div class="box   box-primary">
                            <div class="box-header with-border"><h3
                                    class="box-title">{{ trans('timetracker::projects/admin_lang.filtros') }}</h3></div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <form name="frmFilter" id="frmFilter" method="post"
                                              action="{{  url('admin/projects/saveFilter') }}">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <div class="row">
                                                <div class=" col-md-6">
                                                    <div class="form-group">
                                                        <label for="grupos"
                                                               class="col-sm-4 control-label pull-left">{{ trans("timetracker::projects/admin_lang.active") }}</label>
                                                        <div class="col-sm-8">
                                                            <select name="activos" class="form-control">
                                                                <option value="" @if($activos== '') selected @endif>
                                                                    (Todos los Registros)
                                                                </option>
                                                                <option value="1" @if($activos== '1') selected @endif>
                                                                    Activos
                                                                </option>
                                                                <option value="0" @if($activos== '0') selected @endif>No
                                                                    Activos
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="grupos"
                                                           class="col-sm-4 control-label pull-left">{{ trans("timetracker::projects/admin_lang.invoiced") }}</label>
                                                    <div class="col-sm-8">

                                                        <select multiple name="facturado[]" id="facturados"
                                                                class="form-control select2 select2-selection--multiple"
                                                                data-placeholder="Facturado">
                                                            @foreach($facturados as $key=>$value)
                                                                <option value="{{ $value->id }}" @if(in_array($value->id,$facturado)) selected @endif>{{ $value->name }}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>


                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="grupos"
                                                           class="col-sm-4 control-label pull-left">{{ trans("timetracker::projects/admin_lang.state") }}</label>
                                                    <div class="col-sm-8">
                                                        <select multiple name="estado[]" id="estados"
                                                                class="form-control select2 select2-selection--multiple"
                                                                data-placeholder="Estado">
                                                            @foreach($estados as $key=>$value)
                                                                <option value="{{ $value->slug }}" @if(in_array($value->slug,$estado)) selected @endif>{{ $value->name }}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="grupos"
                                                           class="col-sm-4 control-label pull-left">{{ trans("timetracker::projects/admin_lang.type") }}</label>
                                                    <div class="col-sm-8">
                                                        <select multiple name="tipo[]" id="tipos"
                                                                class="form-control select2 select2-selection--multiple"
                                                                data-placeholder="Tipo">
                                                            @foreach($tipos as $key=>$value)
                                                                <option value="{{ $value->id }}" @if(in_array($value->id,$tipo)) selected @endif>{{ $value->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="grupos"
                                                           class="col-sm-4 control-label pull-left">{{ trans("timetracker::projects/admin_lang.responsable") }}</label>
                                                    <div class="col-sm-8">

                                                        <select multiple name="responsable[]" id="responsables"
                                                                class="form-control select2 select2-selection--multiple"
                                                                data-placeholder="Responsable">
                                                            @foreach($responsables as $key=>$value)
                                                                <option value="{{ $value->id }}" @if(in_array($value->id,$responsable)) selected @endif>{{ $value->nombre }}</option>
                                                            @endforeach


                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-info"
                                                    style="margin-top: 6px;">{{ trans('timetracker::projects/admin_lang.filtrar') }}</button>
                                            <a href="{{  url('admin/projects/clearFilter') }}" class="btn btn-danger"
                                               style="margin-top: 6px;">{{ trans('timetracker::projects/admin_lang.limpiar') }}</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 margin-bottom-10">
                            @if(Auth::user()->can("admin-projects-create"))

                                <div class="col-sm-11 margin-bottom-10">
                                    <a href="{{ url('admin/projects/create') }}"
                                       class="btn btn-primary">{{ trans('timetracker::projects/admin_lang.new') }}</a>
                                </div>

                            @endif
                            @if(Auth::user()->can("admin-projects-update"))
                                @if(!$show_historified)
                                    <a href="{{ url('admin/projects/historified') }}"
                                       class="btn btn-success pull-right">
                                        {{ trans('timetracker::projects/admin_lang.see_historified') }}
                                    </a>
                                @else
                                    <a href="{{ url('admin/projects/historified') }}"
                                       class="btn btn-success pull-right">
                                        {{ trans('timetracker::projects/admin_lang.hide_historified') }}
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                    <table id="table_projects" class="table table-bordered table-hover table-responsive" style="width: 99.99%;" aria-hidden="true">
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

                <div class="box-header"><h3
                        class="box-title">{{ trans("timetracker::projects/admin_lang.export") }}</h3></div>

                <div class="box-body">

                    <a href="#" onclick="exportExcel('{{ url('admin/projects/generateExcel') }}')" class="btn btn-app">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        {{ trans('timetracker::projects/admin_lang.export_excel') }}
                    </a>

                </div>

            </div>

        </div>
    </div>

@endsection

@section("foot_page")

    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>

    <script type="text/javascript">
        var oTable = '';
        var selected = [];

        $(document).ready(function() {
            $("#estados").select2();
            $("#tipos").select2();
            $("#facturados").select2();
            $("#responsables").select2();

            oTable = $('#table_projects').DataTable({
                "stateSave": true,
                "stateDuration": 120,
                "bProcessing": true,
                "bServerSide": true,
                "pageLength": 100,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url: "{{ url('admin/projects/getData') }}",
                    type: "POST"
                },
                order: [[4, "asc"]],
                columns: [
                    {
                        "title": "{!! trans('timetracker::projects/admin_lang.active') !!}",
                        orderable: true,
                        searchable: false,
                        data: 'active',
                        name: 'active',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('timetracker::projects/admin_lang.invoiced') !!}",
                        orderable: true,
                        searchable: false,
                        data: 'invoiced',
                        name: 'invoiced',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('timetracker::projects/admin_lang.state') !!}",
                        orderable: true,
                        searchable: false,
                        data: 'slug_state',
                        name: 'projects.slug_state',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('timetracker::projects/admin_lang.type') !!}",
                        orderable: true,
                        searchable: false,
                        data: 'project_type_id',
                        name: 'projects.project_type_id',
                        sWidth: '50px'
                    },
                    {
                        "title": "{!! trans('timetracker::projects/admin_lang.name') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'name',
                        name: 'projects.name',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('timetracker::projects/admin_lang.order_number') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'order_number',
                        name: 'projects.order_number',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('timetracker::projects/admin_lang.responsable') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'responsable_id',
                        name: 'projects.responsable_id',
                        sWidth: ''
                    },

                    {
                        "title": "{!! trans('timetracker::projects/admin_lang.expire_at') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'expire_at',
                        name: 'projects.expire_at',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('timetracker::projects/admin_lang.customer') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'customer_name',
                        name: 'customers.name',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('timetracker::projects/admin_lang.actions') !!}",
                        orderable: false,
                        searchable: false,
                        sWidth: '140px',
                        data: 'actions'
                    }
                ],
                "rowCallback": function (row, data, index) {
                    if (data.alert == "1") {
                        $('td', row).css('background-color', '#F0F3BD');
                    }
                },
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
            $('tfoot th', $('#table_projects')).each(function (colIdx) {
                var title = $('tfoot th', $('#table_projects')).eq($(this).index()).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if (state) defecto = state.columns[colIdx].search.search;

                    $(this).html('<input type="text" style="width: 100%;" class="form-control input-small input-inline" placeholder="' + oTable.context[0].aoColumns[colIdx].title + ' ' + title + '" value="' + defecto + '" />');
                }
            });

            $('#table_projects').on('keyup change', 'tfoot input', function (e) {
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

        function changeInvoiced(url) {
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

        function historifyElement(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('timetracker::projects/admin_lang.historify_question') }}");
            strBtn += '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn += '<button type="button" class="btn btn-primary" onclick="javascript:historify(\'' + url + '\');">{{ trans('timetracker::projects/admin_lang.historify') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function recoverElement(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('timetracker::projects/admin_lang.recover_question') }}");
            strBtn += '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn += '<button type="button" class="btn btn-primary" onclick="javascript:recover(\'' + url + '\');">{{ trans('timetracker::projects/admin_lang.recover') }}</button>';
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

        function historify(url) {
            $.ajax({
                url: url,
                type: 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                data: {_method: 'post'},
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

        function recover(url) {
            $.ajax({
                url: url,
                type: 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                data: {_method: 'post'},
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

        function showPreview(url) {
            $("#content-preview").html('<div id="spinner" class="overlay" style="text-align: center"><i class="fa fa-refresh fa-spin" style="font-size: 64px;" aria-hidden="true"></i></div>');
            $('#bs-modal-preview').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            $("#content-preview").load(url);
        }

        function openProjectState(id) {
            var url = "{{ url('admin/projects/stateProject') }}/" + id;
            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#container_project_state").html('<div id="spinner" class="overlay" style="' + style + '"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
            $("#modalEstadoProyecto").modal("toggle");
            $("#container_project_state").load(url);
        }

        function openProjectType(id) {
            var url = "{{ url('admin/projects/typeProject') }}/" + id;
            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#container_project_type").html('<div id="spinner" class="overlay" style="' + style + '"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
            $("#modalTipoProyecto").modal("toggle");
            $("#container_project_type").load(url);
        }

        function exportExcel(url) {
            var columns = [];
            var settings = oTable.settings()[0];
            settings.aoColumns.forEach(function(aoColumn) {
                columns[aoColumn.idx]=
                    {
                    'name': aoColumn.name,
                    'value': ''
                    };
            });

            var table = document.getElementById('table_projects'),
                footRow = table.getElementsByTagName('tfoot'),
                i, j, cells, searchValue;

            if ( footRow.length === 1) {
                cells = footRow[0].getElementsByTagName('th');
                for (i = 0, j = cells.length; i < j; ++i) {
                    cell = cells[i].getElementsByTagName('input');
                    if(cell.length > 0) {
                        searchValue = cell[0].value;
                        columns[i].value = searchValue;
                    }
                }
            }

            var search = [];
            search.push( {
                'general': oTable.search()
            });
            search.push( {
                'columns': columns
            });

            window.location.href = encodeURI(url + '/' + JSON.stringify(search));
            return false;
        }


    </script>
@stop
