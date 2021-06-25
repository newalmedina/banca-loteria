@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    @parent
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
    <li><a href="{{ url("admin/asignaturas/".$pregunta->contenido->modulo->asignatura->id."/modulos/") }}">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$pregunta->contenido->modulo->asignatura->{"titulo:es"} }}</a></li>
    <li><a href="{{ url("admin/modulos/".$pregunta->contenido->modulo_id."/contenidos/") }}">{{ trans('elearning::contenidos/admin_lang.contenidos')." ".$pregunta->contenido->modulo->{"nombre:es"} }}</a></li>
    <li><a href="{{ url("admin/contenidos/".$pregunta->contenido->id."/preguntas/") }}">{{ trans('elearning::contenidos/admin_lang.questions')." ".$pregunta->contenido->nombre}}</a></li>
    <li><a href="{{ url("admin/preguntas/".$pregunta->id."/respuestas/") }}">{{ trans('elearning::preguntas/admin_lang.respuestas')." ".substr(strip_tags($pregunta->nombre),0,10)}}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')
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
        {!! Form::model($respuesta, $form_data, array('role' => 'form')) !!}
            {!! Form::hidden('pregunta_id', $pregunta->id, array('id' => 'pregunta_id')) !!}
             <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">
                        <div class="form-group">
                            {!! Form::label('puntos_correcta', trans('elearning::preguntas/admin_lang.puntos_correcta'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-2">
                                {!! Form::text('puntos_correcta',$respuesta->puntos_correcta,array('class'=>'form-control col-sm-6','id'=>'orden','placeholder'=>trans('elearning::preguntas/admin_lang.puntos_correcta'))) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('puntos_incorrecta', trans('elearning::preguntas/admin_lang.puntos_incorrecta'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-2">
                                {!! Form::text('puntos_incorrecta',$respuesta->puntos_incorrecta,array('class'=>'form-control col-sm-6','id'=>'orden','placeholder'=>trans('elearning::preguntas/admin_lang.puntos_incorrecta'))) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('orden', trans('elearning::preguntas/admin_lang.orden'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-2">
                                {!! Form::text('orden',$respuesta->orden,array('class'=>'form-control col-sm-6','id'=>'orden','placeholder'=>trans('elearning::preguntas/admin_lang.orden'))) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('activa', trans('elearning::modulos/admin_lang.activo'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('activa', '0', true, array('id'=>'activa_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('activa', '1', false, array('id'=>'activa_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('correcta', trans('elearning::preguntas/admin_lang.es_correcta'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('correcta', '0', true, array('id'=>'correcta_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('correcta', '1', false, array('id'=>'correcta_1')) !!}
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

                     </ul><!-- /.box-header -->

                     <div class="tab-content">
                         <?php
                         $nX = 1;
                         ?>
                         @foreach ($a_trans as $key => $valor)
                             <div id="tab_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                                 {!!  Form::hidden('userlang['.$key.'][id]', $valor["id"], array('id' => 'id')) !!}
                                 {!!  Form::hidden('userlang['.$key.'][respuesta_id]', $respuesta->id, array('id' => 'pregunta_id')) !!}
                                 <div class="form-group">
                                     {!! Form::label('userlang['.$key.'][nombre]', trans('elearning::preguntas/admin_lang.respuesta'), array('class' => 'col-sm-2 control-label')) !!}
                                     <div class="col-sm-10">
                                         {!! Form::textarea('userlang['.$key.'][nombre]', $respuesta->{'nombre:'.$key} , array('class' => 'form-control textarea', 'id' => 'nombre_'.$key)) !!}
                                     </div>
                                 </div>
                                 <div class="form-group">
                                     {!! Form::label('userlang['.$key.'][comentario]', trans('elearning::preguntas/admin_lang.comentario'), array('class' => 'col-sm-2 control-label')) !!}
                                     <div class="col-sm-10">
                                         {!! Form::textarea('userlang['.$key.'][comentario]', $respuesta->{'comentario:'.$key} , array('class' => 'form-control textarea', 'id' => 'comentario_'.$key)) !!}
                                     </div>
                                 </div>
                             </div>
                             <?php
                             $nX++;
                             ?>
                         @endforeach
                     </div>
                 </div>

                <div class="box box-solid">
                    <div class="box-footer">
                        <a href="{{ url('/admin/preguntas/'.$pregunta->id."/respuestas/") }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
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
    <script>
        var oTable = '';
        var selected = [];

        $(document).ready(function() {
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
        });

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

        function getFileName() {
            $('#nombrefichero').val($('#myfile')[0].files[0].name);
            $("#delete_photo").val('1');
            $("#remove").css("display","block");
            $("#nombre_archivo").css("display","none");
            $("#display_image").addClass("disabled");
        }

    </script>
    {!! JsValidator::formRequest('Clavel\Elearning\Requests\PreguntaRequest')->selector('#formData') !!}
@stop
