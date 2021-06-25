@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link href="{{ asset("assets/admin/css/newsletter_builder/newsletter-builder.css?daaa=aaasdfad") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}" rel="stylesheet" type="text/css" />

    <style>
        #spinner_gral {
            width: 100%;
            height: 100%;
            padding: 50px;
            text-align: center;
            display: none;
            position: absolute;
            z-index: 9999999;
            top:0;
            left: 0;
            background: rgba(0,0,0,0.7);
            font-size: 64px;
            color: #FFF;
        }

    </style>
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/newsletter") }}">{{ trans('Newsletter::admin_lang.newsletter') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    <div id="spinner_gral" class="overlay"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i><br>GENERANDO NEWSLETTER</div>

    @include('admin.includes.errors')
    @include('admin.includes.success')
    @include('Newsletter::admin_partials.media')
    @include('Newsletter::admin_partials.user_vars')
    @if(!empty($newsletter->template_id))
        @include('Newsletter::admin_partials.modales_edicion')
        @include('Newsletter::admin_partials.data_vars')
    @endif

    {!! Form::model($newsletter, $form_data, array('role' => 'form')) !!}
        {!! Form::hidden('generated', null, array('id' => 'generated')) !!}
        @foreach($a_trans as $key=>$value)
            {!! Form::hidden('export-textarea['.$key.']', '', array('id' => 'export-textarea-'.$key)) !!}
        @endforeach
        {!! Form::hidden('export-textarea-editable', '', array('id' => 'export-textarea-editable')) !!}
        {!! Form::hidden('custom_header', null, array('id' => 'custom_header')) !!}
        {!! Form::hidden('custom_footer', null, array('id' => 'custom_footer')) !!}

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary" id="informacion-basica">
                    <div class="box-header  with-border">
                        <h3 class="box-title">{{ trans("pages/admin_lang.info_menu") }}</h3>
                    </div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label(trans('Newsletter::admin_lang.title'), trans('Newsletter::admin_lang.title'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('name', null, array('placeholder' => trans('Newsletter::admin_lang._INSERTAR_title'), 'class' => 'form-control', 'id' => 'name')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label(trans('Newsletter::admin_lang.template'), trans('Newsletter::admin_lang.template'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-4">
                                {!! Form::select('template_id', $active_templates, !empty($design->id) ? $design->id : null , ['id'=>'plantilla', 'class' => 'form-control select2']) !!}
                            </div>
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
                <div id="tab_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                    {!!  Form::hidden('userlang['.$key.'][id]', $valor["id"], array('id' => 'id')) !!}
                    {!!  Form::hidden('userlang['.$key.'][newsletter_id]', $newsletter->id, array('id' => 'newsletter_id')) !!}

                    <div class="form-group">
                        {!! Form::label('userlang['.$key.'][subject]', trans('Newsletter::admin_lang.subject'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-10">
                            {!! Form::text('userlang['.$key.'][subject]', $newsletter->{'subject:'.$key} , array('placeholder' => trans('Newsletter::admin_lang.subject'), 'class' => 'form-control textarea', 'id' => 'subject_'.$key)) !!}
                        </div>
                    </div>

                </div>
                <?php
                $nX++;
                ?>
            @endforeach
        </div>
    </div>

    <div class="row">

        <div id="functions" class="@if($newsletter->id!='' && !empty($newsletter->template_id)) col-md-8 @else col-md-12 @endif">

            @if(!empty($newsletter->template_id))


                <div class="box box-primary"  id="preview_newsletter">

                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans("Newsletter::admin_lang.preview") }}</h3>
                        <span id="info_save" class="text-warning" style="display:none; margin-left: 15px;"><i class="fa fa-exclamation-triangle" style="margin-right: 5px;" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang.info_save") }}</span>
                    </div>

                    <div class="box-body" id="newsletter_prev" style="padding: 0; margin: 0;">
                        <div class="newsletter " style=" background-color: {{ $design->background_content }}; padding: 50px 0; margin: 0">
                            <div id="spinner" class="overlay"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>
                            <table class="structure" style="background: {{ $design->background_page }}; @if($design->border && !$design->border_shadow) border:solid 1px {{ $design->border_color }} @elseif($design->border) box-shadow: 0px 0px 1px {{ $design->border_color }}; @endif" aria-hidden="true">
                                <thead>
                                    <th scope="col">
                                        <td id="header" class="header sortable_content_template">
                                            @if($newsletter->custom_header!='')
                                                 {!! $newsletter->custom_header !!}
                                            @else
                                                {!! $design->header !!}
                                            @endif
                                        </td>
                                    </th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="sortable_content" class="content_newsletter" style="text-align: left; padding: 15px 0; background-color: {{ $design->background_page }};">
                                            <div id="not_info" style="padding: 30px; padding-top: 60px; height:150px; @if($newsletter->newsletterRows()->count()>0) display: none; @endif">
                                                <i class="fa fa-ban" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang.sin_contenido") }}
                                            </div>

                                            @if($newsletter->newsletterRows()->count()>0)
                                                @foreach($newsletter->newsletterRows()->get() as $row)
                                                    <div id="col_{{ $row->id }}" class="groupNew_content" data-value="{{ $row->cols }}">
                                                        <div class="removeItem_2">
                                                            <a href="javascript:remove_file_content('{{ $row->id }}');" class="text-danger"><i class="fa fa-times" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang.elimiar_fila") }}</a>
                                                        </div>
                                                        <table cellspacing="0" cellpadding="0" border="0" style="background-color: {{ $design->background_page }}; width: 100%;" class="news_block_content" data-value="{{ $row->id }}" aria-hidden="true">
                                                            <tbody>
                                                            <tr>
                                                                @for($nX=1; $nX<=$row->cols; $nX++)

                                                                    <td id="{{ $row->id }}_{{ $nX }}" class="noticia_principal" style=" position : relative; cursor: move; width: {{ (100 / $row->cols) }}%" valign="top">
                                                                        @if(isset($a_news[$row->id][$nX]))
                                                                            <div class="ModifyContents">
                                                                                <div onclick="showModal_content('{{ $a_news[$row->id][$nX]["id"] }}', '{{ $row->id }}', '{{ $nX }}');" class="text-success icon_mod">
                                                                                    <i class="fa fa-pencil" style="font-size: 36px;" aria-hidden="true"></i>
                                                                                </div>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                <div onclick="deleteNew('{{ $a_news[$row->id][$nX]["id"] }}', '{{ $row->id }}', '{{ $nX }}');" class="text-danger icon_mod">
                                                                                    <i class="fa fa-times" style="font-size: 36px;" aria-hidden="true"></i>
                                                                                </div>
                                                                            </div>

                                                                            <?php
                                                                            $strStyle = "";
                                                                            $strPaddingTxt = "style='text-align: left;'";
                                                                            $strPadding = "";
                                                                            $strPaddingFont = "style='text-align: left;font-size: 12px; color: #606060;'";
                                                                            $spaceTitle = "";

                                                                            if (!is_null($design) && $a_news[$row->id][$nX]["in_box"]=='1') {
                                                                                $strStyle = "style='";
                                                                                if ($design->resaltar_border=='1') {
                                                                                    $strStyle.= "border: 1px solid ".$design->border_color.";";
                                                                                }
                                                                                if ($design->resaltar_sombra=='1') {
                                                                                    $strStyle.= "box-shadow:0px 0px 17px 1px ".$design->border_color.";";
                                                                                }
                                                                                $strStyle.= "background-color: ".$design->resaltar_background_color.";'";
                                                                                $strPaddingTxt = "style='text-align: left; padding:10px;'";

                                                                                if (isset($a_news[$row->id][$nX]["post"])) {
                                                                                    $strPadding = "style= 'padding:10px;'";
                                                                                    $strPaddingFont = "style='text-align: left;font-size: 12px;color: ".$a_news[$row->id][$nX]["post"]["text_color"]."; padding:10px;'";
                                                                                    if ($a_news[$row->id][$nX]["post"]["img"]!='' && ($a_news[$row->id][$nX]["post"]["image_position"]=='t' || $a_news[$row->id][$nX]["post"]["image_position"]=='b')) {
                                                                                        $spaceTitle = "margin:0;";
                                                                                    }
                                                                                }
                                                                            }
                                                                            ?>

                                                                            @if(isset($a_news[$row->id][$nX]["post"]))
                                                                                @foreach($a_trans as $key=>$value)
                                                                                    <div class="body_{{$key}}" @if($key!=config("app.locale")) style="display:none" @endif>
                                                                                        <table width='100%' {!! $strStyle !!} data-url="{{ url("posts/post") }}/{{ $a_news[$row->id][$nX]["post"]["url_seo_".$key] }}" aria-hidden="true">

                                                                                            <?php
                                                                                            $padding_blcok=($a_news[$row->id][$nX]["in_box"]==1 && $design->resaltar_border=='1') ? 84:60;
                                                                                            $with = 600 - $padding_blcok;
                                                                                            if ($row->cols>1) {
                                                                                                $with = $with / $row->cols;
                                                                                                $padding = 17 * ($row->cols - 1);
                                                                                                $with = $with - $padding;
                                                                                            }
                                                                                            ?>
                                                                                        <tbody class='noSortable'>
                                                                                            @if($a_news[$row->id][$nX]["post"]["img"]!='' && $a_news[$row->id][$nX]["post"]["image_position"]=='t')
                                                                                                <tr>
                                                                                                    <td {!! $strPadding !!}><img width="{{ $with }}" src="{{$a_news[$row->id][$nX]["post"]["img"]}}" alt="" /></td>
                                                                                                </tr>
                                                                                            @endif
                                                                                            <tr>
                                                                                                @if($a_news[$row->id][$nX]["post"]["img"]!='' && $a_news[$row->id][$nX]["post"]["image_position"]=='l')
                                                                                                    <td rowspan="3" width="50%" valign="top" {!! $strPadding !!}><img width="255" src="{{$a_news[$row->id][$nX]["post"]["img"]}}" alt="" /></td>
                                                                                                @endif

                                                                                                <td {!! $strPaddingTxt !!}>
                                                                                                    <h4 class="titulo" style="{{ $spaceTitle }} color: {{ $a_news[$row->id][$nX]["post"]["title_color"] }};">
                                                                                                        {!! $a_news[$row->id][$nX]["post"]["title_".$key] !!}
                                                                                                    </h4>
                                                                                                </td>

                                                                                                @if($a_news[$row->id][$nX]["post"]["img"]!='' && $a_news[$row->id][$nX]["post"]["image_position"]=='r')
                                                                                                    <td rowspan="3" width="50%" valign="top" {!! $strPadding !!}><img width="{{ $with }}" src="{{$a_news[$row->id][$nX]["post"]["img"]}}" alt="" /></td>
                                                                                                @endif
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td {!! $strPaddingFont !!}>{{ $a_news[$row->id][$nX]["post"]["fecha"] }} {{ $a_news[$row->id][$nX]["post"]["fuente"] }}</td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td {!! $strPaddingTxt !!}>
                                                                                                    <div class="texto"  style="color: {{ $a_news[$row->id][$nX]["post"]["text_color"] }};">
                                                                                                        {!! $a_news[$row->id][$nX]["post"]["text_".$key] !!}
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>

                                                                                            @if($a_news[$row->id][$nX]["post"]["img"]!='' && $a_news[$row->id][$nX]["post"]["image_position"]=='b')
                                                                                                <tr>
                                                                                                    <td {!! $strPadding !!}><img width="255" src="{{$a_news[$row->id][$nX]["post"]["img"]}}" alt="' /></td>
                                                                                                </tr>
                                                                                            @endif
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                @endforeach
                                                                            @else
                                                                                @foreach($a_trans as $key=>$value)
                                                                                    <div class="body_{{$key}}" @if($key!=config("app.locale")) style="display:none" @endif>
                                                                                        <table width='100%' {!! $strStyle !!} aria-hidden="true">
                                                                                            <tbody class='noSortable'>
                                                                                            <tr>
                                                                                                <td {!! $strPaddingTxt !!}>
                                                                                                    <div class="texto" style=" font-color:{{ $design->font_color }} !important;">{!! $a_news[$row->id][$nX]["value"]["text_".$key] !!}</div>
                                                                                                </td>
                                                                                            </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                @endforeach
                                                                            @endif
                                                                        @else
                                                                            <div class="text-center action_sort">
                                                                                <a href="javascript:showModal_content('', '{{ $row->id }}', '{{ $nX }}');">
                                                                                    {{ trans("Newsletter::admin_lang.arrastrar_contenido_aqui") }}<br>
                                                                                    <i style="font-size:18px; margin-top:10px;" class="fa fa-share-square-o" aria-hidden="true"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                @endfor
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endforeach
                                            @endif

                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td id="footer" class="footer sortable_content_template">
                                            @if($newsletter->custom_footer!='')
                                                {!! $newsletter->custom_footer !!}
                                            @else
                                                {!! $design->footer !!}
                                            @endif
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                @endif

                <div class="box box-solid">

                    <div class="box-footer">

                        <a href="{{ url('/admin/newsletter') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="button" id="newsletter-builder-save" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
                    </div>

                </div>
            </div>

            @if($newsletter->id!='' && !empty($newsletter->template_id))
                <div class="col-md-4">

                    <div class="box box-primary" id="panel-columnas">
                        <div class="box-header  with-border">
                            <h3 class="box-title">{{ trans("Newsletter::admin_lang.acciones") }}</h3>
                        </div>
                        <div  class="box-body contenido_container">
                            {!! Form::label('has_header', trans('Newsletter::admin_lang.columnas_info'), array('class' => 'col-sm-12', 'readonly' => true)) !!}
                            <em class="text-primary">{{ trans("Newsletter::admin_lang.arrastrar") }}</em>
                            <div style="padding-top: 10px; padding-bottom: 20px;">
                                <div class="columns_btn" data-value="1">
                                    <img src="{{ asset("assets/img/admin/newsletter/layouts.png") }}" style="margin-right: 10px;" alt=""> {{ trans("Newsletter::admin_lang.one_layout") }}
                                </div>
                                <div class="columns_btn" data-value="2">
                                    <img src="{{ asset("assets/img/admin/newsletter/layouts_2_equal.png") }}" style="margin-right: 10px;" alt=""> {{ trans("Newsletter::admin_lang.two_layout") }}
                                </div>
                                <div class="columns_btn" data-value="3">
                                    <img src="{{ asset("assets/img/admin/newsletter/layouts_3.png") }}" style="margin-right: 10px;" alt=""> {{ trans("Newsletter::admin_lang.three_layout") }}
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="box box-primary" id="panel-deshacer">
                        <div class="box-header  with-border">
                            <h3 class="box-title">{{ trans("Newsletter::admin_lang.deshacer") }}</h3>
                        </div>
                        <div  class="box-body contenido_container">
                            {!! Form::label('has_deshacer', trans('Newsletter::admin_lang.deshacer_info'), array('class' => 'col-sm-12', 'readonly' => true)) !!}
                            <br clear='all'>
                            <br clear='all'>
                            <a href="javascript:deshacer();" class="btn btn-primary btn-block">{{ trans("Newsletter::admin_lang.deshacer") }}</a>

                        </div>
                    </div>

                </div>
            @endif

        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset("assets/admin/vendor/colorpicker/js/bootstrap-colorpicker.min.js") }}"></script>
    @if(!empty($newsletter->id) && !is_null($design))
        <script>
            var objQuery;
            var tinyMCE;
            var enviando = false;

            $(function() {
                $(".select2").select2();
                $('.my-colorpicker2').colorpicker();

                //DROPDOWNBUTTON PLANTILLAS- SI CAMBIAMOS EL DIV DE LA NEWSLETTER A DISPLAY:NONE
                $('#plantilla').on('change', function() {
                    $("#error-plantilla").remove();
                    var selected_value=this.value;
                    if(selected_value!={{$design->id}}){
                        $("#preview_newsletter").css("display","none");
                        $("#informacion-basica").append("<div id='error-plantilla' class='alert alert-danger'>Debe de guardar una plantilla para seguir editando</div>");
                        $("#panel-columnas").css("display","none");
                        $("#panel-deshacer").css("display","none");
                        $("#functions").removeClass("col-md-8");
                        $("#functions").addClass("col-md-12");
                    }
                    else{
                        $("#preview_newsletter").css("display","block");
                        $("#panel-columnas").css("display","block");
                        $("#panel-deshacer").css("display","block");
                        $("#functions").removeClass("col-md-12");
                        $("#functions").addClass("col-md-8");
                    }

                }).change();

                //GENERAR NEWSLETTER
                $("#newsletter-builder-save").click(function() {
                    var spinner = $("#spinner_gral");
                    spinner.css('display',"block");
                    $('html, body').animate({ scrollTop: 0 }, 'fast');

                    if($("#preview_newsletter").css("display")=="block" && !enviando) {
                        enviando = true;
                        $("#generated").val('1');
                        $("#custom_header").val($("#header").html());
                        $("#custom_footer").val($("#footer").html());
                        $(".removeItem, .removeItem_2, .ModifyContents, #spinner, .modifyItem, .not_info_hf, .not_info").remove();

                        @foreach($a_trans as $key=>$value)
                            $(".content_newsletter .noticia_principal").each(function() {
                                var $_obj = $(this).children(".body_{{ $key }}").children("TABLE");
                                var url = $_obj.attr("data-url");

                                if(url!==undefined) {
                                    var $_titulo = $_obj.children("TBODY").children("TR").children("TD").children(".titulo");
                                    var $_texto = $_obj.children("TBODY").children("TR").children("TD").children(".texto");
                                    var $_img = $_obj.children("TBODY").children("TR").children("TD").children("IMG");

                                    $_titulo.html("<a href='" +url+ "' style='color:" +$_titulo.css("color")+ "'>"+$_titulo.html()+"</a>");
                                    $_texto.html("<a href='" +url+ "' style='color:" +$_texto.css("color")+ "'>"+$_texto.html()+"</a>");

                                    if($_img!==undefined) {
                                        var $_a = $('<a/>').attr('href', url);
                                        $_img.wrap($_a);
                                        $_img.css("border","none");
                                    }
                                }

                            });
                            $(".noticia_principal").children("DIV").css("display","none");
                            $(".noticia_principal").children(".body_{{ $key }}").css("display","block");
                            ajustarTamanyoImagenOutlook();
                            $("#export-textarea-{{ $key }}").val($("#newsletter_prev").html());
                        @endforeach

                        $(".noticia_principal").css("cursor","default");
                        $(".news_block_content").css("background-color","transparent");
                        $("#export-textarea-editable").val($("#newsletter_prev").html());
                    }

                    $("#formData").submit();
                });

                $( "#sortable_content" ).sortable({
                    revert: true,
                    receive: function() {
                        $("#not_info").css("display", "none");
                        updateListado();
                    },
                    stop: function( event, ui ) {
                        reorderTables();
                    }
                });

                // Drag And Drop columnas
                $( ".sortable_content_template" ).sortable({
                    revert: true,
                    receive: function() {
                        $(this).children(".not_info_hf").css("display", "none");
                        create_info_header_footer();
                    },
                    stop: function( event, ui ) {
                        $("#spinner").css('display',"none");
                        updateListadoHeaderFooter();
                    }
                });

                $( ".columns_btn" ).draggable({
                    connectToSortable: "#sortable_content, .sortable_content_template",
                    helper: "clone",
                    revert: "invalid",
                    stop: function(event, ui) {
                        objQuery = $(ui.helper);
                    }
                });

                @foreach($a_trans as $key=>$value)
                    $(".body_{{ $key }}").css("display","none");
                @endforeach
                $(".body_{{ config('app.locale') }}").css("display","block");

                $( "ul, li" ).disableSelection();
            });

            /* CABECERA Y FOOTER FUNCIONES */
            function create_info_header_footer() {
                var numColumns = objQuery.attr("data-value");
                var spinner = $("#spinner");
                var data = 0;

                $(".groupNew").each(function() {
                    if(data<$(this).attr("data-value")) data = $(this).attr("data-value");
                });
                data++;

                objQuery.removeAttr('style');
                objQuery.removeAttr('class');
                objQuery.addClass('groupNew');
                objQuery.attr("data-value", data);
                spinner.css('height', $(".structure").height() + 150);
                spinner.css('display', "block");
                objQuery.css("cursor", "move");
                objQuery.html(createGroupTable(numColumns, data, '1'));
                objQuery.children("TABLE").addClass("news_block");
                objQuery.children("TABLE").attr("data-value", data);
                objQuery.attr("id", "col_" + data);
                objQuery.prepend( '<div class="modifyItem"><a href="javascript:openFormato(\'' + data + '\');" class="text-primary"><i class="fa fa-pencil" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang_template.modificar_fila") }}</a></div>');
                objQuery.prepend( '<div class="removeItem"><a href="javascript:remove_file(\'' + data + '\');" class="text-danger"><i class="fa fa-times" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang.elimiar_fila") }}</a></div>');
            }

            function updateListadoHeaderFooter() {
                var spinner = $("#spinner");

                spinner.css('height',$(".structure").height() + 150);
                spinner.css('display',"block");
                $("#custom_header").val($("#header").html());
                $("#custom_footer").val($("#footer").html());

                $.ajax({
                    url: "{{ url("admin/newsletter/custom_template") }}",
                    type: 'POST',
                    async: false,
                    headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                    data: {
                        newsletter_id: {{ $newsletter->id }},
                        custom_header: $("#custom_header").val(),
                        custom_footer: $("#custom_footer").val()
                    },
                    success: function (data) {
                        $("#spinner").css('display',"none");
                   }
                });
            }

            function remove_file(fileToRemove) {

                if(confirm("{!! trans('Newsletter::admin_lang.sure_delete') !!}")) {
                    var spinner = $("#spinner");
                    var $_obj = $("#col_" + fileToRemove);

                    spinner.css('height',$(".structure").height() + 150);
                    spinner.css('display',"block");

                    _parent = $_obj.parent(".sortable_content_template");

                    $_obj.remove();
                    if(_parent.children( ".groupNew" ).length==0) {
                        _parent.html('<div class="not_info_hf text-danger" style="padding: 30px; padding-top: 60px; height:150px;"><i class="fa fa-ban" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang.sin_contenido") }}</div>');
                    }

                    updateListadoHeaderFooter();
                    spinner.css('display',"none");

                }

            }

            function deshacer() {
                if(confirm("¿Esta seguro que desea recuperar la cabecera y pie de la plantilla?")) {
                    var spinner = $("#spinner");
                    spinner.css('height',$(".structure").height() + 150);
                    spinner.css('display',"block");
                    $.ajax({
                        url: "{{ url("admin/newsletter/deshacer") }}",
                        type: 'POST',
                        async: false,
                        headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                        data: {
                            newsletter_id: "{{ $newsletter->id }}"
                        },
                        success: function (data) {
                            $("#header").html(data.header);
                            $("#footer").html(data.footer);
                            updateListadoHeaderFooter();
                            spinner.css('display',"none");
                        }
                    });
                }
            }

            function openFormato(id) {
                var _parent = $("#col_" + id).children("TABLE").children("TBODY").children("TR").children(".noticia_principal");
                var background = _parent.css("background-image");
                var values = getImage(background);

                $("#row_file").val(id);
                $("#tamanyo").val(_parent.css("border-bottom-width"));
                $("#border_color_2").val(rgbToHex(_parent.css("border-bottom-color")))
                        .parent(".my-colorpicker2")
                        .children(".input-group-addon")
                        .children("I")
                        .css("background-color", rgbToHex(_parent.css("border-bottom-color")));
                $("#padding").val(_parent.css("padding"));

                if(background!='none') {
                    $("#radial").val(values[0]);
                    $("#inicial").val(rgbToHex(values[1]))
                            .parent(".my-colorpicker2")
                            .children(".input-group-addon")
                            .children("I")
                            .css("background-color", rgbToHex(values[1]));
                    $("#final").val(rgbToHex(values[2])).parent(".my-colorpicker2")
                            .children(".input-group-addon")
                            .children("I")
                            .css("background-color", rgbToHex(values[2]));
                } else {
                    $("#inicial").val();
                    $("#final").val();
                }


                $('#modifyStyle').modal({
                    keyboard: false,
                    backdrop: 'static',
                    show: 'toggle'
                });

            }

            function save_formato() {
                var _parent = $("#col_" + $("#row_file").val()).children("TABLE").children("TBODY").children("TR").children(".noticia_principal");

                _parent.css("padding", $("#padding").val());
                _parent.css("border-bottom-width", $("#tamanyo").val());
                _parent.css("border-bottom-style", "solid");
                _parent.css("border-bottom-color", $("#border_color_2").val());
                _parent.css("background-color", "transparent");
                if($("#inicial").val()!='' && $("#final").val()!='') {
                    _parent.css("background-image", "linear-gradient("+$("#radial").val()+", "+$("#inicial").val()+", "+$("#final").val()+")");
                } else {
                    _parent.css("background-image", "none");
                    if($("#inicial").val()!='') _parent.css("background-color", $("#inicial").val());
                    if($("#final").val()!='') _parent.css("background-color", $("#final").val());
                }


                $('#modifyStyle').modal('hide');
                updateListadoHeaderFooter();
            }

            function componentFromStr(numStr, percent) {
                var num = Math.max(0, parseInt(numStr, 10));
                return percent ?
                        Math.floor(255 * Math.min(100, num) / 100) : Math.min(255, num);
            }

            function rgbToHex(rgb) {
                var rgbRegex = /^rgb\(\s*(-?\d+)(%?)\s*,\s*(-?\d+)(%?)\s*,\s*(-?\d+)(%?)\s*\)$/;
                var result, r, g, b, hex = "";
                if ( (result = rgbRegex.exec(rgb)) ) {
                    r = componentFromStr(result[1], result[2]);
                    g = componentFromStr(result[3], result[4]);
                    b = componentFromStr(result[5], result[6]);

                    hex = "#" + (0x1000000 + (r << 16) + (g << 8) + b).toString(16).slice(1);
                }
                return hex;
            }

            function getImage(background) {
                background = background.replace("linear-gradient(","");
                background = background.replace("))","");
                background = background.replace(", rgb","|rgb");
                background = background.replace("), ",")|");
                background = background + ")";
                return background.split("|");
            }

            function showModal(row_id, posicion) {
                var content_div = $("#content-add-info-hf");
                var my_url = "{{ url("admin/newsletter/form/plantilla_contenidos/") }}/" + row_id + "/" + posicion;

                content_div.html('<div id="spinner2" class="overlay" style="text-align: center"><i class="fa fa-refresh fa-spin" style="font-size: 64px;" aria-hidden="true"></i></div>');

                $('#modalContenidos').modal({
                    keyboard: false,
                    backdrop: 'static',
                    show: 'toggle'
                });

                content_div.load(my_url);
            }

            /* FUNCIONES DE CONTENIDO */

            function updateListado() {
                var numColumns = objQuery.attr("data-value");
                var spinner = $("#spinner");

                objQuery.removeAttr('style');
                objQuery.removeAttr('class');
                objQuery.addClass('groupNew_content');
                spinner.css('height',$(".structure").height() + 150);
                spinner.css('display',"block");
                objQuery.css("cursor","move");
                objQuery.html(createGroupTable(numColumns, '', '0'));

                $.ajax({
                    url: "{{ url("admin/newsletter/set_row") }}",
                    type: 'POST',
                    async: false,
                    headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                    data: {
                        id: "{{ $newsletter->id }}",
                        col: numColumns
                    },
                    success: function (data) {
                        if(data=='NOK') {
                            $("#not_info").css("display", "block");
                            objQuery.remove();
                            alert("ERROR");
                        } else {
                            objQuery.html(createGroupTable(numColumns, data, '0'));
                            objQuery.children("TABLE").addClass("news_block_content");
                            objQuery.children("TABLE").attr("data-value", data);
                            objQuery.attr("id", "col_" + data);
                            objQuery.prepend( '<div class="removeItem_2"><a href="javascript:remove_file_content(\'' + data + '\');" class="text-danger"><i class="fa fa-times" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang.elimiar_fila") }}</a></div>');
                            reorderTables();
                        }
                        updateListadoHeaderFooter();

                        $("#spinner").css('display',"none");
                    }
                });

            }

            function reorderTables() {
                var idspos = "";

                $(".news_block_content").each(function() {
                    if(idspos!='') idspos+=',';
                    idspos+= $(this).attr("data-value");
                });

                if(idspos!='') {
                    var spinner = $("#spinner");

                    spinner.css('height',$(".structure").height() + 150);
                    spinner.css('display',"block");

                    $.ajax({
                        url: "{{ url("admin/newsletter/reorder") }}",
                        type: 'POST',
                        async: false,
                        headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                        data: {
                            idspos: idspos
                        },
                        success: function (data) {
                            $("#spinner").css('display',"none");
                            $("#info_save").fadeIn(500);
                        }
                    });
                }

            }

            function createGroupTable(numColumns, row_id, hf) {
                var strTableNews = "";
                var perwidth = 100 / numColumns;

                strTableNews+= "<table border='0' cellpadding='0' cellspacing='0' style='width: 100%;'>";
                strTableNews+= "<tr>";
                strTableNews+= tdContent(row_id, 1, perwidth, hf);
                if(numColumns>1) {
                    strTableNews+= tdContent(row_id, 2, perwidth, hf);
                }
                if(numColumns>2) {
                    strTableNews+= tdContent(row_id, 3, perwidth, hf);
                }
                strTableNews+= "</tr>";
                strTableNews+= "</table>";

                return strTableNews;
            }

            function tdContent(row_id, posicion, perwidth, hf) {
                var strTableNews = "";
                var strBackground = "background-color: {{ $design->background_page }};";

                strTableNews+= "<td id='"+row_id+"_"+posicion+"' class='noticia_principal' style='" + strBackground + " width: " + perwidth + "%; position : relative; cursor: move;' valign='top'>";
                if(hf=='0') {
                    strTableNews+= "<div class='text-center action_sort'><a href='javascript:showModal_content(\"\", \""+row_id+"\", \""+posicion+"\");'>{{ trans("Newsletter::admin_lang.arrastrar_contenido_aqui") }}<br><i class='fa fa-share-square-o' style='font-size:18px; margin-top:10px;'></i></a></div>";
                } else {
                    strTableNews+= "<div class='text-center action_sort'><a href='javascript:showModal(\""+row_id+"\", \""+posicion+"\");'>{{ trans("Newsletter::admin_lang.arrastrar_contenido_aqui") }}<br><i class='fa fa-share-square-o' style='font-size:18px; margin-top:10px;'></i></a></div>";
                }

                strTableNews+= "</td>";

                return strTableNews;
            }

            function remove_file_content(fileToRemove) {

                if(confirm("{!! trans('Newsletter::admin_lang.sure_delete') !!}")) {
                    var spinner = $("#spinner");

                    spinner.css('height',$(".structure").height() + 150);
                    spinner.css('display',"block");

                    $.ajax({
                        url: "{{ url("admin/newsletter/delete_row") }}",
                        type: 'POST',
                        async: false,
                        headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                        data: {
                            idrow: fileToRemove
                        },
                        success: function (data) {
                            $("#col_" + fileToRemove).remove();
                            if($( ".groupNew_content" ).length==0) {
                                $("#not_info").css("display", "block");
                                $("#spinner").css('display',"none");
                            } else {
                                reorderTables();
                            }
                        }
                    });
                }

            }

            /* Modal para editar Newsletter*/
            function showModal_content(id, row_id, posicion) {
                var content_div = $("#content-add-info");
                var my_url = "{{ url("admin/newsletter/form/".$design->id."/") }}/" + row_id + "/" + posicion + "/" + id;

                content_div.html('<div id="spinner2" class="overlay" style="text-align: center"><i class="fa fa-refresh fa-spin" style="font-size: 64px;" aria-hidden="true"></i></div>');

                $('#bs-modal-add-info').modal({
                    keyboard: false,
                    backdrop: 'static',
                    show: 'toggle'
                });

                content_div.load(my_url);
            }

            function deleteNew(id, row_id, posicion) {

                if(confirm("{!! trans('Newsletter::admin_lang.sure_delete_2') !!}")) {
                    var my_url = "{{ url("admin/newsletter/delete_post/") }}";
                    var strTableNews = "<div class='text-center action_sort'><a href='javascript:showModal_content(\"\", \"" + row_id + "\", \"" + posicion + "\");'>{{ trans("Newsletter::admin_lang.arrastrar_contenido_aqui") }}<br><i class='fa fa-share-square-o' style='font-size:18px; margin-top:10px;'></i></a></div>";
                    var spinner = $("#spinner");

                    spinner.css('height', $(".structure").height() + 150);
                    spinner.css('display', "block");

                    $.ajax({
                        url: my_url,
                        type: 'POST',
                        async: false,
                        headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                        data: {
                            id: id
                        },
                        success: function (data) {
                            $("#" + row_id + "_" + posicion).html(strTableNews);
                            $("#spinner").css('display', "none");
                            $("#info_save").fadeIn(500);
                        }
                    });
                }

            }

            function save_field() {
                var row_id = $("#newsletter_row_id").val();
                var position = $("#position").val();
                var noticia_id = $("#post_id").val();
                var frm = $("#formField");
                var imgtop = "";
                var imgleft = "";
                var imgright = "";
                var imgbottom = "";

                tinyMCE.triggerSave();

                $.ajax({
                    type: "POST",
                    url: frm.attr("action"),
                    data: frm.serialize(), // Adjuntar los campos del formulario enviado.
                    success: function(data) {
                        var title_color = "color:{{ $design->font_color }};";
                        var text_color = "style= 'color:{{ $design->font_color }};'";
                        var strStyle = "";
                        var strPadding = "";
                        var spaceTitle = "";
                        var strPaddingTxt = "style='text-align: left;'";
                        var strPaddingFont = "style='text-align: left;font-size: 12px;color: #606060;'";

                        if(data[0].in_box) {
                            strStyle = "style='";
                            @if($design->resaltar_border=='1') strStyle+= "border: 1px solid {{ $design->border_color }};"; @endif
                            @if($design->resaltar_sombra=='1') strStyle+= "box-shadow:0px 0px 17px 1px {{ $design->border_color }};"; @endif
                            strStyle+= "background-color: {{ $design->resaltar_background_color }};'";
                            strPadding = "style= 'padding:10px;'";
                            strPaddingTxt = "style='text-align: left; padding:10px;'";
                            strPaddingFont = "style='text-align: left;font-size: 12px;color: #606060; padding:10px;'";
                        }

                        if(data[0].type=='post') {

                            if (data[0].img != "") {
                                var padding_blcok=(data[0].in_box==1 && {{ $design->resaltar_border }}=='1') ? 84:60;
                                var m_with = 600 - padding_blcok;
                                if (data[0].cols > 1) {
                                    m_with = m_with / data[0].cols;
                                    padding = 17 * (data[0].cols - 1);
                                    m_with = m_with - padding;
                                }

                                if(data[0].image_position=='t' || data[0].image_position=='b') {
                                    if(data[0].in_box) spaceTitle = "margin:0;";
                                    if (data[0].image_position == 't') imgtop = "<tr><td "+strPadding+"><img width='" + m_with + "' src='" + data[0].img + "' /></td></tr>";
                                    if (data[0].image_position == 'b') imgbottom = "<tr><td "+strPadding+"><img width='" + m_with + "' src='" + data[0].img + "' /></td></tr>";
                                }

                                if (data[0].image_position == 'l') imgleft = '<td rowspan="3" width="255" valign="top" '+strPadding+'><img width="100%" src="'+ data[0].img +'" /></td>';
                                if (data[0].image_position == 'r') imgright = '<td rowspan="3" width="255" valign="top" '+strPadding+'><img width="100%" src="'+ data[0].img +'" /></td>';
                            }

                            if(data[0].title_color!='') title_color = "color: " + data[0].title_color + ";";
                            if(data[0].text_color!='') text_color = "style='color: " + data[0].text_color + ";'";

                            noticia_principal = "<div class='ModifyContents'><div onclick='showModal_content(\"" + data[0].id + "\", \"" + row_id + "\", \"" + position + "\");' class='text-success icon_mod'>" +
                                "<i class='fa fa-pencil' style='font-size: 36px;'></i>" +
                                "</div>&nbsp;&nbsp;&nbsp;&nbsp;" +
                                "<div onclick='deleteNew(\"" + data[0].id + "\", \"" + row_id + "\", \"" + position + "\");' class='text-danger icon_mod'>" +
                                "<i class='fa fa-times' style='font-size: 36px;'></i>" +
                                "</div></div>";
                            @foreach($a_trans as $key=>$value)
                                noticia_principal+= "<div class='body_{{ $key }}' @if(config("app.locale")!=$key)style='display:none' @endif>" +
                                                    "<table width='100%' "+strStyle+" data-url='{{ url("posts/post") }}/" + data[0].url_seo_{{ $key }} + "'><tbody class='noSortable'>" +
                                                    imgtop +
                                                    "<tr>" +imgleft+ "<td "+strPaddingTxt+">" +
                                                    "<h4 class='titulo' style='" + spaceTitle + title_color + "'>"+
                                                    data[0].title_{{ $key }} +
                                                    "</h4>"+
                                                    "</td>" +imgright+ "</tr>" +
                                                    "<tr><td "+strPaddingFont+" >" +data[0].fecha+" "+data[0].fuente+"</td></tr>" +
                                                    "<tr><td "+strPaddingTxt+">" +
                                                    "<div class='texto' " + text_color + ">"+
                                                    data[0].text_{{ $key }} +
                                                    "</div>"+
                                                    "</td></tr>" +
                                                    imgbottom +
                                                    "</tbody></table></div>";
                            @endforeach

                        } else {
                            noticia_principal = "<div class='ModifyContents'><div onclick='showModal_content(\"" + data[0].id + "\", \"" + row_id + "\", \"" + position + "\");' class='text-success icon_mod'>" +
                                "<i class='fa fa-pencil' style='font-size: 36px;'></i>" +
                                "</div>&nbsp;&nbsp;&nbsp;&nbsp;" +
                                "<div onclick='deleteNew(\"" + data[0].id + "\", \"" + row_id + "\", \"" + position + "\");' class='text-danger icon_mod'>" +
                                "<i class='fa fa-times' style='font-size: 36px;'></i>" +
                                "</div></div>";
                            @foreach($a_trans as $key=>$value)
                                noticia_principal+= "<div class='body_{{ $key }}' @if(config("app.locale")!=$key)style='display:none' @endif>" +
                                                    "<table width='100%' "+strStyle+" aria-hidden='true'>" +
                                                    "<tbody class='noSortable'><tr><td "+strPadding+"><div class='texto' " + text_color + ">" +
                                                    data[0].text_{{ $key }} +
                                                    "</td></tr></tbody>" +
                                                    "</table></div>";
                            @endforeach
                        }

                        $("#" + row_id + "_" + position).html(noticia_principal);
                        $('#bs-modal-add-info').modal("hide");
                        $("#info_save").fadeIn(500);
                    }
                });

                return false; // Evitar ejecutar el submit del formulario.
            }

            /* IMAGENES POR CULPA DEL OUTLOOK HAY QUE AJUSTARLAS */
            function ajustarTamanyoImagenOutlook() {
                var contenedor = $(".newsletter");

                $(".header IMG, .footer IMG").each(function() {

                    if($(this).parent("p").parent("div").css("display") == 'block' || $(this).parent("div").css("display")=="block") {
                        var widthImg = $(this).width();
                        $(this).attr("width", widthImg);
                        $(this).css("width", widthImg);
                    }

                });

            }
        </script>

    @else
        <script>
            $(document).ready(function() {
                $(".select2").select2();

                $("#newsletter-builder-save").click(function(){
                   $("#formData").submit();
                });
            });
        </script>
    @endif


@stop
