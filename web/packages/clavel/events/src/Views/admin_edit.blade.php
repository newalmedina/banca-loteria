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
    <li><a href="{{ url("admin/events") }}">{{ trans('Events::admin_lang.newpost') }}</a></li>
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
                    <h4 class="modal-title">{{ trans('Events::admin_lang.selecciona_un_archivo') }}</h4>
                </div>
                <div id="responsibe_images" class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="row">
        {!! Form::model($event, $form_data, array('role' => 'form')) !!}
        {!! Form::hidden('permission_name', null, array('id' => 'permission_name')) !!}

        <div class="col-md-10">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("Events::admin_lang.info_menu") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('date_start', trans('Events::admin_lang.date_start'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-md-10">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                </div>
                                {!! Form::text('date_start', $event->date_start_formatted, array('placeholder' => trans('Events::admin_lang.date_start'), 'class' => 'form-control', 'id' => 'date_start')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('date_end', trans('Events::admin_lang.date_end'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-md-10">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                </div>
                                {!! Form::text('date_end', $event->date_end_formatted, array('placeholder' => trans('Events::admin_lang.date_end'), 'class' => 'form-control', 'id' => 'date_end')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('in_home', trans('Events::admin_lang.in_home'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
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

                    <div class="form-group">
                        {!! Form::label('active', trans('Events::admin_lang.status'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
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
                        {!! Form::label('has_shared', trans('Events::admin_lang.has_shared'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
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
                        {!! Form::label('has_comment_only_user', trans('Events::admin_lang.imagenes'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div id="imagenes_group" class="col-md-10">
                            @if($event->images->count()>0)

                                @foreach($event->images as $key=>$value)
                                    <div id="imagen_put_{{ $key }}">
                                        <div class="input-group">
                                            {!! Form::text('image[]', $value->path, array('placeholder' => trans('Events::admin_lang.imagen'), 'class' => 'form-control copycat', 'id' => 'imagen_'.$key)) !!}
                                            <span class="input-group-btn">
                                                    <button class="btn bg-olive btn-flat" onclick="javascript:openImageController('imagen_{{$key}}', '1');" type="button">{{ trans('Events::admin_lang.image') }}</button>
                                                    <button class="btn btn-danger" onclick="javascript:deleteImageController('{{ $key }}');" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
                                                </span>
                                        </div>
                                        <br>
                                    </div>

                                @endforeach

                            @endif
                        </div>

                    </div>
                    <button class="btn bg-olive btn-flat pull-right" onclick="javascript:newImageController();" type="button">{{ trans('Events::admin_lang.new_image') }}</button>
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
                                @if($nX==1)- <span class="text-success">{{ trans('Events::admin_lang._defecto') }}</span>@endif
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
                            {!!  Form::hidden('userlang['.$key.'][event_id]', $event->id, array('id' => 'event_id')) !!}

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][title]', trans('Events::admin_lang.titulo'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('userlang['.$key.'][title]', $event->{'title:'.$key} , array('placeholder' => trans('Events::admin_lang.titulo'), 'class' => 'form-control textarea', 'id' => 'title_'.$key)) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][url_seo]', trans('Events::admin_lang.url_seo'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">{{ url('') }}/</span>
                                        {!! Form::text('userlang['.$key.'][url_seo]', "events/detalle/".$event->{'url_seo:'.$key} , array('placeholder' => trans('Events::admin_lang.url_seo'), 'class' => 'form-control textarea', 'readonly' => true, 'id' => 'url_seo_'.$key)) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][body]', trans('Events::admin_lang.descripcion'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::textarea('userlang['.$key.'][body]', $event->{'body:'.$key} , array('class' => 'form-control textarea', 'id' => 'body_'.$key)) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][localization]', trans('Events::admin_lang.localization'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('userlang['.$key.'][localization]', $event->{'localization:'.$key} , array('placeholder' => trans('Events::admin_lang.localization'), 'class' => 'form-control textarea', 'id' => 'localization_'.$key)) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][link]', trans('Events::admin_lang.link'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('userlang['.$key.'][link]', $event->{'link:'.$key} , array('placeholder' => trans('Events::admin_lang.link'), 'class' => 'form-control textarea', 'id' => 'link_'.$key)) !!}
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

                    <a href="{{ url('/admin/events') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                </div>

            </div>

        </div>

        <div class="col-md-2">
            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title"><i class="fa  fa-key" aria-hidden="true"></i> {{ trans("Events::admin_lang.permisos") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {!! Form::radio('permission', 0, true, array('id'=>'permission_0')) !!}
                                    {{ Lang::get('Events::admin_lang.sin_permioss') }}</label><br>
                                <label class="radio-inline">
                                    {!! Form::radio('permission', 1, false, array('id'=>'permission_1')) !!}
                                    {{ Lang::get('Events::admin_lang.permisos_select') }} </label>
                            </div>

                            <div id="roles" style="@if(is_null($event->permission) || $event->permission=='0') display: none; @endif">
                                <div id="sel_roles" class="selector-roles" style="margin-left: 20px;">
                                    <br clear="all">
                                    <select class="form-control select2" name="sel_roles[]" multiple="multiple" data-placeholder="{{ trans('Events::admin_lang.seleccion_roles') }}" style="width: 100%;">
                                        @foreach($roles as $value)
                                            <option value="{{ $value->id }}" @if($value->eventsSelected($event->id)) selected @endif>{{ $value->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title"><i class="fa fa-tags" aria-hidden="true"></i> {{ trans("Events::admin_lang.tags") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div id="tags">
                                <div id="sel_tags" class="selector-tags">
                                    <br clear="all">
                                    <select class="form-control select2" name="sel_tags[]" multiple="multiple" data-placeholder="{{ trans('Events::admin_lang.seleccion_tags') }}" style="width: 100%;">
                                        @foreach($tags as $value)
                                            <option value="{{ $value->id }}" @if($event->tagSelected($value->id)) selected @endif>{{ $value->tag }}</option>
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
        var image_num = @if($event->images->count()>0) {{ $event->images->count() }} @else 0 @endif;

        $(document).ready(function() {
            $("#date_start, #date_end").datepicker({
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
                plugins: [
                    "advlist autolink lists link image charmap print preview anchor",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table contextmenu paste"
                ],
                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image | code",
                file_browser_callback : function(field_name, url, type, win) {

                    openImageController(field_name, '0');

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
            strInput+= '<input type="text" value="" name="image[]" id="imagen_' + image_num +'" class="form-control copycat" placeholder="{!! trans('Events::admin_lang.selecciona_una_image') !!}">';
            strInput+= '<span class="input-group-btn">';
            strInput+= '<button class="btn bg-olive btn-flat" onclick="javascript:openImageController(\'imagen_' + image_num +'\', \'1\');" type="button">{{ trans('Events::admin_lang.image') }}</button>';
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
    {!! JsValidator::formRequest('App\Modules\Events\Requests\AdminEventRequest')->selector('#formData') !!}

@stop
