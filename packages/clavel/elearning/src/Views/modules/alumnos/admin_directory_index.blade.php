@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <style>
        /** SPINNER CREATION **/
        .loader {
            position: relative;
            text-align: center;
            margin: 15px auto 35px auto;
            z-index: 9999;
            display: block;
            width: 80px;
            height: 80px;
            border: 10px solid rgba(0, 0, 0, .3);
            border-radius: 50%;
            border-top-color: #000;
            animation: spin 1s ease-in-out infinite;
            -webkit-animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @-webkit-keyframes spin {
            to {
                -webkit-transform: rotate(360deg);
            }
        }

        .loader-txt p {
            font-size: 13px;
            color: #666;
        }

        .loader-txt p small {
            font-size: 11.5px;
            color: #999;
        }

    </style>
@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <!-- Modal para la subida de ficheros -->
    <div id="modalSubidaFicheros" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalSubidaFicheros"
         data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div id="content_block" class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                        <span id="btnEnviarTextoCerrarCheck" aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        {{ trans('elearning::alumnos/admin_lang.subir_fichero') }}
                    </h4>
                </div>
                <div id="container_cambio_texto_contenido" class="modal-body">
                    {!! Form::open(array('role' => 'form','id'=>'formUploadFiles', 'method'=>'POST', 'files'=>true)) !!}
                    {!! Form::hidden('user_id', $alumno->user->id, array('id' => 'user_id')) !!}
                    {!! Form::hidden('folder', Session::has('alumnos_directorio_admin')?'inbox':'outbox', array('id' => 'folder')) !!}
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="nombrefichero" readonly>
                                <span class="input-group-btn">
                                        <div class="btn btn-primary btn-file">
                                            {{ trans('elearning::alumnos/admin_lang.search_files') }}
                                            {!! Form::file('alumnos_ficheros[]',array('id'=>'alumnos_ficheros', 'multiple'=>true)) !!}
                                        </div>
                                    </span>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnEnviarTextoCerrar" class="btn btn-default pull-left"
                            onclick="javascript:doClose();">
                        {{ trans("general/front_lang.cerrar") }}
                    </button>
                    <button type="button" id="btnEnviarTexto" class="btn btn-primary" onclick="javascript:doUpload();">{{ trans('elearning::alumnos/admin_lang.subir') }}</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Fin Modal para la Modificación de estado de proyectos -->

    <!-- Modal Enviando-->
    <div class="modal fade" id="sendingDataModal" tabindex="-1" role="dialog" aria-labelledby="sendingDataModal">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="loader"></div>
                    <div clas="loader-txt">
                        <p>{!! trans("elearning::alumnos/admin_lang.enviando_datos") !!}<br><br><small>{!! trans("elearning::alumnos/admin_lang.paciencia") !!}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('elearning::alumnos/admin_lang.folders') }}</h3>
                    </div>
                    <div class="box-body no-padding">
                        <ul class="nav nav-pills nav-stacked">
                            <li><a href="javascript:changeFolder('inbox');"><i class="fa fa-upload" aria-hidden="true"></i>
                                    {{ trans('elearning::alumnos/admin_lang.file_sent') }}
                                    </a></li>
                            <li><a href="javascript:changeFolder('outbox');"><i class="fa  fa-download" aria-hidden="true"></i>
                                    {{ trans('elearning::alumnos/admin_lang.file_received') }}
                                </a></li>
                        </ul>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /. box -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="box
                    @if (!Session::has('alumnos_directorio_admin'))
                            box-primary
                    @else
                            box-warning
                    @endif">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            @if (!Session::has('alumnos_directorio_admin'))
                                {{ trans('elearning::alumnos/admin_lang.file_sent') }}
                            @else
                                {{ trans('elearning::alumnos/admin_lang.file_received') }}
                            @endif
                        </h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-footer">
                        <div class="pull-right">
                            <a type="button"
                               href="javascript:openSubidaFicheros('{{ Session::has('alumnos_directorio_admin')?'inbox':'outbox' }}');"
                               class="btn btn-default">
                                <i class="fa fa-cloud-upload" aria-hidden="true"></i> {{ trans('elearning::alumnos/admin_lang.subir_fichero') }}
                            </a>
                        </div>
                    </div>


                    <!-- /.box-body -->
                    <div class="box-footer">
                        <ul class="mailbox-attachments clearfix">
                            @foreach($mediaFiles as $mediaFile)
                                @if($mediaFile->getCustomProperty('folder') == (Session::has('alumnos_directorio_admin')?'inbox':'outbox'))
                                <li>
                                    <span class="mailbox-attachment-icon">
                                        @switch($mediaFile->mime_type)
                                            @case('application/pdf')
                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                            @break
                                            @case('application/word')
                                            @case('application/msword')
                                            @case('application/vnd.openxmlformats-officedocument.wordprocessingml.document')
                                            <i class="fa fa-file-word-o" aria-hidden="true"></i>
                                            @break
                                            @case('application/vnd.ms-excel')
                                            @case('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                            @break
                                            @case('application/vnd.ms-powerpoint')
                                            @case('application/vnd.openxmlformats-officedocument.presentationml.presentation')
                                            <i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>
                                            @break
                                            @default
                                            <i class="fa fa-file-o" aria-hidden="true"></i>
                                        @endswitch
                                    </span>
                                        <div class="mailbox-attachment-info">
                                            <a href="{{ url('admin/alumnos-directory/media/'.$mediaFile->id) }}" class="mailbox-attachment-name"><i class="fa  fa-paperclip" aria-hidden="true"></i>
                                                {{ $mediaFile->file_name }}
                                            </a>
                                            <span class="mailbox-attachment-size">
                                              {{ $mediaFile->human_readable_size }}
                                              <a href="{{ url('admin/alumnos-directory/media/'.$mediaFile->id) }}" class="btn btn-default btn-xs pull-right"><i class="fa  fa-cloud-download" aria-hidden="true"></i></a>
                                              <a href="javascript:deleteFile('{{ $mediaFile->id }}')" class="btn btn-default btn-xs pull-right"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            </span>
                                        </div>
                                </li>
                                @endif
                            @endforeach

                        </ul>
                    </div>

                </div>
                <!-- /. box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>

    {!! Form::open(array('role' => 'form','id'=>'formDeleteFile', 'method'=>'POST', 'files'=>true)) !!}
    {!! Form::hidden('user_id', $alumno->user->id, array('id' => 'user_id')) !!}
    {!! Form::hidden('media_id', 0, array('id' => 'media_id')) !!}
    {!! Form::hidden('_method', 'DELETE', array('id' => '_method')) !!}
    {!! Form::hidden('folder', Session::has('alumnos_directorio_admin')?'inbox':'outbox', array('id' => 'folder')) !!}

    {!! Form::close() !!}

@endsection

@section("foot_page")
    <!-- page script -->
    <script type="text/javascript">
        function changeFolder(folder) {
            window.location = '{{ url('admin/alumnos-directory/' . $alumno->user->id . '/change-directory')}}'+'/'+folder;
        }

        function openSubidaFicheros(folder) {
            $("#modalSubidaFicheros").modal("toggle");
        }

        function doClose() {
            var btnClose = $("#btnEnviarTextoCerrar");
            if (btnClose.hasClass('disabled')) {
                return false;
            }
            $("#modalSubidaFicheros").modal("toggle");
        }

        function doUpload() {
            $("#sendingDataModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $("#modalSubidaFicheros").modal("toggle");
            $("#formUploadFiles").attr("action", "{!! url("admin/alumnos-directory/upload") !!}");
            $("#formUploadFiles").submit();
        }

        $("#alumnos_ficheros").change(function(){
            getFileName();
        });

        function deleteFile(media_id) {
            $("#formDeleteFile").attr("action", "{!! url("admin/alumnos-directory/delete") !!}");
            $("#media_id").val(media_id);
            $("#formDeleteFile").submit();

        }

        function getFileName() {
            var ficheros = "";

            var array = $('#alumnos_ficheros')[0].files;
            var sep = "";
            for (index = 0; index < array.length; index++) {
                ficheros +=  sep + array[index].name;
                sep = ", "
            }
            $('#nombrefichero').val(ficheros);
        }

    </script>
@stop
