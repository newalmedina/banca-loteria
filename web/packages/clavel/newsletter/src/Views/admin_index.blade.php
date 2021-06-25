@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.modals')

    <!-- Vista previa -->
    <div class="modal modal-preview fade in" id="bs-modal-preview">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('Newsletter::admin_lang.preview') }}</h4>
                </div>
                <div id="content-preview" class="modal-body" style="padding: 0; margin: 0;">
                    <input type="hidden" id="newsletter_id" name="newsletter_id" value="">
                    <div class="selector" style="padding: 10px 20px;">
                        <div class="row">
                            <div class="col-md-2">
                                <strong>{{ trans("Newsletter::admin_lang_template.select_idioma") }}</strong>
                            </div>
                            <div class="col-md-10">
                                <select name="idioma" id="idioma" class="form-control">
                                    @foreach($idiomas as $idioma)
                                        <option value="{{ $idioma->code }}">{{ $idioma->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="preview_page"></div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <!-- Default box -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("Newsletter::admin_lang.listado_newsletter") }}</h3>
                </div>

                <div class="box-body">
                    @if(Auth::user()->can("admin-newsletter-create"))
                        <a href="{{ url('admin/newsletter/create') }}" class="btn btn-success pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i> {{ trans('Newsletter::admin_lang.nuevo_newsletter') }}</a>
                    @endif
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table_newsletter" class="table table-bordered table-striped" aria-hidden="true">
                        <thead>
                        <tr>
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

@endsection

@section("foot_page")
    <!-- DataTables -->
    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>

    <!-- page script -->
    <script type="text/javascript">
        var oTable = '';
        var selected = [];
        var newsletter_send_type = -1;

        $(function () {
            oTable = $('#table_newsletter').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "processing": true,
                "responsive": true,
                "serverSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/newsletter/list') }}",
                    type        : "POST"
                },
                order: [[ 0, "asc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang.name_newsletter') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'name', name            : 'n.name',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang.subject') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'subject',name            : 'n.subject',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang.acciones') !!}",
                        orderable       : false,
                        searchable      : false,
                        sWidth          : '150px',
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
            $('tfoot th',$('#table_newsletter')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_newsletter')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_newsletter').on( 'keyup change','tfoot input', function (e) {
                oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
            });

            $("#idioma").change(function(e) {
                loadIdioma();
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
                        $("#modal_alert").addClass('modal-success');
                        $("#alertModalHeader").html("Borrado de newsletter");
                        $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                        $("#modal_alert").modal('toggle');
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

        function showPreview(id) {
            $("#newsletter_id").val(id);
            $("#preview_page").html('<div id="spinner" class="overlay" style="text-align: center"><i class="fa fa-refresh fa-spin" style="font-size: 64px;" aria-hidden="true"></i></div>');
            $('#bs-modal-preview').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            url ="{{ url("admin/newsletter/preview/") }}/" + id + "/" + $("#idioma").val();
            $("#preview_page").load(url);
        }

        function loadIdioma() {
            $("#preview_page").load("{{ url("admin/newsletter/preview/") }}/" + $("#newsletter_id").val() + "/" + $("#idioma").val());
        }

    </script>

@stop
