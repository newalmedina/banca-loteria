@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link href="{{ asset("assets/admin/vendor/dropzone") }}/dropzone.min.css" rel="stylesheet" />
    <style>


        /* Mimic table appearance */
        div.table {
            display: table;
        }
        div.table .file-row {
            display: table-row;
        }
        div.table .file-row > div {
            display: table-cell;
            vertical-align: top;
            border-top: 1px solid #ddd;
            padding: 8px;
        }
        div.table .file-row:nth-child(odd) {
            background: #f9f9f9;
        }



        /* The total progress gets shown by event listeners */
        #total-progress {
            opacity: 0;
            transition: opacity 0.3s linear;
        }

        /* Hide the progress bar when finished */
        #previews .file-row.dz-success .progress {
            opacity: 0;
            transition: opacity 0.3s linear;
        }

        /* Hide the delete button initially */
        #previews .file-row .delete {
            display: none;
        }

        /* Hide the start and cancel buttons and show the delete button */

        #previews .file-row.dz-success .start,
        #previews .file-row.dz-success .cancel {
            display: none;
        }
        #previews .file-row.dz-success .delete {
            display: block;
        }




    </style>

@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop


@section('content')


    <div class="container" style="padding-top: 35px;">
        @include('front.includes.errors')
        @include('front.includes.success')

        <div class="starter-template">
            <h1>@lang('file-transfer::front_lang.upload-files-title')</h1>
        </div>

        <div class="row">
            <div class="col-md-6">

                <div id="actions" class="row">

                    <div class="col-lg-12">
                {!! Form::open(['route'=> 'file-transfer.upload.store', 'method' => 'POST', 'role' =>'form',
'files'=>'true', 'class' => 'dropzone', 'id' => 'upload-form',
'style' => "box-shadow:none; border-radius: 0px; border-style:dotted; background-color:#e5e8ed;"
]) !!}

                <div class="dz-message needsclick">
                    <span class="text-center">
                        <span class="font-lg visible-xs-block visible-sm-block visible-lg-block">
                            <span class="font-lg">
                                <i class="fa fa-upload text-primary fa-5x" aria-hidden="true"></i>
                                <br>
                                <br>
                                @lang('file-transfer::front_lang.maximum-filesize') {{ Clavel\FileTransfer\Services\Upload::fileMaxSize(true) }}
                            </span>
                        </span>
                        <span>
                            <h4 class="display-inline">@lang('file-transfer::front_lang.drop-file-here')</h4>
                        </span>
                    </span>

                </div>
                {!! Form::close() !!}
                        <div id="select-link-message" style="color: #a94442; visibility:hidden;">{{ trans("file-transfer::front_lang.download-link_required") }}</div>

                    </div>
                </div>
                <div class="col-lg-12">
                    <button class="btn btn-success" data-clipboard-target="#download-link">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                    <label>@lang('file-transfer::front_lang.preview-link')</label>
                    <p class="link"><a href="#" target="_blank" id="preview-link"></a></p>


                    <button class="btn btn-primary" data-clipboard-target="#preview-link">
                        <i class="fa fa-download" aria-hidden="true"></i>
                    </button>
                    <label>@lang('file-transfer::front_lang.direct-link')</label>
                    <p class="link"><a href="#" target="_blank" id="download-link"></a></p>

                    <button class="btn btn-danger" data-clipboard-target="#delete-link">
                        <i class="fa fa-eraser" aria-hidden="true"></i>
                    </button>
                    <label>@lang('file-transfer::front_lang.delete-link')</label>
                    <p class="link"><a href="#" id="delete-link"></a></p>
                </div>


            </div>
            <div class="col-md-6">
                <p>{{ trans("file-transfer::front_lang.all_fields_obligatory") }}</p>

                {!! Form::open(['route'=> 'file-transfer.upload.send', 'method' => 'POST', 'role' =>'form',
                    'id' => 'send-form'
                    ]) !!}
                {!! Form::hidden('bundle_id', $bundle_id, array('id' => 'bundle_id')) !!}
                {!! Form::hidden('select-link', '', array('id' => 'select-link')) !!}


                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group has-feedback">
                            {!! Form::label('email_destino', trans('file-transfer::front_lang.email_destino'), array('class' => 'control-label', 'readonly' => true)) !!}
                            {!! Form::text('email_destino', null, array('placeholder' => trans('file-transfer::front_lang._INSERTAR_email_destino'), 'class' => 'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('email', trans('file-transfer::front_lang.email'), array('class' => 'control-label', 'readonly' => true)) !!}
                            {!! Form::text('email', null, array('placeholder' => trans('file-transfer::front_lang._INSERTAR_email'), 'class' => 'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('message', trans('file-transfer::front_lang.message'), array('class' => 'control-label', 'readonly' => true)) !!}
                            {!! Form::textarea('message', null, array('class' => 'form-control', 'style' => 'resize:none; height:150px;')) !!}
                        </div>


                        <input type="submit" name="submit" id="submit" value="{{ trans("file-transfer::front_lang.submit") }}" class="btn btn-primary">
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h2>@lang('file-transfer::front_lang.files-list')</h2>
                <p>@lang('file-transfer::front_lang.you-can-add-files')</p>

                <div class="col-lg-5">
                    <!-- The global file processing state -->
                    <span class="fileupload-process">
                        <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress=""></div>
                        </div>
                      </span>
                </div>
                <div class="table table-striped files" id="previews">
                    <div id="template" class="file-row dz-image-preview">
                        <!-- This is used as the file preview template -->
                        <div>
                            <span class="preview"><img data-dz-thumbnail alt=""></span>
                        </div>
                        <div>
                            <p class="name" data-dz-name></p>
                            <strong class="error text-danger" data-dz-errormessage></strong>
                        </div>
                        <div>
                            <p class="size" data-dz-size></p>
                            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                            </div>
                        </div>
                        <div>
                            <!--<button class="btn btn-primary start">
                                <i class="glyphicon glyphicon-upload"></i>
                                <span>Start</span>
                            </button>
                            <button data-dz-remove class="btn btn-warning cancel">
                                <i class="glyphicon glyphicon-ban-circle"></i>
                                <span>Cancel</span>
                            </button>-->
                            <button data-dz-remove class="btn btn-danger delete">
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                        <!--
                        <div>
                            <div class="dz-success-mark"><span>✔</span></div>
                            <div class="dz-error-mark"><span>✘</span></div>
                        </div>
                        -->
                    </div>
                </div>


            </div>
        </div>
    </div><!-- /.container -->
@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script src="{{ asset("/assets/admin/vendor/dropzone") }}/dropzone.min.js"></script>

    <script>

        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
        var previewNode = document.querySelector("#template");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);


        // Getting count of successful files
        var success = 0;

        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone(".dropzone",{

            createImageThumbnails: true,
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 1,

            clickable: true,
            previewTemplate: previewTemplate,
            previewsContainer: "#previews", // Define the container to display the previews

            paramName: 'file',

            maxFiles: '{{ config('file-transfer.max_files') }}',
            maxFilesize: '{{ round(Clavel\FileTransfer\Services\Upload::fileMaxSize() / 1000000) }}',
            dictMaxFilesExceeded: '@lang('file-transfer::front_lang.files-count-limit')',
            dictFileTooBig: '@lang('file-transfer::front_lang.file-too-big')',

            addRemoveLinks : false,
            timeout: 3600000,
            headers: {
                'X-Upload-Bundle': '{{ $bundle_id }}',
                'X-CSRF-Token': '{{ csrf_token() }}'
            },
            init: function() {
                console.log("Its initialized.");


                this.on("uploadprogress", function(file, progress) {
                    console.log("uploadprogress");
                    console.log("File progress " + file.name, progress);
                });



                this.on("complete", function(file) {
                    console.log("complete");
                    console.log(file);
                });


            },
            error: function(file, response) {
                console.log("There was an error");
                $(file.previewElement).find('.dz-error-message').text(response);
                return false;
            },
            success: function(file, response)
            {
                console.log("success");
                console.log(file.name);

                var ext = checkFileExt(file.name); // Get extension
                var newimage = "";

                // Check extension
                if(ext !== 'png' && ext !== 'jpg' && ext !== 'jpeg'){
                    newimage = "{{ asset('/assets/front/img/logo.png') }}"; // default image path

                    console.log(ext);
                    var mydropzone = this;
                    mydropzone.emit("thumbnail", file, newimage);
                }


            },
            removedfile: function(file) {
                var name = file.upload.filename;
                console.log(name);
                $.ajax({
                    type: 'POST',
                    url: ' {{ route('file-transfer.upload.destroy') }}',
                    data: {filename: name},
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function (data){
                        console.log("File has been successfully removed!!");
                    },
                    error: function(e) {
                        console.log(e);
                    }});
                var fileRef;
                return (fileRef = file.previewElement) != null ?
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            }
        });

        // Get file extension
        function checkFileExt(filename){
            filename = filename.toLowerCase();
            return filename.split('.').pop();
        }


        myDropzone.on("addedfile", function(file) {
            console.log("addedfile");
            console.log("file uploaded "+ file.name);
            // Hookup the start button
            //file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file); };
        });

        // Update the total progress bar
        myDropzone.on("totaluploadprogress", function(progress) {
            document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
        });

        myDropzone.on("sending", function(file) {
            $('#submit').hide();
            // Show the total progress bar when upload starts
            document.querySelector("#total-progress").style.opacity = "1";
            // And disable the start button
            //file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
        });

        // Hide the total progress bar when nothing's uploading anymore
        myDropzone.on("queuecomplete", function(progress) {
            // Do not complete batch if not file was uploaded
            //if ($('.file-row.dz-success').length <= 0) return false;
            console.log("queuecomplete2");
            console.log("All done!2");
            $.ajax({
                async: true,
                dataType: 'json',
                headers: {
                    'X-Upload-Bundle': '{{ $bundle_id }}',
                    'X-CSRF-Token': '{{ csrf_token() }}'
                },
                method: 'POST',
                url: '{{ route('file-transfer.upload.complete') }}'
            }).done(function (data) {
                if (data.result == true) {
                    $('#preview-link').attr('href', data.bundle_url).html(data.bundle_url);
                    $('#download-link').attr('href', data.download_url).html(data.download_url);
                    $('#delete-link').attr('href', data.delete_url).html(data.delete_url);
                    $('#upload-column').removeClass('wide');
                    $('#settings-column').show();

                    $('#select-link').val(data.download_url);
                }
                document.querySelector("#total-progress").style.opacity = "0";
                $('#submit').show();
            });


        });

        $(document).ready(function() {
            $("#send-form").submit(function( event ) {

                if($("#select-link").val() === ''){
                    var messageError = document.getElementById('select-link-message')
                    messageError.style.visibility='visible';
                    setTimeout(function(){
                        var messageError = document.getElementById('select-link-message')
                        messageError.style.visibility='hidden';
                    }, 3000);
                    return false;
                }
                if(! $(this).valid()) return false;
                return true;
            });
        });

    </script>

    {!! JsValidator::formRequest('Clavel\FileTransfer\Requests\SendRequest')->selector('#send-form') !!}

@stop

