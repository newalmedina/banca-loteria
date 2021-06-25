@extends('admin.layouts.popup')

@section('title')
    @parent {{ $page_title }}
@stop

@section("head_page")

    <link rel="stylesheet" href="{{ asset("/assets/admin/vendor/dropzone/dropzone.min.css") }} ">

    <style>
        .imag {
            float:left;
            padding: 10px;
            border: solid 1px #C0C0C0;
            background-color: #F4F4F4;
            margin: 5px;
            cursor: pointer;
            width: 150px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .imag:hover {
            background-color: #fcefa1;
            -webkit-transition: background-color 1000ms linear;
            -moz-transition: background-color 1000ms linear;
            -o-transition: background-color 1000ms linear;
            -ms-transition: background-color 1000ms linear;
            transition: background-color 1000ms linear;
        }

        .image_container {
            height: 120px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .helper {
            display: inline-block;
            height: 100%;
            vertical-align: middle;
        }

        #modal_confirm {
            z-index: 9999999999;
        }
    </style>
@stop

@section('content')

    @include('admin.includes.modals')

    <!-- Creación  -->
    <div class="modal modal-note fade in" id="bs-modal-new-folder" style="padding-right: 17px;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('basic::media/admin_lang.new_folder') }}</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('url' => 'admin/media/dir/create', 'method' => 'POST', 'id' => 'formFolder')) }}
                    {!! Form::hidden('act_folder', "/", array('id' => 'act_folder')) !!}
                    {!! Form::hidden('by_ajax', "1", array('id' => 'by_ajax')) !!}

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
                    <button onclick="$('#bs-modal-new-folder').modal('hide');" class="btn btn-default" type="button">{{ trans('general/admin_lang.close') }}</button>
                    <button onclick="javascript:$('#formFolder').submit();" class="btn btn-default" type="button">{{ trans('general/admin_lang.save') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    @if(Auth::user()->can("admin-media-create"))
        <div id="dropimages" class="row" style="display: none; height: 500px;">
            <div class="col-md-12">
                <a class="btn btn-sm btn-primary btn-flat pull-left" href="javascript:uploadImages();"><i class="fa fa-close" aria-hidden="true"></i> {{ trans('basic::media/admin_lang.close_upload_image') }}</a>
                <br clear="all">
                <form class="dropzone table-layout well mb0" style="box-shadow:none; border-radius: 0px; border-style:dotted; background-color:#e5e8ed; height: 425px; margin-top: 20px;" role="form">
                    <input type="hidden" id="foldertoUp" value="/" name="foldertoUp">
                    <div class="fallback">
                        <input name="file" type="file" multiple />
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div id="multimedia" class="row">
        <div class="col-md-3">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("basic::media/admin_lang.folders") }}</h3>
                </div>
                @if(Auth::user()->can("admin-media-create"))
                    <div class="box-header with-border">
                        <a class="btn btn-sm btn-primary btn-flat pull-left" href="javascript:createFolder();"><i class="fa fa-folder-open" aria-hidden="true"></i> {{ trans('basic::media/admin_lang.new_folder') }}</a>
                    </div>
                @endif
                <div id="listOfFolders" class="box-body" style="height: 425px; overflow: hidden; overflow-y: auto;">
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
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>

        <div class="col-md-9">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("basic::media/admin_lang.files") }}</h3>
                </div>
                @if(Auth::user()->can("admin-media-create"))
                    <div class="box-header with-border">
                        <a class="btn btn-sm btn-primary btn-flat pull-left" href="javascript:uploadImages();"><i class="fa fa-upload" aria-hidden="true"></i> {{ trans('basic::media/admin_lang.upload_image') }}</a>
                    </div>
                @endif
                <div id="images_list" class="box-body" style="height: 425px; overflow: hidden; overflow-y: auto;">

                </div>

            </div>

        </div>
        <br clear="all">
    </div>


    <input type="hidden" id="selectedFile" value="" name="selectedFile">

@endsection

@section('foot_page')

    <script src="{{ asset("/assets/admin/vendor/dropzone/dropzone.min.js") }}"></script>

    <script>
        var positionPath = "root";
        var positionFolder = "/";

        $(document).ready(function() {
            loadImages("/");
            $('#act_folder').val("/");
            $("#foldertoUp").val("/");

            $("#formFolder").submit(function(e) {

                var frm = $("#formFolder"); // the script where you handle the form input.

                $.ajax({
                    type: frm.attr('method'),
                    url: frm.attr('action'),
                    data: frm.serialize(),// serializes the form's elements.
                    dataType: 'JSON',
                    success: function(data)
                    {
                        if(data!='error') {
                            reorderFolders(data);
                            $('#bs-modal-new-folder').modal("hide");
                        } else {
                            $("#modal_alert").css('z-index','99999');
                            $("#modal_alert").addClass('modal-danger');
                            $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                            $("#modal_alert").modal('toggle');
                        }
                    }
                });

                e.preventDefault(); // avoid to execute the actual submit of the form.
            });

            Dropzone.autoDiscover = false;

            myDropzone = new $(".dropzone").dropzone({
                url: "{{ url('admin/media/file/upload') }}",
                addRemoveLinks : false,
                maxFilesize: {{ config("general.media.upload_max_file_size") }},
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
                success: function(file, response){
                    thisDropzone = this;
                    if (thisDropzone.getQueuedFiles().length == 0 && thisDropzone.getUploadingFiles().length == 0) $(".dz-message").css("display","block");
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                }
            });
        });

        function returnurl(url) {
            $("#selectedFile").val(url);
            $('#bs-modal-images').modal('hide');
        }

        function changeFolder(newFolder, routid) {
            $('#act_folder').val(newFolder);
            $("#foldertoUp").val(newFolder);
            $(".list_folder").removeClass("active");
            $("#li_" + routid).addClass("active");
            $(".fa-icon_f").removeClass("fa-folder-open");
            $(".fa-icon_f").removeClass("fa-folder");
            $(".fa-icon_f").addClass("fa-folder");
            $("#fa_" + routid).removeClass("fa-folder-open");
            $("#fa_" + routid).addClass("fa-folder-open");
            $("#image_selected").focus();
            positionPath = routid;
            positionFolder = newFolder;
            loadImages(newFolder);
        }

        function loadImages(newFolder) {
            var url = "{{ url("admin/media/load") }}";

            $.ajax({
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                url     : url,
                type    : 'POST',
                data: {
                    folder: newFolder,
                    only_img: '{{ $only_img }}'
                },
                dataType: 'JSON',
                success : function(data) {
                    if(data) {
                        var strImg = "";
                        $.each(data, function (key, value) {
                            strImg+= '<div id="imag_' + key + '" class="imag">';
                            strImg+= '<div onclick="returnurl(\'' + value.url + '\');">';
                            strImg+= '<div class="image_container">';
                            strImg+= '<span class="helper"></span>';
                            strImg+= value.src;
                            strImg+= '</div>';
                            strImg+= value.original_filename;
                            strImg+= '</div>';
                            if(value.delete=='1') {
                                strImg+= '<a href="javascript:deleteFileElement(' + key + ');">[{{ trans('basic::media/admin_lang.delete')  }}]</a>';
                            } else {
                                strImg+= '&nbsp;';
                            }
                            strImg+= '</div>';
                        });

                        if(strImg=='') strImg = '{{ trans("basic::media/admin_lang.notImages") }}';

                        $("#images_list").html(strImg);
                    } else {
                        $("#images_list").html("No hay archivos");
                    }
                    return false;
                }
            });
            return false;
        }

        function deleteFileElement(id) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('general/admin_lang.delete_question') }}");
            strBtn+= '<button type="button" class="btn btn-default" onclick="javascript:closeform();">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deletefile(\''+id+'\');">{{ trans('general/admin_lang.borrar_item') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');

        }

        function closeform() {
            $('#modal_confirm').modal('hide');
        }

        function deletefile(id) {
            var url = '{{ url('admin/media/file/') }}/' + id + '/destroy';

            $.ajax({
                url     : url,
                type    : 'GET',
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                        $("#imag_" + id).remove();
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

        function createFolder() {
            $("#foldername").val("");
            $('#bs-modal-new-folder').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });
        }

        function reorderFolders(data) {
            var str_folders = "";

            str_folders+= '<ul class="nav nav-pills nav-stacked">';
            str_folders+= '<li id="li_root" class="active list_folder">';
            str_folders+= '<a href="javascript:changeFolder(\'/\', \'root\');">';
            str_folders+= '<i id="fa_root" class="fa fa-folder-open text-warning fa-icon_f" aria-hidden="true"></i>/';
            str_folders+= '</a>';
            str_folders+= '</li>';

            $.each(data, function (key, value) {
                str_folders+= '<li id="li_'+key+'" class="list_folder">';
                str_folders+= '<a href="javascript:changeFolder(\''+value+'\', \''+key+'\');">';
                str_folders+= str_repeat('&nbsp;',(substr_count(value,'/')) * 5);
                str_folders+= '<i id="fa_'+key+'" class="fa fa-folder text-warning fa-icon_f" aria-hidden="true"></i>';
                str_folders+= baseName(value);
                str_folders+= '</a>';
                str_folders+= '</li>';
            });

            str_folders+= '</ul>';

            $("#listOfFolders").html(str_folders);
            changeFolder(positionFolder, positionPath);

        }

        function baseName(str)
        {
            var base = new String(str).substring(str.lastIndexOf('/') + 1);
            return base;
        }

        function str_repeat(input, multiplier) {
            var y = '';
            while (true) {
                if (multiplier & 1) {
                    y += input;
                }
                multiplier >>= 1;
                if (multiplier) {
                    input += input;
                } else {
                    break;
                }
            }
            return y;
        }

        function substr_count(haystack, needle, offset, length) {
            var cnt = 0;

            haystack += '';
            needle += '';
            if (isNaN(offset)) {
                offset = 0;
            }
            if (isNaN(length)) {
                length = 0;
            }
            if (needle.length == 0) {
                return false;
            }
            offset--;

            while ((offset = haystack.indexOf(needle, offset + 1)) != -1) {
                if (length > 0 && (offset + needle.length) > length) {
                    return false;
                }
                cnt++;
            }

            return cnt;
        }

        function uploadImages() {
            if($("#multimedia").css("display")=='none') {
                $("#dropimages").fadeOut(500, function() {
                    $("#multimedia").fadeIn(500);
                    loadImages(positionFolder);
                });
            } else {
                $("#multimedia").fadeOut(500, function() {
                    $("#dropimages").fadeIn(500);
                });
            }
        }
    </script>


@stop

