@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    @parent
    <link href="{{ asset("/assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}" rel="stylesheet" type="text/css" />


    <!--<link href="{{ asset("assets/front/css/admin_template/contenidos.css") }}" rel="stylesheet" type="text/css" />-->
    <link href="{{ asset("assets/admin/vendor/dropzone") }}/dropzone.min.css" rel="stylesheet" />


    <style>
        #bs-modal-images, #bs-modal-code {
            z-index: 99999999;
        }

        .select2-container--default .select2-selection--multiple {
            height: auto !important;
        }
    </style>
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/asignaturas/") }}">{{ trans('elearning::asignaturas/admin_lang.asignaturas') }}</a></li>
    <li><a href="{{ url("admin/asignaturas/".$modulo->asignatura->id."/modulos/") }}">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$modulo->asignatura->{"titulo:es"} }}</a></li>
    <li><a href="{{ url("admin/modulos/".$modulo->id."/contenidos/") }}">{{ trans('elearning::contenidos/admin_lang.contenidos_listado')." ".$modulo->nombre}}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')
    @include('admin.includes.warnings')


    <!-- Imágenes multimedia  -->
    <div class="modal modal-note fade in" id="bs-modal-images">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('general/admin_lang.selecciona_un_archivo') }}</h4>
                </div>
                <div id="responsibe_images" class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="row">
        {!! Form::model($contenido, $form_data, array('role' => 'form')) !!}
            {!! Form::hidden('modulo_id', $modulo->id, array('id' => 'modulo_id')) !!}
            {!! Form::hidden('modal', 0, array('id' => 'modal')) !!}
             <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">
                        <div class="form-group">
                            {!! Form::label('tipo_contenido_id', trans('elearning::contenidos/admin_lang.tipos'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}

                            <div class="col-md-10">
                                @foreach($tipos as $tipo)
                                    <a class="btn btn-app @if($tipo->id == $contenido->tipo_contenido_id) btn-primary active @endif @if($contenido->id != "")  disabled @endif" href="javascript:setTipo(<?=$tipo->id;?>)">
                                        <i class="fa <?=$tipo->{'icono:es'};?>" aria-hidden="true"></i> <?=$tipo->{'nombre:es'};?>
                                    </a>
                                @endforeach
                                {!! Form::text('tipo_contenido_id', $contenido->tipo_contenido_id, array('id' => 'tipo_contenido_id','class'=>'form-control', 'style' => 'visibility:hidden; height:0px; margin-top:-15px;')) !!}
                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label('parent_id', trans('elearning::contenidos/admin_lang.contenido_padre'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <select name="parent_id" class="form-control">
                                    <option value="">{{ trans('elearning::contenidos/admin_lang.sel_contenido_padre') }}</option>
                                    @foreach($modulo->contenidos()->orderBy('lft')->get() as $o_contenidos)
                                        @if($o_contenidos->id != $contenido->id)
                                            <option value="{{ $o_contenidos->id }}" @if($o_contenidos->id == $contenido->parent_id) selected @endif>{{ $o_contenidos->nombre }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('activo', trans('elearning::modulos/admin_lang.activo'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('activo', '0', true, array('id'=>'activo_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('activo', '1', false, array('id'=>'activo_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('obligatorio', trans('elearning::contenidos/admin_lang.obligatorio'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('obligatorio', '0', true, array('id'=>'obligatorio_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('obligatorio', '1', false, array('id'=>'obligatorio_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('modal', trans('elearning::contenidos/admin_lang.modal'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('modal', '0', true, array('id'=>'modal_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('modal', '1', false, array('id'=>'modal_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                 <div class="nav-tabs-custom">
                     <ul class="nav nav-tabs">

                         <?php
                         $nX = 1;
                         ?>
                         @foreach ($a_trans as $key => $valor)
                             <li @if($nX==1) class="active" @endif>
                                 <a href="#tab_{{ $key }}" data-toggle="tab">
                                     {{ $valor["idioma"] }}
                                     @if($nX==1)- <span class="text-success">{{ trans('general/admin_lang._defecto') }}</span>@endif
                                 </a>
                             </li>
                             <?php
                             $nX++;
                             ?>
                         @endforeach

                     </ul>

                     <div class="tab-content">
                         <?php
                         $nX = 1;
                         ?>
                         @foreach ($a_trans as $key => $valor)
                             <div id="tab_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                                 {!!  Form::hidden('userlang['.$key.'][id]', $valor["id"], array('id' => 'id')) !!}
                                 {!!  Form::hidden('userlang['.$key.'][contenido_id]', $contenido->id, array('id' => 'modulo_id')) !!}

                                 <div class="form-group">
                                     {!! Form::label('userlang['.$key.'][nombre]', trans('elearning::contenidos/admin_lang.nombre_contenido'), array('class' => 'col-sm-2 control-label')) !!}
                                     <div class="col-sm-10">
                                         {!! Form::text('userlang['.$key.'][nombre]', $contenido->{'nombre:'.$key} , array('placeholder' => trans('elearning::contenidos/admin_lang.nombre_contenido'), 'class' => 'form-control', 'id' => 'nombre_'.$key)) !!}
                                     </div>
                                 </div>

                                 <div class="form-group">
                                     {!! Form::label('userlang['.$key.'][url_amigable]', trans('elearning::contenidos/admin_lang.url_amigable'), array('class' => 'col-sm-2 control-label')) !!}
                                     <div class="col-sm-10">
                                         <div class="input-group">
                                             <span class="input-group-addon">{{ url("/") }}/</span>
                                             {!! Form::text('userlang['.$key.'][url_amigable]', "contenido/detalle-contenido/".$contenido->{'url_amigable:'.$key}."/".$contenido->id , array('placeholder' => trans('elearning::contenidos/admin_lang.url_amigable'), 'class' => 'form-control', 'readonly' => true, 'id' => 'url_amigable_'.$key)) !!}
                                         </div>
                                     </div>
                                 </div>

                             </div>
                             <?php
                             $nX++;
                             ?>
                         @endforeach
                     </div>
                 </div>
                 @if($vista_tipo != "")
                        <div class="tipos">
                            @include($vista_tipo)
                        </div>

                @endif

                <div class="box box-solid">
                    <div class="box-footer">
                        <a href="{{ url('/admin/modulos/'.$modulo->id."/contenidos/") }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
                    </div>
                </div>
             </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('foot_page')
    <script src="{{ asset("/assets/admin/vendor/tinymce/tinymce.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/assets/admin/vendor/colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
    <script src="{{ asset("/assets/admin/vendor/dropzone") }}/dropzone.min.js"></script>

    <script>
        var oTable = '';
        var selected = [];
        var myDropzone;
        var positionPath = "root";

        $(document).ready(function() {
            $(".btn-app").click(function() {
                $(".btn-app").removeClass("btn-primary active");
                $(this).addClass("btn-primary active");
            });

            $( "#formData" ).submit(function( event ) {
                $(".destroy").remove();
            });

            $("#myfile").change(function(){
                getFileName("");
            });

            $("#myfile1").change(function(){
                getFileName("1");
            });

            $("#myfile2").change(function(){
                getFileName("2");
            });

            $("#select_video").click(function () {
                openImageController("media_url", '0');
            });

            recargarTiny();

            Dropzone.autoDiscover = false;

            myDropzone = new $(".dropzone").dropzone({
                url: "{{ url('admin/media/subirarchivos') }}",
                addRemoveLinks : false,
                maxFiles: 2000,
                timeout: 3600000,
                maxFilesize: {!! config("general.media.upload_max_file_size")!!},
                dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-upload text-primary" style="font-size: 64px;" aria-hidden="true"></i><br><br>{{ trans('elearning::media/admin_lang.arrastra') }}</span></span><span>&nbsp&nbsp<h4 class="display-inline"> ({{ trans('elearning::media/admin_lang.oclick') }})</h4></span>',
                dictResponseError: 'Error!',
                dictCancelUpload: '{{ trans('elearning::media/admin_lang.dictCancelUpload') }}',
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
                    oTable.ajax.url( '{{ url('admin/media/getData/') }}/' + positionPath ).load();
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                }
            });

        });

        function recargarTiny() {
            tinymce.init({
                selector: "textarea.textarea",
                menubar: false,
                height: 300,
                resize:false,
                convert_urls: false,
                extended_valid_elements : "a[class|name|href|target|title|onclick|rel],script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],$elements",
                plugins: [
                    "advlist autolink lists link image charmap print preview anchor",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table paste hr",
                    "wordcount fullscreen nonbreaking visualblocks"
                ],

                content_css: [
                    {{--
                    // Ponemos aquí los css de front
                    '{{ url('assets/front/vendor/bootstrap/css/bootstrap.min.css') }}',
                    '{{ url('assets/front/vendor/fontawesome/css/font-awesome.min.css') }}',
                    '{{ url('assets/front/css/front.min.css') }}',
                    '{{ url('assets/front/css/theme.css') }}',
                    '{{ url('assets/front/css/theme-element.css') }}',
                    '{{ url('assets/front/vendor/fontawesome/css/font-awesome.min.css') }}'
                    --}}

                ],
                toolbar: "insertfile undo redo | styleselect | fontsizeselect | bold italic forecolor, backcolor | hr nonbreaking visualblocks | table | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image | code fullscreen",
                file_picker_callback: function(callback, value, meta) {
                    openImageControllerExt(callback, '0');
                }
            });
        }

        function evtHidden(evt) {
            evt.data($("#selectedFile").val());
        }

        function openImageControllerExt(callback, only_img) {
            $('#bs-modal-images')
                .one('hidden.bs.modal', callback, evtHidden)
                .modal({
                keyboard: false,
                backdrop: 'static',
                show: true
            });

            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#responsibe_images").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin"></i></div>');
            $("#responsibe_images").load("{{ url("admin/media/viewer-simple/") }}/" + only_img);
        }

        var inputChange = '';
        function openImageController(input, only_img) {
            inputChange = input;
            $('#bs-modal-images')
            .on('hidden.bs.modal', function(){
                $("#"+inputChange).val($("#selectedFile").val())
            })
            .modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#responsibe_images").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
            $("#responsibe_images").load("{{ url("admin/media/viewer-simple/") }}/" + only_img);

        }
        function setTipo(idtipo) {
            $("#tipo_contenido_id").val(idtipo);
            $(".tipos").addClass("destroy");
            $(".tipos").each(function() {
                if($(this).attr("id") == "tipo_"+idtipo) {
                    $(this).removeClass("destroy");
                }
            });
        }

        function getFileName(id) {
            $('#nombrefichero'+id).val($('#myfile'+id)[0].files[0].name);
            $("#delete_photo"+id).val('1');
            $("#remove"+id).css("display","block");
            $("#nombre_archivo"+id).css("display","none");
            $("#display_image"+id).addClass("disabled");
        }

        function remove_image(id) {
            $("#display_image"+id).addClass("disabled");
            $("#remove"+id).css("display","none");
            $('#nombrefichero'+id).val('');
            $('#myfile'+id).val("");
            $("#delete_photo"+id).val('1');
        }
    </script>
    {!! JsValidator::formRequest('Clavel\Elearning\Requests\ContenidoRequest')->selector('#formData') !!}
@stop
