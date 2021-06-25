@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link href="{{ asset("assets/admin/vendor/datepicker/css/bootstrap-datepicker.min.css") }}" rel="stylesheet" type="text/css" />

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
    <li><a href="{{ url("admin/posts") }}">{{ trans('posts::admin_lang.newpost') }}</a></li>
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
                    <h4 class="modal-title">{{ trans('posts::admin_lang.selecciona_un_archivo') }}</h4>
                </div>
                <div id="responsibe_images" class="modal-body">

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
                    <h4 class="modal-title">{{ trans('posts::admin_lang.preview') }}</h4>
                </div>
                <div id="content-preview" class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="row">
        {!! Form::model($post, $form_data, array('role' => 'form')) !!}
        {!! Form::hidden('permission_name', null, array('id' => 'permission_name')) !!}

        <div class="col-md-10">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("posts::admin_lang.info_menu") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('author_id', trans('posts::admin_lang.author'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-md-10">
                           {!!
                            Form::select(
                                'author_id',
                                $autores,
                                !empty($post->author_id) ? $post->author_id : null,
                                array(
                                    'class' => 'form-control select2',
                                    'id' => 'author_id',
                                    'data-placeholder' => trans('posts::admin_lang.seleccion_author'),
                                     'style' => "width: 100%;"
                                ))
                            !!}

                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('date_post', trans('posts::admin_lang.date_post'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-md-10">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                </div>
                                {!! Form::text('date_post', $post->date_post_formatted, array('placeholder' => trans('posts::admin_lang.date_post'), 'class' => 'form-control', 'id' => 'date_post')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('date_activation', trans('posts::admin_lang.date_activation'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-md-10">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                </div>
                                {!! Form::text('date_activation', $post->date_activation_formatted, array('placeholder' => trans('posts::admin_lang.date_activation'), 'class' => 'form-control', 'id' => 'date_activation')) !!}
                            </div>
                            <p class="help-block">{{ trans('posts::admin_lang.fecha_info_block') }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('date_deactivation', trans('posts::admin_lang.date_deactivation'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-md-10">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                </div>
                                {!! Form::text('date_deactivation', $post->date_deactivation_formatted, array('placeholder' => trans('posts::admin_lang.date_deactivation'), 'class' => 'form-control', 'id' => 'date_deactivation')) !!}
                            </div>
                            <p class="help-block">{{ trans('posts::admin_lang.fecha_info_block') }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('in_home', trans('posts::admin_lang.in_home'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div class="col-md-10">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {!! Form::radio('in_home', 0, true, array('id'=>'in_home_0')) !!}
                                    {{ Lang::get('general/admin_lang.no') }}</label>
                                <label class="radio-inline">
                                    {!! Form::radio('in_home', 1, false, array('id'=>'in_home_1')) !!}
                                    {{ Lang::get('general/admin_lang.yes') }} </label>
                            </div>
                        </div>
                    </div>

                    <div id="date_deactivation_home_group" class="form-group" style="@if($post->in_home==null || $post->in_home=='0') display:none; @endif">
                        {!! Form::label('date_deactivation_home', trans('posts::admin_lang.date_deactivation_home'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-md-10">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                </div>
                                {!! Form::text('date_deactivation_home', $post->date_deactivation_home_formatted, array('placeholder' => trans('posts::admin_lang.date_deactivation_home'), 'class' => 'form-control', 'id' => 'date_deactivation_home')) !!}
                            </div>
                            <p class="help-block">{{ trans('posts::admin_lang.fecha_info_block') }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('active', trans('posts::admin_lang.status'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
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

                    <div class="form-group">
                        {!! Form::label('has_shared', trans('posts::admin_lang.has_shared'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div class="col-md-10">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {!! Form::radio('has_shared', 0, true, array('id'=>'has_shared_0')) !!}
                                    {{ Lang::get('general/admin_lang.no') }}</label>
                                <label class="radio-inline">
                                    {!! Form::radio('has_shared', 1, false, array('id'=>'has_shared_1')) !!}
                                    {{ Lang::get('general/admin_lang.yes') }} </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('has_comment', trans('posts::admin_lang.has_comment'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div class="col-md-10">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {!! Form::radio('has_comment', 0, true, array('id'=>'has_comment_0')) !!}
                                    {{ Lang::get('general/admin_lang.no') }}</label>
                                <label class="radio-inline">
                                    {!! Form::radio('has_comment', 1, false, array('id'=>'has_comment_1')) !!}
                                    {{ Lang::get('general/admin_lang.yes') }} </label>
                            </div>
                        </div>
                    </div>

                    <div id="has_comment_user" class="form-group" style="@if($post->has_comment==null || $post->has_comment=='0') display:none; @endif">
                        {!! Form::label('has_comment_only_user', trans('posts::admin_lang.has_comment_only_user'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div class="col-md-10">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {!! Form::radio('has_comment_only_user', 0, true, array('id'=>'has_comment_only_user_0')) !!}
                                    {{ Lang::get('general/admin_lang.no') }}</label>
                                <label class="radio-inline">
                                    {!! Form::radio('has_comment_only_user', 1, false, array('id'=>'has_comment_only_user_1')) !!}
                                    {{ Lang::get('general/admin_lang.yes') }} </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('has_comment_only_user', trans('posts::admin_lang.imagenes'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div id="imagenes_group" class="col-md-10">
                            @if($post->images->count()>0)

                                @foreach($post->images as $key=>$value)
                                    <div id="imagen_put_{{ $key }}">
                                        <div class="input-group">
                                            {!! Form::text('image[]', $value->path, array('placeholder' => trans('posts::admin_lang.imagen'), 'class' => 'form-control copycat', 'id' => 'imagen_'.$key)) !!}
                                            <span class="input-group-btn">
                                                    <button class="btn bg-olive btn-flat" onclick="javascript:openImageController('imagen_{{$key}}', '1');" type="button">{{ trans('posts::admin_lang.image') }}</button>
                                                    <button class="btn btn-danger" onclick="javascript:deleteImageController('{{ $key }}');" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
                                                </span>
                                        </div>
                                        <br>
                                    </div>

                                @endforeach

                            @endif
                        </div>

                    </div>
                    <button class="btn bg-olive btn-flat pull-right" onclick="javascript:newImageController();" type="button">{{ trans('posts::admin_lang.new_image') }}</button>
                    <br clear="all">

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
                                @if($nX==1)- <span class="text-success">{{ trans('posts::admin_lang._defecto') }}</span>@endif
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
                            {!!  Form::hidden('userlang['.$key.'][page_id]', $post->id, array('id' => 'page_id')) !!}

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][title]', trans('posts::admin_lang.titulo'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('userlang['.$key.'][title]', $post->{'title:'.$key} , array('placeholder' => trans('posts::admin_lang.titulo'), 'class' => 'form-control textarea', 'id' => 'title_'.$key)) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][url_seo]', trans('posts::admin_lang.url_seo'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">{{ url('') }}/</span>
                                        {!! Form::text('userlang['.$key.'][url_seo]', "posts/detalle/".$post->{'url_seo:'.$key} , array('placeholder' => trans('posts::admin_lang.url_seo'), 'class' => 'form-control textarea', 'readonly' => true, 'id' => 'url_seo_'.$key)) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][body]', trans('posts::admin_lang.descripcion'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::textarea('userlang['.$key.'][body]', $post->{'body:'.$key} , array('class' => 'form-control textarea', 'id' => 'body_'.$key)) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('meta', trans('posts::admin_lang.metatags'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    <div id="accordion_{{ $key }}" class="box-group">

                                        <div class="panel box box-primary">
                                            <div class="box-header with-border">
                                                <h4 class="box-title">
                                                    <a href="#meta_{{ $key }}" data-parent="#accordion_{{ $key }}" data-toggle="collapse" aria-expanded="false" class="collapsed">
                                                        {{ trans('posts::admin_lang.metatags') }}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="panel-collapse collapse" id="meta_{{ $key }}" aria-expanded="false" style="height: 0px;">
                                                <div class="box-body">

                                                    <div class="form-group">
                                                        {!! Form::label('userlang['.$key.'][meta_title]', trans('posts::admin_lang.meta_title'), array('class' => 'col-sm-12')) !!}
                                                        <div class="col-sm-12">
                                                            {!! Form::text('userlang['.$key.'][meta_title]', $post->{'meta_title:'.$key} , array('placeholder' => trans('posts::admin_lang.meta_title'), 'class' => 'form-control textarea', 'id' => 'meta_title_'.$key)) !!}
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        {!! Form::label('userlang['.$key.'][meta_content]', trans('posts::admin_lang.meta_content'), array('class' => 'col-sm-12')) !!}
                                                        <div class="col-sm-12">
                                                            {!! Form::textarea('userlang['.$key.'][meta_content]', $post->{'meta_content:'.$key} , array('class' => 'form-control', 'id' => 'meta_content_'.$key, 'style' => 'resize:none; height: 100px;')) !!}
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

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

                    <a href="{{ url('/admin/posts') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                </div>

            </div>

        </div>

        <div class="col-md-2">
            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title"><i class="fa  fa-key" aria-hidden="true"></i> {{ trans("posts::admin_lang.permisos") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {!! Form::radio('permission', 0, true, array('id'=>'permission_0')) !!}
                                    {{ Lang::get('posts::admin_lang.sin_permioss') }}</label><br>
                                <label class="radio-inline">
                                    {!! Form::radio('permission', 2, true, array('id'=>'permission_2')) !!}
                                    {{ Lang::get('posts::admin_lang.solo_autenticados') }}</label><br>
                                <label class="radio-inline">
                                    {!! Form::radio('permission', 1, false, array('id'=>'permission_1')) !!}
                                    {{ Lang::get('posts::admin_lang.permisos_select') }} </label>
                            </div>

                            <div id="roles" style="@if(is_null($post->permission) || $post->permission=='0') display: none; @endif">
                                <div id="sel_roles" class="selector-roles" style="margin-left: 20px;">
                                    <br clear="all">
                                    <select class="form-control select2" name="sel_roles[]" multiple="multiple" data-placeholder="{{ trans('posts::admin_lang.seleccion_roles') }}" style="width: 100%;">
                                        @foreach($roles as $value)
                                            <option value="{{ $value->id }}" @if($value->postsSelected($post->id)) selected @endif>{{ $value->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title"><i class="fa fa-tags" aria-hidden="true"></i> {{ trans("posts::admin_lang.tags") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div id="tags">
                                <div id="sel_tags" class="selector-tags">
                                    <br clear="all">
                                    <select class="form-control select2" name="sel_tags[]" multiple="multiple" data-placeholder="{{ trans('posts::admin_lang.seleccion_tags') }}" style="width: 100%;">
                                        @foreach($tags as $value)
                                            <option value="{{ $value->id }}" @if($post->tagSelected($value->id)) selected @endif>{{ $value->tag }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        {!! Form::close() !!}
    </div>

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/datepicker/locales/bootstrap-datepicker.'.app()->getLocale(). '.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset("assets/admin/vendor/tinymce/tinymce.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        var image_num = @if($post->images->count()>0) {{ $post->images->count() }} @else 0; @endif

        $(document).ready(function() {
            $("#date_post, #date_activation, #date_deactivation, #date_deactivation_home").datepicker({
                isRTL: false,
                format: 'dd/mm/yyyy',
                autoclose:true,
                language: 'es'
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
                    "searchreplace visualblocks code fullscreen textcolor",
                    "insertdatetime media table contextmenu paste table"
                ],
                content_css: [
                    '{{ url('assets/front/vendor/bootstrap/css/bootstrap.min.css') }}',
                    '{{ url('assets/front/vendor/fontawesome/css/font-awesome.min.css') }}',
                    '{{ url('assets/front/css/front.min.css') }}',
                    '{{ url('assets/front/css/theme.css') }}',
                    '{{ url('assets/front/css/theme-element.css') }}',
                    '{{ url('assets/front/vendor/fontawesome/css/font-awesome.min.css') }}'

                ],
                toolbar: "insertfile undo redo | styleselect | fontsizeselect | forecolor, backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | link media image | code",
                file_browser_callback : function(field_name, url, type, win) {
                    openImageController(field_name, '0');
                }
            });

            $("#has_comment_0").click(function() {
                $("#has_comment_user").slideUp(500);
            });

            $("#has_comment_1").click(function() {
                $("#has_comment_user").slideDown(500);
            });

            $("#in_home_0").click(function() {
                $("#date_deactivation_home_group").slideUp(500);
            });

            $("#in_home_1").click(function() {
                $("#date_deactivation_home_group").slideDown(500);
            });

            $("#permission_0").click(function() {
                $("#roles").slideUp(500);
            });

            $("#permission_1").click(function() {
                $("#roles").slideDown(500);
            });

            $(".select2").select2();
        });

        function openImageController(input, only_img) {
            $('#bs-modal-images').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#responsibe_images").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
            $("#responsibe_images").load("{{ url("admin/media/viewer/") }}/" + input + "/" + only_img);

        }

        function newImageController() {
            var strInput = "";

            image_num++;

            strInput = '<div id="imagen_put_' + image_num + '">';
            strInput+= '<div class="input-group">';
            strInput+= '<input type="text" value="" name="image[]" id="imagen_' + image_num +'" class="form-control copycat" placeholder="{!! trans('posts::admin_lang.selecciona_una_image') !!}">';
            strInput+= '<span class="input-group-btn">';
            strInput+= '<button class="btn bg-olive btn-flat" onclick="javascript:openImageController(\'imagen_' + image_num +'\', \'1\');" type="button">{{ trans('posts::admin_lang.image') }}</button>';
            strInput+= '<button class="btn btn-danger" onclick="javascript:deleteImageController(\'' + image_num + '\');" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>';
            strInput+= '</span>';
            strInput+= '</div>';
            strInput+= '<br>';
            strInput+= '</div>';

            $("#imagenes_group").append(strInput);
        }

        function deleteImageController(id) {
            $("#imagen_put_" + id).remove();
        }
    </script>
    {!! JsValidator::formRequest('Clavel\Posts\Requests\AdminPostRequest')->selector('#formData') !!}
@stop
