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


    <!-- Envío newsletter -->
    <div class="modal modal-preview fade in" id="bs-modal-send">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ trans('Newsletter::admin_lang_campaigns.campaign_preparing') }}</h4>
                </div>
                <div id="content-send" class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div style="display: none;" id="content-send-temp">
        {!! Form::open(array('role' => 'form','id'=>'frmPrepareCampaign', 'method'=>'POST')) !!}


        <div class="row">
            <div class="col-md-12" style="padding-bottom: 10px;">
                <label class="radio-inline"><input type="radio" class="newsletter_send_type" checked="checked" name="newsletter_send_type" value="1" />{{ trans('Newsletter::admin_lang.send_newsletter_subscriptors') }}</label>
            </div>
            <div class="col-md-12" style="padding-bottom: 10px;">
                <label class="radio-inline"><input type="radio" class="newsletter_send_type" name="newsletter_send_type" value="0" />{{ trans('Newsletter::admin_lang.send_all_users') }}</label>
            </div>
            <div class="col-md-12" style="padding-bottom: 10px;">
                <label class="radio-inline"><input type="radio" class="newsletter_send_type" name="newsletter_send_type" value="2" />{{ trans('Newsletter::admin_lang.send_roles') }}</label>
            </div>
            <div class="col-md-5 role_selection_send_newsletter" style="display: none; padding-left: 40px;">
                <select multiple class="form-control select2 newsletter_roles" name="newsletter_roles[]" data-placeholder="Seleccionar rol" style="width: 100%;">
                    <option value="">Selecciona un rol</option>
                    @foreach($roles as $value)
                        <option value="{{ $value->id }}">{{ $value->display_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="margin-top: 15px;" class="row">
            <div class="col-md-12">
                <button aria-label="Close" data-dismiss="modal" class='btn btn-default'>{{ trans('general/admin_lang.cancelar') }}</button>
                <button type="button" class="btn btn-info pull-right newsletter-send-button">{{ trans('Newsletter::admin_lang.send') }}</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    <!-- Default box -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("Newsletter::admin_lang_campaigns.newsletter-campaigns") }}</h3>
                </div>

                <div class="box-body">
                    @if(Auth::user()->can("admin-newsletter-campaigns-create"))
                        <a href="{{ url('admin/newsletter-campaigns/create') }}" class="btn btn-success pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i> {{ trans('Newsletter::admin_lang_campaigns.new_campaign') }}</a>
                    @endif
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table_campaigns" class="table table-bordered table-striped" aria-hidden="true">
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
            oTable = $('#table_campaigns').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "processing": true,
                "responsive": true,
                "serverSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/newsletter-campaigns/list') }}",
                    type        : "POST"
                },
                order: [[ 1, "asc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.status') !!}",
                        orderable       : false,
                        searchable      : false,
                        data            : 'status',
                        sWidth          : '40px'
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.campaign') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'name', name            : 'name',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.mailing_list') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'mailing_list', name            : 'mailing_list',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.newsletter') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'newsletter', name            : 'newsletter',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.screduled') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'scheduled_for', name            : 'scheduled_for',
                        sWidth          : '90px'
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.sent') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'sent_at', name            : 'sent_at',
                        sWidth          : '90px'
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.acciones') !!}",
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
            $('tfoot th',$('#table_campaigns')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_campaigns')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_campaigns').on( 'keyup change','tfoot input', function (e) {
                oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
            });

        });

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
                        $("#alertModalHeader").html("Borrado de campaign");
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

        function sendCampaign(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('Newsletter::admin_lang_campaigns.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('Newsletter::admin_lang_campaigns.send_question') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:confirmSendCampaign(\''+url+'\');">{{ trans('Newsletter::admin_lang_campaigns.send_now') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function confirmSendCampaign(url) {
            $.ajax({
                url     : url,
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                        $("#modal_alert").addClass('modal-success');
                        $("#alertModalHeader").html("Envio de campaña");
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

        function prepareNewsletterPopup(id) {
            $("#content-send").html($("#content-send-temp").html());

            $("#content-send input[type=radio][name=newsletter_send_type]").change(function(){
                newsletter_send_type = $(this).val();
                switch(newsletter_send_type){
                    case '2':
                        $("#content-send .role_selection_send_newsletter").slideDown();
                        $("#content-send .testing_send_newsletter").slideUp();
                        break;
                    case '3':
                        $("#content-send .testing_send_newsletter").slideDown();
                        $("#content-send .role_selection_send_newsletter").slideUp();
                        break;
                    default:
                        $("#content-send .testing_send_newsletter").slideUp();
                        $("#content-send .role_selection_send_newsletter").slideUp();
                        break;
                }
            });

            $("#content-send .newsletter_roles").select2();

            $("#content-send .newsletter-send-button").click(function() {
                $("#frmPrepareCampaign").attr("action", "{!! url("admin/newsletter-campaigns/") !!}/" + id + "{{ "/prepare" }}");
                $("#frmPrepareCampaign").submit();
            });

            $('#bs-modal-send').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });
        }

    </script>

@stop
