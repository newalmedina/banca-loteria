@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

    <style>
        .spinner {
            text-align: center;
        }
    </style>
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/newsletter-campaings") }}">{{ trans('Newsletter::admin_lang_campaigns.newsletter-campaigns') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-bookmark-o" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans("Newsletter::admin_lang_campaigns.Pendientes") }}</span>
                    <span class="info-box-number">{{ $stats[0]["count"] }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $stats[0]["perc"] }}%"></div>
                    </div>
                    <span class="progress-description">{{ $stats[0]["perc"] }}%</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans("Newsletter::admin_lang_campaigns.correctamente") }}</span>
                    <span class="info-box-number">{{ $stats[1]["count"] }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $stats[1]["perc"] }}%"></div>
                    </div>
                    <span class="progress-description">{{ $stats[1]["perc"] }}%</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-comments-o" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans("Newsletter::admin_lang_campaigns.Errores") }}</span>
                    <span class="info-box-number">{{ $stats[2]["count"] }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $stats[2]["perc"] }}%"></div>
                    </div>
                    <span class="progress-description">{{ $stats[2]["perc"] }}%</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("Newsletter::admin_lang_campaigns.listado_enviados") }}</h3>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table_list_newsletter" class="table table-bordered table-striped" aria-hidden="true">
                        <thead>
                        <tr>
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
                        </tr>
                        </tfoot>
                    </table>

                </div>
                <!-- /.box-body -->

                <div class="clearfix"></div>

            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 style="margin-bottom: 0; margin-top: 0">
                        <i class="fa fa-globe" aria-hidden="true"></i> {{ $campaign->name }}
                        <small class="pull-right" style="color: #FFF;">{{ $campaign->updated_at }}</small>
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">

                            @if($campaign->is_scheduled=='1')
                                <div class="alert alert-info">
                                    <h4><i class="icon fa fa-clock-o" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang_campaigns.programada") }}</h4>
                                    <p>{{ trans("Newsletter::admin_lang_campaigns.programada_info") }} {{ $campaign->scheduled_for }}</p>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table" aria-hidden="true">
                                    <tr>
                                        <th scope="col" style="width:50%">{{ trans("Newsletter::admin_lang_campaigns.newsletter") }}:</th>
                                        <td>{{ $campaign->newsletter->name }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="col" style="width:50%">{{ trans("Newsletter::admin_lang_campaigns.mailing_list") }}:</th>
                                        <td>
                                            <?php $first_loop = true; ?>
                                            @foreach($campaign->mailingList as $list)
                                                @if(!$first_loop), @endif
                                                {{ $list->name }}
                                                <?php $first_loop =false; ?>
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="col">{{ trans("Newsletter::admin_lang_campaigns.suscriptores") }}:</th>
                                        <td>{{ $campaign->recipients()->count() }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="col">{{ trans("Newsletter::admin_lang_campaigns.Delivered") }}:</th>
                                        <td>{{ $campaign->sent_at_date_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="col">{{ trans("Newsletter::admin_lang_campaigns.Subject") }}:</th>
                                        <td>{{ $campaign->newsletter->subject }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <a href="{{ url('/admin/newsletter-campaigns') }}" class="btn btn-default pull-right">{{ trans('general/admin_lang.cancelar') }}</a>
                        </div>
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

    <script>
        var oTable = '';

        $(function () {
            oTable = $('#table_list_newsletter').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "processing": true,
                "responsive": true,
                "serverSide": true,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/newsletter-campaigns/sent_list/'.$campaign->id) }}",
                    type        : "POST"
                },
                order: [[ 1, "asc" ]],
                columns: [
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.estado') !!}",
                        orderable       : true,
                        searchable      : false,
                        data            : 'is_sent',
                        name            : 'is_sent',
                        sWidth          : '60px'
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.first_name') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'first_name',
                        name            : "user_profiles.first_name",
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.last_name') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'last_name',
                        name            : "user_profiles.last_name",
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('Newsletter::admin_lang_campaigns.email') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'email',
                        name            : 'users.email',
                        sWidth          : ''
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
            $('tfoot th',$('#table_list_newsletter')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_list_newsletter')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_list_newsletter').on( 'keyup change','tfoot input', function (e) {
                oTable
                        .column( $(this).parent().index()+':visible' )
                        .search( this.value )
                        .draw();
            });
        });
    </script>
@stop
