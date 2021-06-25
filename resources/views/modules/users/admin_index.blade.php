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
<div id="app">
    @include('admin.includes.modals')

    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <info-box color="bg-aqua"
                icon="fa-user"
                title="Total usuarios"
                :value="totalUsuarios"
                v-on:click-data='clickData'>
                Total de usuarios
            </info-box>
        </div>
        <!-- /.col -->
        <div class="col-md-4 col-sm-6 col-xs-12">
            <info-box color="bg-green"
                icon="fa-user-plus"
                title="Nuevos 30 días"
                :value="nuevosUsuarios">
                Nuevos usuarios últimos 30 días
            </info-box>
        </div>
        <!-- /.col -->
        <div class="col-md-4 col-sm-6 col-xs-12">
            <info-box color="bg-yellow"
                icon="fa-users"
                title="Activos última hora"
                :value="activosUsuarios">
                Usuarios activos en la última hora
            </info-box>
        </div>
    </div>
    <!-- /.row -->

    <!-- Default box -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("users/lang.listado_usuarios") }}</h3>
                </div>

                <div class="box-body">
                    @if(Auth::user()->can("admin-users-create"))
                        <a href="{{ url('admin/users/create') }}" class="btn btn-success pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i> {{ trans('users/lang.nueva_usuario') }}</a>
                    @endif
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table_users" class="table table-bordered table-striped" aria-hidden="true">
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

                <div class="box-header"><h3 class="box-title">{{ trans("users/lang.export") }}</h3></div>

                <div class="box-body">

                    <a href="{{ url('admin/users/generateExcel') }}" class="btn btn-app">
                        <i class="fa fa-file-excel-o"></i>
                        {{ trans('users/lang.exportar_usuarios') }}
                    </a>

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
    <script src="{{ asset("/assets/admin/vendor/axios/axios.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/vue/vue.min.js") }}"></script>

    <!-- page script -->
    <script type="text/javascript">
        var oTable = '';

        $(function () {
            oTable = $('#table_users').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "processing": true,
                "responsive": true,
                "serverSide": true,
                "pageLength": 100,
                ajax: {
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url         : "{{ url('admin/users/list') }}",
                    type        : "POST"
                },
                order: [[ 2, "asc" ]],
                columns: [
                    {
                        orderable       : false,
                        searchable      : false,
                        width          : '20px',
                        data: 'active',
                    },
                    {
                        "title"         : "{!! trans('users/lang.online') !!}",
                        orderable       : false,
                        searchable      : false,
                        width          : '20px',
                        data: 'online',
                        name: 'online'
                    },
                    {
                        "title"         : "{!! trans('users/lang.nombre_usuario') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'first_name', name: 'user_profiles.first_name',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('users/lang._APELLIDOS_USUARIO') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'last_name', name: 'user_profiles.last_name',
                        width          : ''
                    },
                    {
                        "title"         : "{!! trans('users/lang.email_usuario') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'email', name: 'users.email',
                        width          : '200px'
                    },
                    {
                        "title"         : "{!! trans('users/lang.username') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'username', name: 'users.username',
                        width          : '200px'
                    },
                    {
                        "title"         : "{!! trans('users/lang.acciones') !!}",
                        orderable       : false,
                        searchable      : false,
                        width          : '130px',
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
            $('tfoot th',$('#table_users')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_users')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_users').on( 'keyup change','tfoot input', function (e) {
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

            $("#confirmModalLabel").html("{{ trans('users/lang.user_warning_title') }}");
            $("#confirmModalBody").html("{{ trans('users/lang.user_delete_question') }}");
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
                            $("#alertModalHeader").html("Borrado de usuario");
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
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('users/lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }
            });
            return false;
        }

        function suplantarElement(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('users/lang.user_warning_title') }}");
            $("#confirmModalBody").html("{{ trans('users/lang.user_suplantar_question') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<a class="btn btn-primary" href="'+url+'">{{ trans('users/lang.suplantar_item') }}</a>';

            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        Vue.component('info-box', {
            props: [
                'color',
                'icon',
                'title',
                'value'
            ],
            data: {
                colorMe: '#ff00ff'
            },
            methods:{
                notifyClick: function () {
                    this.$emit('click-data', this.colorMe);
                }
            },
            mounted() {
                this.colorMe = this.color;
            },
            template: `
                <div class="info-box">
                    <span class="info-box-icon" :class="color">
                        <i class="fa" :class="icon"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">@{{ title }}</span>
                        <span v-on:mouseover="notifyClick" class="info-box-number">
                            @{{ value }}
                        </span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            <slot></slot>
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            `
        });


        var app = new Vue({
            el: '#app',
            data: {
                totalUsuarios: 0,
                nuevosUsuarios: 0,
                activosUsuarios: 0
            },
            methods:{
                clickData: function (color) {
                    //app.totalUsuarios += 1;
                    //alert(color);
                }
            },

            mounted() {
                axios.get('/admin/users/userStats')
                    .then(function (response) {
                        // handle success
                        app.totalUsuarios = response.data.total;
                        app.nuevosUsuarios = response.data.nuevos;
                        app.activosUsuarios = response.data.activos;
                    })
                    .catch(function (error) {
                        // handle error
                        console.log(error);
                    })
                    .then(function () {
                        // always executed
                    });
            }

        })
    </script>
@stop
