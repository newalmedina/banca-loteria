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
    <li><a href="{{ url("admin/asignaturas") }}">{{ trans('elearning::asignaturas/admin_lang.asignaturas') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')

    @include('admin.includes.modals')

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
        {!! Form::model($asignatura, $form_data, array('role' => 'form')) !!}
            {!! Form::hidden('delete_photo', 0, array('id' => 'delete_photo')) !!}

            <div class="@if($asignatura->id!='' && Auth::user()->can("admin-asignaturas-convocatorias")) col-md-8 @else col-md-12 @endif">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('activo', trans('elearning::asignaturas/admin_lang.activo'), array('class' => 'col-sm-2 control-label')) !!}
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
                            {!! Form::label('myfile', trans('elearning::asignaturas/admin_lang.GRUPO_IMAGEN'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="nombrefichero" readonly>
                                    <span class="input-group-btn">
                                        <div class="btn btn-primary btn-file">
                                            {{ trans('elearning::asignaturas/admin_lang.search_logo') }}
                                            {!! Form::file('myfile[]',array('id'=>'myfile', 'multiple'=>true)) !!}
                                        </div>
                                    </span>
                                </div>
                                <div id="remove" style="margin-top: 5px; @if($asignatura->image=='') display: none; @endif">
                                    @if($asignatura->image!='')
                                        <div id="nombre_archivo" style="margin-bottom:10px;">
                                            <strong>{{ trans("elearning::asignaturas/admin_lang.nombre_archivo") }}:</strong> {{ $asignatura->image }}
                                        </div>
                                    @endif
                                    <a id="display_image" href="{{ url('asignaturas/openImage/'.$asignatura->id) }}" target="_blank" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> {{ trans('elearning::asignaturas/admin_lang.view_image') }}</a>
                                    <a class="btn btn-danger" href="javascript:remove_image();"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('elearning::asignaturas/admin_lang.delete_image') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('sel_users', trans('elearning::asignaturas/admin_lang.cursos'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <select class="form-control select2" name="sel_cursos[]" multiple="multiple" data-placeholder="{{ trans('elearning::asignaturas/admin_lang.cursos') }}" style="width: 100%;">
                                    @foreach($cursos as $curso)
                                        <option value="{{ $curso->id }}" @if($asignatura->cursoSelected($curso->id)) selected @endif>{{ $curso->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('sel_profesores', trans('elearning::asignaturas/admin_lang.profesores'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <select class="form-control select2" name="sel_profesores[]" multiple="multiple" data-placeholder="{{ trans('elearning::asignaturas/admin_lang.profesores') }}" style="width: 100%;">
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->id }}"  @if($asignatura->profesorSelected($profesor->id)) selected @endif>({{ $profesor->id }}) {{ $profesor->userProfile->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('obligatorio_id', trans('elearning::asignaturas/admin_lang.obligatorio_id'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <select name="obligatorio_id" class="form-control">
                                    <option value="">{{ trans("elearning::asignaturas/admin_lang.sin_asignaturas") }}</option>
                                    @foreach($asignaturas as $asignatura_obligatorio)
                                        <option value="{{ $asignatura_obligatorio->id }}" @if($asignatura_obligatorio->id==$asignatura->obligatorio_id) selected @endif>{{ $asignatura_obligatorio->titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('requiere_codigo', trans('elearning::asignaturas/admin_lang.requiere_codigo'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('requiere_codigo', '0', true, array('id'=>'requiere_codigo_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('requiere_codigo', '1', false, array('id'=>'requiere_codigo_1')) !!}
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
                                {!!  Form::hidden('userlang['.$key.'][asignatura_id]', $asignatura->id, array('id' => 'asignatura_id')) !!}

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][titulo]', trans('elearning::asignaturas/admin_lang.titulo'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('userlang['.$key.'][titulo]', $asignatura->{'titulo:'.$key} , array('placeholder' => trans('elearning::asignaturas/admin_lang.titulo'), 'class' => 'form-control', 'id' => 'titulo_'.$key)) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][url_amigable]', trans('elearning::asignaturas/admin_lang.url_amigable'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <span class="input-group-addon">{{ url("/") }}/</span>
                                            {!! Form::text('userlang['.$key.'][url_amigable]', "asignaturas/detalle/".$asignatura->{'url_amigable:'.$key} , array('placeholder' => trans('elearning::asignaturas/admin_lang.url_amigable'), 'class' => 'form-control', 'readonly' => true, 'id' => 'url_amigable_'.$key)) !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][breve]', trans('elearning::asignaturas/admin_lang.breve'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::textarea('userlang['.$key.'][breve]', $asignatura->{'breve:'.$key} , array('class' => 'form-control textarea', 'id' => 'breve_'.$key)) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][descripcion]', trans('elearning::asignaturas/admin_lang.descripcion'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::textarea('userlang['.$key.'][descripcion]', $asignatura->{'descripcion:'.$key} , array('class' => 'form-control textarea', 'id' => 'descripcion_'.$key)) !!}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">

                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][creditos]', trans('elearning::asignaturas/admin_lang.creditos'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('userlang['.$key.'][creditos]', $asignatura->{'creditos:'.$key} , array('placeholder' => trans('elearning::asignaturas/admin_lang.creditos'), 'class' => 'form-control', 'id' => 'creditos_'.$key)) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][academico]', trans('elearning::asignaturas/admin_lang.academico'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('userlang['.$key.'][academico]', $asignatura->{'academico:'.$key} , array('placeholder' => trans('elearning::asignaturas/admin_lang.academico'), 'class' => 'form-control', 'id' => 'academico_'.$key)) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][caracteristica]', trans('elearning::asignaturas/admin_lang.caracteristicas'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('userlang['.$key.'][caracteristica]', $asignatura->{'caracteristica:'.$key} , array('placeholder' => trans('elearning::asignaturas/admin_lang.caracteristicas'), 'class' => 'form-control', 'id' => 'caracteristicas_'.$key)) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][plazas]', trans('elearning::asignaturas/admin_lang.plazas'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('userlang['.$key.'][plazas]', $asignatura->{'plazas:'.$key} , array('placeholder' => trans('elearning::asignaturas/admin_lang.plazas'), 'class' => 'form-control', 'id' => 'plazas_'.$key)) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][estudiantes]', trans('elearning::asignaturas/admin_lang.estudiantes'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('userlang['.$key.'][estudiantes]', $asignatura->{'estudiantes:'.$key} , array('placeholder' => trans('elearning::asignaturas/admin_lang.estudiantes'), 'class' => 'form-control', 'id' => 'estudiantes_'.$key)) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][admision]', trans('elearning::asignaturas/admin_lang.admision'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('userlang['.$key.'][admision]', $asignatura->{'admision:'.$key} , array('placeholder' => trans('elearning::asignaturas/admin_lang.admision'), 'class' => 'form-control', 'id' => 'admision_'.$key)) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][coordinacion]', trans('elearning::asignaturas/admin_lang.coordinacion'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('userlang['.$key.'][coordinacion]', $asignatura->{'coordinacion:'.$key} , array('placeholder' => trans('elearning::asignaturas/admin_lang.coordinacion'), 'class' => 'form-control', 'id' => 'coordinacion_'.$key)) !!}
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

                        <a href="{{ url('/admin/asignaturas') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                    </div>

                </div>

            </div>

            @if($asignatura->id!='' && Auth::user()->can("admin-asignaturas-convocatorias"))
                <div class="col-md-4">
                    <div id="convocatorias"></div>
                </div>
            @endif
        </div>

        {!! Form::close() !!}

    </div>

@endsection

@section('foot_page')
    <script src="{{ asset("/assets/admin/vendor/tinymce/tinymce.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        $(document).ready(function() {
            $(".select2").select2();

            $("#myfile").change(function(){
                getFileName();
            });

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

            @if($asignatura->id!='')
                load_info_convocatorias();
            @endif
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

        function remove_image() {
            $("#display_image").addClass("disabled");
            $("#remove").css("display","none");
            $('#nombrefichero').val('');
            $('#myfile').val("");
            $("#delete_photo").val('1');
        }

        function load_info_convocatorias() {
            $("#convocatorias").html("<div class='text-center'><div class='overlay'><i class='fa fa-refresh fa-spin' style='font-size: 48px;'></i></div>{{ trans("elearning::asignaturas/admin_lang.cargando") }}</div>");
            $("#convocatorias").load("{{ url("admin/asignaturas/convocatorias/".$asignatura->id."/listado") }}");
        }
    </script>

    {!! JsValidator::formRequest('Clavel\Elearning\Requests\AsignaturaRequest')->selector('#formData') !!}
@stop
