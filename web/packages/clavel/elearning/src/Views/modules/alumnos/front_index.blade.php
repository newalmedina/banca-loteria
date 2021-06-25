@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section("head_page")

@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('carrousel')
    <img src="/assets/front/img/124A8841.jpg" alt="">
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
                    {!! Form::hidden('folder', Session::has('alumnos_directorio_front')?'outbox':'inbox', array('id' => 'folder')) !!}
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="nombrefichero" readonly>
                                <label class="input-group-btn">
                                        <span class="btn btn-primary btn-file">
                                            {{ trans('elearning::alumnos/admin_lang.search_files') }}
                                            {!! Form::file('alumnos_ficheros[]',array('id'=>'alumnos_ficheros', 'style' => 'display: none;', 'multiple'=>true)) !!}
                                        </span>
                                    </label>
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
                        <p>{!! trans("elearning::alumnos/front_lang.enviando_datos") !!}<br><br><small>{!! trans("elearning::alumnos/front_lang.paciencia") !!}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mailbox">
        <div class="col-md-3">
            <div class="mailbox-box mailbox-box-solid">
                <div class="mailbox-box-header">
                    <h3 class="mailbox-box-title">{{ trans('elearning::alumnos/front_lang.folders') }}</h3>
                </div>

                <div class="mailbox-box-body">
                    <ul class="vertical-list">
                        <li class="active"><a href="javascript:changeFolder('inbox');"><i class="fa fa-upload" aria-hidden="true"></i>
                                {{ trans('elearning::alumnos/front_lang.file_sent') }}
                            </a></li>

                        <li><a href="javascript:changeFolder('outbox');"><i class="fa  fa-download" aria-hidden="true"></i>
                                {{ trans('elearning::alumnos/front_lang.file_received') }}
                            </a></li>
                    </ul>
                </div>

            </div> <!-- End Box -->
        </div>
        <!-- Sidebar -->

        <div class="col-md-9">

            <div class="mailbox-box
                @if (!Session::has('alumnos_directorio_front'))
                    mailbox-box-primary
                @else
                    mailbox-box-warning
                @endif">

                <div class="mailbox-box-header">
                    <h3 class="mailbox-box-title">
                        @if (!Session::has('alumnos_directorio_front'))
                            {{ trans('elearning::alumnos/front_lang.file_sent') }}
                        @else
                            {{ trans('elearning::alumnos/front_lang.file_received') }}
                        @endif
                    </h3>
                </div>

                <div class="mailbox-box-footer clearfix">
                    <div class="pull-right">
                        <a type="button"
                           href="javascript:openSubidaFicheros('{{ Session::has('alumnos_directorio_front')?'inbox':'outbox' }}');"
                           class="btn btn-default">
                            <i class="fa fa-cloud-upload" aria-hidden="true"></i> {{ trans('elearning::alumnos/front_lang.subir_fichero') }}
                        </a>
                    </div>
                </div>

                <div class="mailbox-box-body" style="border-top: 1px solid #f4f4f4;">
                    <ul class="mailbox-attachment clearfix">
                        @foreach($mediaFiles as $mediaFile)
                            @if($mediaFile->getCustomProperty('folder') == (Session::has('alumnos_directorio_front')?'outbox':'inbox'))
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
                                        <a href="{{ url('alumnos-directory/media/'.$mediaFile->id) }}" class="mailbox-attachment-name"><i class="fa  fa-paperclip" aria-hidden="true"></i>
                                            {{ $mediaFile->file_name }}
                                        </a>
                                        <span class="mailbox-attachment-size">
                                              {{ $mediaFile->human_readable_size }}
                                              <a href="{{ url('alumnos-directory/media/'.$mediaFile->id) }}" class="btn btn-default btn-xs pull-right"><i class="fa  fa-cloud-download" aria-hidden="true"></i></a>
                                            @if (!Session::has('alumnos_directorio_front'))
                                                <a href="javascript:deleteFile('{{ $mediaFile->id }}')" class="btn btn-default btn-xs pull-right"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            @endif

                                            </span>
                                    </div>
                                </li>
                            @endif
                        @endforeach


                    </ul> <!-- Attachments -->

                </div>



            </div> <!-- End Box -->

        </div>
        <!-- Content -->

    </div>


    {!! Form::open(array('role' => 'form','id'=>'formDeleteFile', 'method'=>'POST', 'files'=>true)) !!}
    {!! Form::hidden('user_id', $alumno->user->id, array('id' => 'user_id')) !!}
    {!! Form::hidden('media_id', 0, array('id' => 'media_id')) !!}
    {!! Form::hidden('_method', 'DELETE', array('id' => '_method')) !!}
    {!! Form::hidden('folder', Session::has('alumnos_directorio_front')?'inbox':'outbox', array('id' => 'folder')) !!}

    {!! Form::close() !!}


@endsection


@section('foot_page')
    <!-- page script -->
    <script type="text/javascript">
        function changeFolder(folder) {
            window.location = '{{ url('alumnos-directory/change-directory')}}'+'/'+folder;
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
            $("#formUploadFiles").attr("action", "{!! url("alumnos-directory/upload") !!}");
            $("#formUploadFiles").submit();
        }

        $("#alumnos_ficheros").change(function(){
            getFileName();
        });

        @if (!Session::has('alumnos_directorio_front'))
        function deleteFile(media_id) {
            $("#formDeleteFile").attr("action", "{!! url("alumnos-directory/delete") !!}");
            $("#media_id").val(media_id);
            $("#formDeleteFile").submit();

        }
        @endif

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
@endsection
