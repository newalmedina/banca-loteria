@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset("/assets/admin/vendor/dropzone/dropzone.min.css") }} ">

@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.errors')
    @include('admin.includes.success')
    @include('admin.includes.modals')


    <!-- Creación  -->
    <div class="modal modal-note fade in" id="bs-modal-note" style="padding-right: 17px;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('basic::media/admin_lang.new_folder') }}</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('url' => 'admin/media/dir/create', 'method' => 'POST', 'id' => 'formFolder')) }}
                    {!! Form::hidden('act_folder', "/", array('id' => 'act_folder')) !!}

                    <div class="form-group">
                        {!! Form::label('foldername', trans('basic::media/admin_lang.nameFolder'), array('class' => 'col-sm-3 control-label', 'readonly' => true)) !!}
                        <div class="col-md-9">
                            {!! Form::text('foldername', null, array('placeholder' => trans('basic::media/admin_lang.nameFolder_insertar'), 'class' => 'form-control', 'id' => 'foldername')) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <br clear="all">
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">{{ trans('general/admin_lang.close') }}</button>
                    <button onclick="javascript:$('#formFolder').submit();" class="btn btn-default" type="button">{{ trans('general/admin_lang.save') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="row">
        <div class="col-md-3">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("basic::media/admin_lang.folders") }}</h3>
                </div>
                <div class="box-body" style="max-height: 75vh; overflow-y: auto;">
                    <ul class="nav nav-pills nav-stacked">
                        <li id="li_root" class="active list_folder">
                            <a href="javascript:changeFolder('/', 'root');">
                                <i id="fa_root" class="fa fa-folder-open text-warning fa-icon_f" aria-hidden="true"></i>/
                            </a>
                        </li>
                        @foreach($subfolders as $key => $value)
                            <li id="li_{{ $key }}" class="list_folder">
                                <a href="javascript:changeFolder('{{ $value }}', '{{$key}}');">
                                    {!! str_repeat('&nbsp;',(substr_count($value,'/')) * 5) !!}
                                    <i id="fa_{{ $key }}" class="fa fa-folder text-warning fa-icon_f" aria-hidden="true"></i>
                                    {{ basename($value) }}
                                    <button onclick="deletedir('{{ $value }}');" class="btn btn-xs btn-danger pull-right"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @if(Auth::user()->can("admin-media-create"))
                    <div class="box-footer clearfix">
                        <a class="btn btn-sm btn-primary btn-flat pull-left" href="javascript:createFolder();">{{ trans('basic::media/admin_lang.new_folder') }}</a>
                    </div>
                @endif
            </div>

        </div>

        <div class="col-md-9">

            @if(Auth::user()->can("admin-media-create"))

                <form id="id_dropzone" class="dropzone table-layout well mb0"
                      role="form" enctype="multipart/form-data" method="post"
                      style="box-shadow:none; border-radius: 0px; border-style:dotted; background-color:#e5e8ed;">
                    <input type="hidden" id="foldertoUp" value="/" name="foldertoUp">
                    <div class="fallback">
                        <input name="file" type="file" multiple />
                    </div>
                </form>
            @endif

            <div class="box ">

                <div class="box-body">
                    <br clear="all">
                    <table id="table_media" class="table table-bordered table-hover" aria-hidden="true">
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
                    <br clear="all">
                </div>
            </div>

        </div>
    </div>

@endsection

@section("foot_page")
    <!-- DataTables -->
    <script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/dropzone/dropzone.min.js") }}"></script>

    <script type="text/javascript">
        var oTable = '';
        var selected = [];
        var myDropzone;
        var positionPath = "root";

        Dropzone.autoDiscover = false;

        $(document).ready(function() {
            $("#id_dropzone").dropzone({
                url: "{{ url('admin/media/file/upload') }}",
                maxFilesize: {{ config("general.media.upload_max_file_size") }},
                addRemoveLinks : false,
                maxFiles: 2000,
                timeout: 3600000,
                dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-upload text-primary" style="font-size: 64px;"></i><br><br>{{ trans('basic::media/admin_lang.arrastra') }}</span></span><span>&nbsp&nbsp<h4 class="display-inline"> ({{ trans('basic::media/admin_lang.oclick') }})</h4></span>',
                dictResponseError: 'Error!',
                dictCancelUpload: '{{ trans('basic::media/admin_lang.dictCancelUpload') }}',
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}'
                },
                accept: function(file, done) {
                    $(".dz-message").css("display","none");
                    done();
                },
                success: function (file, response) {
                    thisDropzone = this;
                    if (thisDropzone.getQueuedFiles().length == 0 && thisDropzone.getUploadingFiles().length == 0) $(".dz-message").css("display","block");
                    oTable.ajax.url( '{{ url('admin/media/list/') }}/' + positionPath ).load();
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                }
            });


            oTable = $('#table_media').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "bProcessing": true,
                "bServerSide": true,
                ajax: {
                    beforeSend  : function(xhr) {xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}' )},
                    url         : "{{ url('admin/media/list/') }}/" + positionPath,
                    type        : "POST"
                },
                columns: [
                    {
                        "title"         : "{!! trans('basic::media/admin_lang.nombre_archivo') !!}",
                        orderable       : false,
                        searchable      : true,
                        data: 'original_filename', name            : 'original_filename',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('basic::media/admin_lang.propietario') !!}",
                        orderable       : true,
                        searchable      : true,
                        data: 'first_name', name            : 'first_name',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('basic::media/admin_lang.thumbnail') !!}",
                        orderable       : false,
                        searchable      : false,
                        data: 'path', name            : 'path',
                        sWidth          : '120px'
                    },
                    {
                        "title"         : "{!! trans('basic::media/admin_lang.filename') !!}",
                        orderable       : false,
                        searchable      : true,
                        data: 'filename', name            : 'filename',
                        sWidth          : ''
                    },
                    {
                        "title"         : "{!! trans('basic::media/admin_lang.mime') !!}",
                        orderable       : false,
                        searchable      : false,
                        data: 'mime', name            : 'mime',
                        sWidth          : '100px'
                    },
                    {
                        "title"         : "{!! trans('basic::media/admin_lang.mime') !!}",
                        orderable       : false,
                        searchable      : false,
                        data: 'size', name            : 'size',
                        sWidth          : '100px'
                    },
                    {
                        "title"         : "{!! trans('basic::media/admin_lang.acciones') !!}",
                        orderable       : false,
                        data: 'actions',
                        searchable      : false,
                        sWidth          : '75px'
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
            $('tfoot th',$('#table_media')).each( function (colIdx) {
                var title = $('tfoot th',$('#table_users')).eq( $(this).index() ).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if(state) defecto = state.columns[colIdx].search.search;

                    $(this).html( '<input type="text" class="form-control input-small input-inline" placeholder="'+oTable.context[0].aoColumns[colIdx].title+' '+title+'" value="'+defecto+'" />' );
                }
            });

            $('#table_media').on( 'keyup change','tfoot input', function (e) {
                oTable
                    .column( $(this).parent().index()+':visible' )
                    .search( this.value )
                    .draw();
            });

            $('#act_folder').val("/");
            $("#foldertoUp").val("/");
        });

        function deletedir(routedel) {
            var strBtn = "";

            routedel = routedel.replace(/\//g, '|!|');
            url = "{{ url('admin/media/dir/delete/') }}/" + routedel;

            $("#confirmModalLabel").html("{{ trans('basic::media/admin_lang.media_warning_title') }}");
            $("#confirmModalBody").html("{{ trans('basic::media/admin_lang.media_delete_question_dir') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfoDir(\''+url+'\');">{{ trans('general/admin_lang.borrar_item') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');

        }

        function deleteinfoDir(url) {
            window.location = url;
        }

        function createFolder() {
            $('#bs-modal-note').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });
        }

        function changeFolder(newFolder, routid) {
            $('#act_folder').val(newFolder);
            $("#foldertoUp").val(newFolder);
            positionPath = routid;
            $(".list_folder").removeClass("active");
            $("#li_" + routid).addClass("active");
            $(".fa-icon_f").removeClass("fa-folder-open");
            $(".fa-icon_f").removeClass("fa-folder");
            $(".fa-icon_f").addClass("fa-folder");
            $("#fa_" + routid).removeClass("fa-folder-open");
            $("#fa_" + routid).addClass("fa-folder-open");
            oTable.ajax.url( '{{ url('admin/media/list/') }}/' + routid ).load();
        }

        function deleteElement(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('basic::media/admin_lang.media_warning_title') }}");
            $("#confirmModalBody").html("{{ trans('basic::media/admin_lang.media_delete_question') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo(\''+url+'\');">{{ trans('general/admin_lang.borrar_item') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function optimizeVideo(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('basic::media/admin_lang.media_warning_title') }}");
            $("#confirmModalBody").html("{{ trans('basic::media/admin_lang.media_optimize_question') }}");
            strBtn+= '<button type="button" id="optimizeVideoCancel" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" id="optimizeVideoStart" class="btn btn-primary" onclick="javascript:optimize(\''+url+'\');">{{ trans('basic::media/admin_lang.optimizar_item') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }



        function deleteinfo(url) {
            $.ajax({
                url     : url,
                type    : 'GET',
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                        if(data.success) {
                            oTable.ajax.url( '{{ url('admin/media/list/') }}/' + positionPath ).load();
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('basic::media/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }
            });
            return false;
        }


        function optimize(url) {
            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#confirmModalBody").append('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin fa-5x"></i></div>');
            $("#optimizeVideoCancel").attr("disabled",true);
            $("#optimizeVideoStart").attr("disabled",true);

            $.ajax({
                url     : url,
                type    : 'GET',
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                        if(data.success) {
                            oTable.ajax.url( '{{ url('admin/media/list/') }}/' + positionPath ).load();
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('basic::media/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }
            });
            return false;
        }
    </script>

@stop
