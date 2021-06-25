<link href="{{ asset("assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}" rel="stylesheet" type="text/css" />

<style>
    .select2-container--default .select2-selection--multiple {
        height: auto !important;
    }

    i.mce-i-icon-users:before {
        content: "\f007";
        font-family: FontAwesome, sans-serif;
    }
</style>

{!! Form::model($field, $form_data) !!}
    {!! Form::hidden("id", null, ["id" => 'id']) !!}
    {!! Form::hidden("newsletter_row_id", null, ["id" => 'newsletter_row_id']) !!}
    {!! Form::hidden("position", null, ["id" => 'position']) !!}
    {!! Form::hidden("type", null, ["id" => 'type']) !!}

    <div  class="form-group mt-lg">
        {!! Form::label('in_box',trans('Newsletter::admin_lang.in_box'), array("class" => 'col-sm-3 control-label')) !!}
        <div class="col-sm-3">
            <div  class="radio-list" style="float: left; margin-bottom: 10px;">
                <label class="radio-inline">
                    {!! Form::radio('in_box', 0, true, array('id'=>'in_box_0')) !!}
                    {{ Lang::get('general/admin_lang.no') }}</label>
                <label class="radio-inline">
                    {!! Form::radio('in_box', 1, false, array('id'=>'in_box_1')) !!}
                    {{ Lang::get('general/admin_lang.yes') }} </label>
            </div>
        </div>
    </div>

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab_1" data-value="post">{{trans("Newsletter::admin_lang.insertar_post") }}</a></li>
            <li><a data-toggle="tab" href="#tab_2" data-value="text">{{trans("Newsletter::admin_lang.insertar_texto") }}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab_1" class="tab-pane active">
                <br clear="all">
                <div class="form-group mt-lg">
                    {!! Form::label('post_id',trans('Newsletter::admin_lang.select_a_new'), array("class" => 'col-sm-3 control-label')) !!}
                    <div class="col-sm-9">
                        <select class="form-control select2" name="post_id" id="post_id" style="width: 100%;">
                            @foreach($posts as $value)
                                <option value="{{ $value->id }}" @if($field->post_id == $value->id) selected @endif>{{ $value->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <p class="text-primary" style="font-weight: bold; margin-top: 20px;"><i class="fa fa-gear" style="margin-right: 5px;" aria-hidden="true"></i> Opciones avanzadas</p>

                <div id="advoptions" style="display: block;">
                    <hr style="margin-top: 0px;">

                    <div  class="form-group mt-lg">
                        {!! Form::label('image_position',trans('Newsletter::admin_lang.image_position'), array("class" => 'col-sm-3 control-label')) !!}
                        <div class="col-sm-9">
                            <div class="radio-list" style="float: left; margin-bottom: 10px;">
                                <label class="radio-inline">
                                    {!! Form::radio('image_position', 'n', true, array('id'=>'image_position_n')) !!}
                                    {{ Lang::get('general/admin_lang.no') }}</label>
                                <label class="radio-inline">
                                    {!! Form::radio('image_position', 't', false, array('id'=>'image_position_t')) !!}
                                    {{ Lang::get('general/admin_lang.yes') }} </label>
                            </div>
                            <div class="direct-chat-text" style=" float: left; margin-top: 0px; margin-left: 20px;">
                                {{trans("Newsletter::admin_lang.imagen_post_efecto") }}
                            </div>
                            <br clear="all">
                        </div>
                    </div>

                    <div class="form-group mt-lg block-image">
                        {!! Form::label('image_custom',trans('Newsletter::admin_lang.image_custom'), array("class" => 'col-sm-3 control-label')) !!}
                        <div class="col-sm-9">
                            <div class="input-group">
                                {!! Form::text('image_custom', $value->path, array('placeholder' => trans('Newsletter::admin_lang.image_custom'), 'class' => 'form-control copycat', 'id' => 'image_custom')) !!}
                                <span class="input-group-btn">
                                    <button class="btn bg-olive btn-flat" onclick="javascript:openImageController('image_custom', '1');" type="button">{{ trans('Newsletter::admin_lang.image') }}</button>
                                    <button class="btn btn-danger" onclick="javascript:deleteImageController();" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-lg block-image">
                        {!! Form::label('image_position_info',trans('Newsletter::admin_lang.image_position_info'), array("class" => 'col-sm-3 control-label')) !!}
                        <div class="col-sm-9">
                            <select class="form-control" name="image_position_info" id="image_position_info" style="width: 100%;">
                                <option value="t" @if($field->image_position=='t') selected @endif>{{trans("Newsletter::admin_lang.Arriba") }}</option>
                                @if($field->row->cols=='1') <option value="l" @if($field->image_position=='l') selected @endif>{{trans("Newsletter::admin_lang.Izquierda") }}</option> @endif
                                @if($field->row->cols=='1') <option value="r" @if($field->image_position=='r') selected @endif>{{trans("Newsletter::admin_lang.Derecha") }}</option> @endif
                                <option value="b" @if($field->image_position=='b') selected @endif>{{trans("Newsletter::admin_lang.Abajo") }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mt-lg">
                        {!! Form::label('title_color',trans('Newsletter::admin_lang.title_color'), array('class' => 'col-sm-3 control-label')) !!}
                        <div class="col-md-3">
                            <div class="input-group my-colorpicker2" style="margin-bottom: 5px;">
                                <div class="input-group-addon">
                                    <i aria-hidden="true"></i>
                                </div>
                                {!! Form::text('title_color', null, array('placeholder' =>trans('Newsletter::admin_lang.title_color'), 'class' => 'form-control', 'id' => 'title_color')) !!}
                            </div>
                            <a href="javascript:default_color('title_color', '#53545e');"><i class="fa fa-undo" style="margin-right: 5px;" aria-hidden="true"></i> {{trans("Newsletter::admin_lang.color_defecto")}}</a>
                        </div>
                        {!! Form::label('text_color',trans('Newsletter::admin_lang.text_color'), array('class' => 'col-sm-3 control-label')) !!}
                        <div class="col-md-3">
                            <div class="input-group my-colorpicker2" style="margin-bottom: 5px;">
                                <div class="input-group-addon">
                                    <i aria-hidden="true"></i>
                                </div>
                                {!! Form::text('text_color', null, array('placeholder' =>trans('Newsletter::admin_lang.text_color'), 'class' => 'form-control', 'id' => 'text_color')) !!}
                            </div>
                            <a href="javascript:default_color('text_color', '#8e8e90');"><i class="fa fa-undo" style="margin-right: 5px;" aria-hidden="true"></i> {{trans("Newsletter::admin_lang.color_defecto")}}</a>
                        </div>
                    </div>

                    <div class="form-group mt-lg">
                        {!! Form::label('complete_post',trans('Newsletter::admin_lang.complete_post'), array("class" => 'col-sm-3 control-label')) !!}
                        <div class="col-sm-3">
                            <div class="radio-list" style="float: left; margin-bottom: 10px;">
                                <label class="radio-inline">
                                    {!! Form::radio('complete_post', 0, true, array('id'=>'complete_post_0')) !!}
                                    {{ Lang::get('general/admin_lang.no') }}</label>
                                <label class="radio-inline">
                                    {!! Form::radio('complete_post', 1, false, array('id'=>'complete_post_1')) !!}
                                    {{ Lang::get('general/admin_lang.yes') }} </label>
                            </div>
                        </div>
                        {!! Form::label('text_length',trans('Newsletter::admin_lang.text_length'), array('class' => 'col-sm-3 control-label', 'readonly' => true)) !!}
                        <div class="col-md-3">
                            {!! Form::text('text_length', null, array('placeholder' =>trans('Newsletter::admin_lang.text_length'), 'class' => 'form-control', 'id' => 'text_length', 'style' => 'margin-bottom: 5px;')) !!}
                            <a href="javascript:default_value();"><i class="fa fa-undo" style="margin-right: 5px;" aria-hidden="true"></i> {{trans("Newsletter::admin_lang.valor_defecto")}}</a>
                        </div>
                    </div>

                </div>
                <br clear="all">
            </div>
            <!-- /.tab-pane -->
            <div id="tab_2" class="tab-pane">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">

                        <?php
                        $nX = 1;
                        ?>
                        @foreach ($a_trans as $key => $valor)
                            <li @if($nX==1) class="active" @endif>

                                <a href="#tab_lang_{{ $key }}" data-toggle="tab" data-value="text">
                                    {{ $valor["idioma"] }}
                                    @if($nX==1)- <span class="text-success">{{ trans('Posts::admin_lang._defecto') }}</span>@endif
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
                            <div id="tab_lang_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                                {!!  Form::hidden('userlang['.$key.'][id]', $valor["id"], array('id' => 'id')) !!}

                                <div class="form-group">
                                    {!! Form::label('userlang['.$key.'][body]', trans('Newsletter::admin_lang.descripcion'), array('class' => 'col-sm-2 control-label')) !!}
                                    <div class="col-sm-10">
                                        {!! Form::textarea('userlang['.$key.'][body]', $field->{'body:'.$key} , array('class' => 'form-control textarea', 'id' => 'body_'.$key)) !!}
                                    </div>
                                </div>
                            </div>
                            <?php
                            $nX++;
                            ?>
                        @endforeach
                    </div>
                </div>

            </div>
            <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>

{!! Form::close() !!}

<script type="text/javascript" src="{{ asset("assets/admin/vendor/colorpicker/js/bootstrap-colorpicker.min.js") }}"></script>
<script type="text/javascript" src="{{ asset("assets/admin/vendor/tinymce/tinymce.min.js") }}"></script>

<script>
    $(document).ready(function() {

        for (i=0; i < tinyMCE.editors.length; i++) {
            tinyMCE.editors[i].remove();
        }

        $(".select2").select2();
        $('.my-colorpicker2').colorpicker();

        $(".nav-tabs").children("LI").click(function() {
            $("#type").val($(this).children("A").attr("data-value"));
        });

        @if($field->type=='post') $('.nav-tabs a[href="#tab_1"]').tab('show'); @endif
        @if($field->type=='text') $('.nav-tabs a[href="#tab_2"]').tab('show'); @endif

        $('.nav-tabs a').on('shown', function (e) {
            window.location.hash = e.target.hash.replace("#", "#" + prefix);
        });

        $("#complete_post_1").click(function() {
            $('#text_length').attr('readonly', true);
            $('#text_length').prop('readonly', true);
            $("#text_length").val("");
        });

        $("#complete_post_0").click(function() {
            $('#text_length').attr('readonly', false);
            $('#text_length').prop('readonly', false);
            $("#text_length").val("{{ $longitud_defecto }}");
        });

        $("#image_position_n").click(function() {
            $('#image_position_info').attr('readonly', true);
            $('#image_position_info').prop('readonly', true);
            $('.block-image').slideUp(500);
        });

        $("#image_position_t").click(function() {
            $('#image_position_info').attr('readonly', false);
            $('#image_position_info').prop('readonly', false);
            $('.block-image').slideDown(500);
        });

        @if($field->image_position=='n')
            $('#image_position_info').attr('readonly', true);
            $('#image_position_info').prop('readonly', true);
            $('.block-image').slideUp(500);
        @else
            $("#image_position_t").click();
        @endif

        @if($field->complete_post=='1')
            $('#text_length').attr('readonly', true);
            $('#text_length').prop('readonly', true);
            $("#text_length").val("");
        @endif

        $("#text_length").change(function () {
            if($(this).val()!='') {
                if (!/^([0-9])*$/.test($(this).val())) {
                    alert("El valor " + $(this).val() + " no es un número");
                    $(this).val("{{ $field->text_length }}");
                }
            }
        });

        tinymce.init({
            mode : "exact",
            selector: "textarea",
            menubar: false,
            height: 300,
            convert_urls : false,
            resize:false,
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image | code, user_button",
            file_browser_callback : function(field_name, url, type, win) {

                openImageController(field_name, '0');

            },
            setup : function(ed) {

                my_editor = ed;

                // Add a custom button
                ed.addButton('user_button', {
                    title: 'Datos de usuarios',
                    icon: 'icon-users',
                    onclick: function () {
                        $('#bs-modal-users').modal({
                            keyboard: false,
                            backdrop: 'static',
                            show: 'toggle'
                        });
                    }
                })
            }
        });
    });

    function default_value() {
        $("#complete_post_0").click();
    }

    function default_color(obj_id, new_color) {
        $('#' + obj_id).val(new_color);
        $("#" + obj_id).parent(".my-colorpicker2").children(".input-group-addon").children("I").css("background-color", new_color);
    }

    function execTC(word) {
        $('#bs-modal-users').modal("hide");
        tinyMCE.execCommand('mceInsertContent',false, word);
    }

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

    function deleteImageController() {
        $("#image_custom").val('');
    }
</script>
