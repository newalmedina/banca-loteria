@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
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
    <li><a href="{{ url("admin/asignaturas/".$contenido->modulo->asignatura->id."/modulos/") }}">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$contenido->modulo->asignatura->{"titulo:es"} }}</a></li>
    <li><a href="{{ url("admin/modulos/".$contenido->modulo_id."/contenidos/") }}">{{ trans('elearning::contenidos/admin_lang.contenidos')." ".$contenido->modulo->{"nombre:es"} }}</a></li>
    <li class="active"><a href="{{ url("admin/contenidos/".$contenido->id."/preguntas/") }}">{{ trans('elearning::contenidos/admin_lang.contenidos_listado')." ".$contenido->nombre}}</a></li>
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
        {!! Form::model($pregunta, $form_data, array('role' => 'form')) !!}

            {!! Form::hidden('contenido_id', $contenido->id, array('id' => 'modulo_id')) !!}
             <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">
                        <div class="form-group">
                            {!! Form::label('tipo_pregunta_id', trans('elearning::contenidos/admin_lang.tipos'), array('class' => 'col-md-2  control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <select name="tipo_pregunta_id" class="form-control">
                                @foreach($tipos as $tipo)
                                    <option value="{{$tipo->id}}" @if($tipo->id == $pregunta->tipo_pregunta_id) selected @endif>{{$tipo->nombre}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('orden', trans('elearning::preguntas/admin_lang.orden'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-4">
                                {!! Form::text('orden',$pregunta->orden,array('class'=>'form-control col-sm-10','id'=>'orden','placeholder'=>trans('elearning::preguntas/admin_lang.orden'))) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('grupo', trans('elearning::contenidos/admin_lang.grupo_preguntas'), array('class' => 'col-sm-2 control-label ')) !!}
                            <div class="col-sm-8">
                                <select name="grupo" id="grupo" class="form-control select2">
                                    <option value="0">{{ trans('elearning::contenidos/admin_lang.sin_grupo_preguntas') }}</option>
                                    @foreach($gruposList as $key=>$value)
                                        <option value="{{ $value->id }}"
                                                @if($value->id==$pregunta->grupo_pregunta_id) selected @endif>{{ $value->titulo }}</option>
                                    @endforeach
                                </select>

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

                            {!! Form::label('obligatoria', trans('elearning::modulos/admin_lang.obligatorio'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('obligatoria', '0', true, array('id'=>'obligatoria_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('obligatoria', '1', false, array('id'=>'obligatoria_1')) !!}
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
                                 {!!  Form::hidden('userlang['.$key.'][pregunta_id]', $pregunta->id, array('id' => 'pregunta_id')) !!}
                                 <div class="form-group">
                                     {!! Form::label('userlang['.$key.'][nombre]', trans('elearning::contenidos/admin_lang.nombre_contenido'), array('class' => 'col-sm-2 control-label')) !!}
                                     <div class="col-sm-10">
                                         {!! Form::textarea('userlang['.$key.'][nombre]', $pregunta->{'nombre:'.$key} , array('class' => 'form-control textarea', 'id' => 'nombre_'.$key)) !!}
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
                        <a href="{{ url('/admin/contenidos/'.$contenido->id."/preguntas/") }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
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
            $(".select2").select2();

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
