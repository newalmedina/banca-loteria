@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    @parent
    <link href="{{ asset("/assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}" rel="stylesheet" type="text/css" />


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
    <li><a href="{{ url("admin/asignaturas") }}">{{ trans('elearning::asignaturas/admin_lang.asignaturas') }}</a></li>
    <li><a href="{{ url("admin/asignaturas/".$asignatura->id."/modulos") }}">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$asignatura->titulo }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @if (Session::get('success',"") != "")
        <div class="alert alert-success">
            <button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button>
            <strong>{{ date('d/m/Y H:i:s') }}</strong>
            {{ Session::get('success',"") }}
        </div>
    @endif

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
        {!! Form::model($modulo, $form_data, array('role' => 'form')) !!}
            {!! Form::hidden('asignatura_id', null, array('id' => 'asignatura_id')) !!}
            {!! Form::hidden('delete_photo', 0, array('id' => 'delete_photo')) !!}
            {!! Form::hidden('tipo_modulo_id', $tipo_modulos[0]->id, array('id' => 'tipo_modulo_id')) !!}

             <div class="@if($modulo->id!='' && Auth::user()->can("admin-modulos-convocatorias-update")) col-md-8 @else col-md-12 @endif">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

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
                            {!! Form::label('myfile', trans('elearning::modulos/admin_lang.GRUPO_IMAGEN'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="nombrefichero" readonly>
                                    <span class="input-group-btn">
                                        <div class="btn btn-primary btn-file">
                                            {{ trans('elearning::modulos/admin_lang.search_logo') }}
                                            {!! Form::file('myfile[]',array('id'=>'myfile', 'multiple'=>true)) !!}
                                        </div>
                                    </span>
                                </div>
                                <div id="remove" style="margin-top: 5px; @if($modulo->image=='') display: none; @endif">
                                    @if($modulo->image!='')
                                        <div id="nombre_archivo" style="margin-bottom:10px;">
                                            <strong>{{ trans("elearning::modulos/admin_lang.nombre_archivo") }}:</strong> {{ $modulo->image }}
                                        </div>
                                    @endif
                                    <a id="display_image" href="{{ url('modulos/openImage/'.$modulo->id) }}" target="_blank" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> {{ trans('elearning::modulos/admin_lang.view_image') }}</a>
                                    <a class="btn btn-danger" href="javascript:remove_image();"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('elearning::modulos/admin_lang.delete_image') }}</a>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('myfile', trans('elearning::modulos/admin_lang.fondo'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <div class="input-group my-colorpicker2 colorpicker-element">
                                    {!! Form::text('fondo', null, array('placeholder' => trans('elearning::modulos/admin_lang.fondo'), 'class' => 'form-control', 'id' => 'fondo')) !!}

                                    <div class="input-group-addon">
                                        <i style="background-color: rgb(136, 119, 119);"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('obligatorio_id', trans('elearning::modulos/admin_lang.obligatorio_id'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <select name="obligatorio_id" class="form-control">
                                    <option value="">{{ trans("elearning::modulos/admin_lang.sin_modulos") }}</option>
                                    @foreach($modulos as $modulo_obligatorio)
                                        <option value="{{ $modulo_obligatorio->id }}" @if($modulo_obligatorio->id==$modulo->obligatorio_id) selected @endif>{{ $modulo_obligatorio->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!--<div class="form-group">
                            {!! Form::label('tipo_modulo_id', trans('elearning::modulos/admin_lang.tipo'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <select name="tipo_modulo_id" id="tipo_modulo_id" class="form-control">
                                    <option value="">{{ trans("elearning::modulos/admin_lang.sin_tipo") }}</option>
                                    @foreach($tipo_modulos as $tipo)
                                        <option value="{{ $tipo->id }}" @if($tipo->id==$modulo->tipo_modulo_id) selected @endif>{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>-->

                        <div class="form-group">
                            {!! Form::label('puntua', trans('elearning::contenidos/admin_lang.puntua'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('puntua', '0', true, array('id'=>'puntua_0','onclick'=>'javascript:setPeso(this.value)')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('puntua', '1', false, array('id'=>'puntua_1','onclick'=>'javascript:setPeso(this.value)')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>
                        <?php
                        $tPermiso = "disabled";
                        if ((bool) $modulo->puntua) {
                            $tPermiso = "";
                        }
                        ?>
                        <div class="form-group">
                            {!! Form::label('peso', trans('elearning::modulos/admin_lang.peso'), array('class' => 'col-md-2 control-label')) !!}
                            <div class="col-md-4">
                                {!! Form::text('peso', null , array('placeholder' => trans('elearning::modulos/admin_lang.peso'), 'class' => 'form-control',$tPermiso,'maxlength'=>'4', 'id' => 'peso')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('orden', trans('elearning::modulos/admin_lang.orden'), array('class' => 'col-md-2 control-label')) !!}
                            <div class="col-md-4">
                                {!! Form::text('orden', null , array('placeholder' => trans('elearning::modulos/admin_lang.orden'), 'class' => 'form-control','maxlength'=>'4', 'id' => 'orden')) !!}
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
                                 {!!  Form::hidden('userlang['.$key.'][modulo_id]', $modulo->id, array('id' => 'modulo_id')) !!}

                                 <div class="form-group">
                                     {!! Form::label('userlang['.$key.'][nombre]', trans('elearning::modulos/admin_lang.nombre'), array('class' => 'col-sm-2 control-label')) !!}
                                     <div class="col-sm-10">
                                         {!! Form::text('userlang['.$key.'][nombre]', $modulo->{'nombre:'.$key} , array('placeholder' => trans('elearning::modulos/admin_lang.nombre'), 'class' => 'form-control', 'id' => 'nombre_'.$key)) !!}
                                     </div>
                                 </div>

                                 <div class="form-group">
                                     {!! Form::label('userlang['.$key.'][url_amigable]', trans('elearning::modulos/admin_lang.url_amigable'), array('class' => 'col-sm-2 control-label')) !!}
                                     <div class="col-sm-10">
                                         <div class="input-group">
                                             <span class="input-group-addon">{{ url("/") }}/</span>
                                             {!! Form::text('userlang['.$key.'][url_amigable]', "modulos/detalle_modulo/".$modulo->{'url_amigable:'.$key} , array('placeholder' => trans('elearning::modulos/admin_lang.url_amigable'), 'class' => 'form-control', 'readonly' => true, 'id' => 'url_amigable_'.$key)) !!}
                                         </div>
                                     </div>
                                 </div>

                                 <div class="form-group">
                                     {!! Form::label('userlang['.$key.'][descripcion]', trans('elearning::modulos/admin_lang.descripcion'), array('class' => 'col-sm-2 control-label')) !!}
                                     <div class="col-sm-10">
                                         {!! Form::textarea('userlang['.$key.'][descripcion]', $modulo->{'descripcion:'.$key} , array('class' => 'form-control textarea', 'id' => 'descripcion_'.$key)) !!}
                                     </div>
                                 </div>

                                 <div class="form-group">
                                     {!! Form::label('userlang['.$key.'][coordinacion]', trans('elearning::modulos/admin_lang.coordinacion'), array('class' => 'col-sm-2 control-label')) !!}
                                     <div class="col-sm-10">
                                         {!! Form::text('userlang['.$key.'][coordinacion]', $modulo->{'coordinacion:'.$key} , array('placeholder' => trans('elearning::modulos/admin_lang.coordinacion'), 'class' => 'form-control', 'id' => 'coordinacion_'.$key)) !!}
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

                        <a href="{{ url('/admin/asignaturas/'.$asignatura->id.'/modulos') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                    </div>

                </div>
             </div>
            @if($modulo->id!='' && Auth::user()->can("admin-modulos-convocatorias-update"))
                 <div class="col-md-4">
                    <div id="convocatorias"></div>
                </div>

                <!-- Modal para la creación/Modificación de convocatorias -->
                <div id="modalConvocatoria" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog modal-lg">
                        <div id="content_block" class="modal-content">
                            <div class="modal-header">
                                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title">{{ trans('asignaturas/admin_lang.gestionar_convocatoria') }}</h4>
                            </div>
                            <div id="responsibe_convocatoria" class="modal-body">

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin Modal para la creación/Modificación de convocatorias -->
            @endif
        {!! Form::close() !!}
    </div>

@endsection

@section('foot_page')
    <script src="{{ asset("/assets/admin/vendor/tinymce/tinymce.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/assets/admin/vendor/colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>

    <script>
        $(document).ready(function() {
            $(".select2").select2();

            $("#myfile").change(function(){
                getFileName();
            });

            $(".my-colorpicker2").colorpicker();

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

            @if($modulo->id!='' && Auth::user()->can("admin-modulos-convocatorias-update"))
                load_info_convocatorias();
            @endif

        });

        @if($modulo->id!='' && Auth::user()->can("admin-modulos-convocatorias-update"))
            function load_info_convocatorias() {
                $("#convocatorias").html("<div class='text-center'><div class='overlay'><i class='fa fa-refresh fa-spin' style='font-size: 48px;'></i></div>{{ trans("asignaturas/admin_lang.cargando") }}</div>");
                $("#convocatorias").load("{{ url("admin/modulos/convocatorias/".$modulo->id."/listado") }}");

            }
        @endif

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

        function remove_image() {
            $("#display_image").addClass("disabled");
            $("#remove").css("display","none");
            $('#nombrefichero').val('');
            $('#myfile').val("");
            $("#delete_photo").val('1');
        }

        function setPeso(val) {
            if(val == 1) {
                $("#peso").attr("disabled",false);
            }else{
                $("#peso").attr("disabled",true);
            }
        }

    </script>

    {!! JsValidator::formRequest('Clavel\Elearning\Requests\ModuloRequest')->selector('#formData') !!}
@stop
