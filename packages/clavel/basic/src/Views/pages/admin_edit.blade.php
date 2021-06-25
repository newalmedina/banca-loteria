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
    <li><a href="{{ url("admin/pages") }}">{{ trans('basic::pages/admin_lang.pages') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')
    @include('admin.includes.modals')

    <!-- Imágenes multimedia  -->
    <div class="modal modal-note fade in" id="bs-modal-images">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('basic::pages/admin_lang.selecciona_un_archivo') }}</h4>
                </div>
                <div id="responsibe_images" class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <!-- Código fuente -->
    <div class="modal modal-code fade in" id="bs-modal-code">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('basic::pages/admin_lang.codigo') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info" style="margin-right: 5px;" aria-hidden="true"></i> {{ trans("basic::pages/admin_lang.info_save_code") }}
                    </div>

                    <div id="editor" style="height: 500px;"></div>
                </div>
                <div class="modal-footer">

                    <a data-dismiss="modal" class="btn btn-default pull-left">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button onclick="javascript:changeEditor();" class="btn btn-info pull-right">{{ trans('general/admin_lang.change') }}</button>

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <!-- Vista previa -->
    <div class="modal modal-preview fade in" id="bs-modal-preview">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('basic::pages/admin_lang.preview') }}</h4>
                </div>
                <div id="content-preview" class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>



    <div class="row">
        {!! Form::model($page, $form_data, array('role' => 'form')) !!}
        {!! Form::hidden('css', null, array('id' => 'css')) !!}
        {!! Form::hidden('javascript', null, array('id' => 'javascript')) !!}
        {!! Form::hidden('permission_name', null, array('id' => 'permission_name')) !!}

        <div class="col-md-10">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("basic::pages/admin_lang.info_menu") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('active', trans('basic::pages/admin_lang.status'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div class="col-md-10">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {!! Form::radio('active', 0, true, array('id'=>'active_0')) !!}
                                    {{ Lang::get('general/admin_lang.no') }}</label>
                                <label class="radio-inline">
                                    {!! Form::radio('active', 1, false, array('id'=>'active_1')) !!}
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
                                @if($nX==1)- <span class="text-success">{{ trans('basic::pages/admin_lang._defecto') }}</span>@endif
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
                            {!!  Form::hidden('userlang['.$key.'][id]', $valor["id"], array('id' => 'id_'.$key)) !!}
                            {!!  Form::hidden('userlang['.$key.'][page_id]', $page->id, array('id' => 'page_id_'.$key)) !!}

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][title]', trans('basic::pages/admin_lang.title'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('userlang['.$key.'][title]', $page->{'title:'.$key} , array('placeholder' => trans('basic::pages/admin_lang._INSERTAR_title'), 'class' => 'form-control textarea', 'id' => 'title_'.$key)) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][url_seo]', trans('basic::pages/admin_lang.url_seo'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">{{ url('') }}/</span>
                                        {!! Form::text('userlang['.$key.'][url_seo]', "pages/".$page->{'url_seo:'.$key} , array('placeholder' => trans('basic::pages/admin_lang._INSERTAR_url_seo'), 'class' => 'form-control textarea', 'readonly' => true, 'id' => 'url_seo_'.$key)) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][body]', trans('basic::pages/admin_lang.descripcion'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::textarea('userlang['.$key.'][body]', $page->{'body:'.$key} , array('class' => 'form-control textarea', 'id' => 'body_'.$key)) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('meta', trans('basic::pages/admin_lang.metatags'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    <div id="accordion_{{ $key }}" class="box-group">

                                        <div class="panel box box-primary">
                                            <div class="box-header with-border">
                                                <h4 class="box-title">
                                                    <a href="#meta_{{ $key }}" data-parent="#accordion_{{ $key }}" data-toggle="collapse" aria-expanded="false" class="collapsed">
                                                        {{ trans('basic::pages/admin_lang.metadata') }}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="panel-collapse collapse" id="meta_{{ $key }}" aria-expanded="false" style="height: 0px;">
                                                <div class="box-body">

                                                    <div class="form-group">
                                                        {!! Form::label('userlang['.$key.'][meta_title]', trans('basic::pages/admin_lang.meta_title'), array('class' => 'col-sm-12')) !!}
                                                        <div class="col-sm-12">
                                                            {!! Form::text('userlang['.$key.'][meta_title]', $page->{'meta_title:'.$key} , array('placeholder' => trans('basic::pages/admin_lang._INSERTAR_meta_title'), 'class' => 'form-control textarea', 'id' => 'meta_title_'.$key)) !!}
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        {!! Form::label('userlang['.$key.'][meta_content]', trans('basic::pages/admin_lang.meta_content'), array('class' => 'col-sm-12')) !!}
                                                        <div class="col-sm-12">
                                                            {!! Form::textarea('userlang['.$key.'][meta_content]', $page->{'meta_content:'.$key} , array('class' => 'form-control', 'id' => 'meta_content_'.$key, 'style' => 'resize:none; height: 100px;')) !!}
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        @foreach(config("social") as $keysocial => $valuesocial)
                                            @if($valuesocial["meta"]["active"] == '1')
                                                <div class="panel box box-primary">
                                                    <div class="box-header with-border">
                                                        <h4 class="box-title">
                                                            <a href="#meta_{{ $keysocial }}_{{ $key }}" data-parent="#accordion_{{ $key }}" data-toggle="collapse" aria-expanded="false" class="collapsed">
                                                                <i class="fa {{ $valuesocial["ico"] }}" aria-hidden="true"></i> {{ $valuesocial["meta"]["label"] }}
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse collapse" id="meta_{{ $keysocial }}_{{ $key }}" aria-expanded="false" style="height: 0px;">
                                                        <div class="box-body">

                                                            @foreach($valuesocial["meta"]["inputs"] as $keyinput => $input)

                                                                <div class="form-group">
                                                                    {!! Form::label('provider['.$keysocial.']['.$key.']['.$keyinput.']', trans('general/admin_lang.'.$input["label"]), array('class' => 'col-sm-12')) !!}
                                                                    <div class="col-sm-12">
                                                                        @if($input["isimage"]=='1') <div class="input-group"> @endif
                                                                            <?php
                                                                            $tipo = $input["type"];
                                                                            ?>
                                                                            {!! Form::$tipo('provider['.$keysocial.']['.$key.']['.$keyinput.']', (isset($a_metas_providers[$keysocial][$key][$keyinput])) ? $a_metas_providers[$keysocial][$key][$keyinput] : null, array('placeholder' => trans('basic::pages/admin_lang._INSERTAR_data'), 'class' => 'form-control', "style" => ($input["type"]=='textarea') ? "height:100px; resize:none;" : null, 'id' => 'meta_title_'.$key.'_'.$keysocial."_".str_replace(":","",$keyinput))) !!}
                                                                            @if($input["isimage"]=='1')
                                                                                <span class="input-group-btn">
                                                                                      <button class="btn bg-olive btn-flat" onclick="javascript:openImageController('{{ 'meta_title_'.$key.'_'.$keysocial."_".str_replace(":","",$keyinput) }}', '1');" type="button">{{ trans('basic::pages/admin_lang.selecciona_una_image') }}</button>
                                                                                    </span>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

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

            <div class="box box-solid">

                <div class="box-footer">

                    <a href="{{ url('/admin/pages') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
                    <a onclick="javascript:showPreview();" class="btn btn-primary pull-right" style="margin-right: 10px;">{{ trans('general/admin_lang.previa') }}</a>

                </div>

            </div>

        </div>

        <div class="col-md-2">
            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title"><i class="fa  fa-key" aria-hidden="true"></i> {{ trans("basic::pages/admin_lang.permisos") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {!! Form::radio('permission', 0, true, array('id'=>'permission_0')) !!}
                                    {{ Lang::get('basic::pages/admin_lang.sin_permioss') }}</label><br>
                                <label class="radio-inline">
                                    {!! Form::radio('permission', 1, false, array('id'=>'permission_1')) !!}
                                    {{ Lang::get('basic::pages/admin_lang.permisos_select') }} </label>
                            </div>

                            <div id="roles" style="@if(is_null($page->permission) || $page->permission=='0') display: none; @endif">
                                <div id="sel_roles" class="selector-roles" style="margin-left: 20px;">
                                    <br clear="all">
                                    <select class="form-control select2" name="sel_roles[]" multiple="multiple" data-placeholder="{{ trans('basic::pages/admin_lang.seleccion_roles') }}" style="width: 100%;">
                                        @foreach($roles as $value)
                                            <option value="{{ $value->id }}" @if($value->pagesSelected($page->id)) selected @endif>{{ $value->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title"><i class="fa fa-gear" aria-hidden="true"></i> {{ trans("basic::pages/admin_lang.avanzadas") }}</h3></div>
                <div class="box-body">
                    <div style="margin-top: 5px;">
                        <a href="javascript:openFormAdvance('javascript');"><i class="fa fa-jsfiddle" style="margin-right: 5px;" aria-hidden="true"></i> {{ trans("basic::pages/admin_lang.cambiar_javascript") }}</a>
                    </div>
                    <div style="margin-top: 10px; margin-bottom: 10px;">
                        <a href="javascript:openFormAdvance('css');"><i class="fa fa-css3" style="margin-right: 5px;" aria-hidden="true"></i> {{ trans('basic::pages/admin_lang.cambiar_css') }}</a>
                    </div>
                </div>
            </div>
        </div>

        {!! Form::close() !!}
    </div>

@endsection

@section("foot_page")

    <script type="text/javascript" src="{{ asset("assets/admin/vendor/tinymce/tinymce.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("assets/admin/vendor/ace-builds/ace.js") }}" charset="utf-8"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script>
        var gtypeShow = "";
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai");

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

            $("#permission_0").click(function() {
                $("#roles").slideUp(500);
            });

            $("#permission_1").click(function() {
                $("#roles").slideDown(500);
            });

            $(".select2").select2();
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

        function openFormAdvance(typeShow) {
            $('#bs-modal-code').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });
            gtypeShow = typeShow;

            editor.getSession().setMode("ace/mode/" + typeShow);
            editor.setValue($("#" + typeShow).val());
        }

        function changeEditor() {
            $("#" + gtypeShow).val(editor.getValue());
            gtypeShow = "";
            $("#bs-modal-code").modal('hide');
        }

        function showPreview() {
            $('#bs-modal-preview').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            $.ajax({
                url: '{{ url("admin/pages/preview") }}',
                type: 'POST',
                data: {
                    'title': $("#title_{{ app()->getLocale() }}").val(),
                    'css': $("#css").val(),
                    'javascript': $("#javascript").val(),
                    'body': $("#body_{{ app()->getLocale() }}").val()
                },
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                success: function(result){
                    $("#content-preview").html(result);
                }
            });


        }
    </script>
    {!! JsValidator::formRequest('Clavel\Basic\Requests\AdminPagesRequest')->selector('#formData') !!}

@stop
