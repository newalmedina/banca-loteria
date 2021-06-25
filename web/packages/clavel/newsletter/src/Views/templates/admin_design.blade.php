@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link href="{{ asset("assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("assets/admin/css/newsletter_builder/newsletter-builder-template.css") }}" rel="stylesheet" type="text/css" />
    <style>
        .columns_btn {
            border-radius: 4px;
            cursor: move;
            font-size: 14px;
            font-weight: normal;
            line-height: 1.42857;
            margin-bottom: 0;
            padding: 6px 12px;
            vertical-align: middle;
            white-space: nowrap;
            background-color: #f4f4f4;
            border: solid 1px #ddd;
            color: #444;
            margin-top: 10px;
        }

        .action_sort {
            cursor: move;
            border: dashed 1px #FFF;
            padding: 40px 20px;
            border-radius: 5px;
        }

        .action_sort:hover {
            border: dashed 1px #C0C0C0;
        }

        .modifyItem {
            margin: auto;
            background-color: #FFF;
            text-align: right;
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
            padding: 15px;
            margin-top: 10px;
            border: solid 1px #d6d4d4;
            border-left: none;
            margin-left: 600px;
            white-space: nowrap;
            position: absolute;
            z-index: 999;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.5s linear;
        }

        .removeItem {
            margin: auto;
            background-color: #FFF;
            text-align: right;
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
            padding: 15px;
            margin-top: 70px;
            border: solid 1px #d6d4d4;
            border-left: none;
            margin-left: 600px;
            white-space: nowrap;
            position: absolute;
            z-index: 999;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.5s linear;
        }

        .groupNew:hover > .removeItem, .groupNew:hover > .modifyItem {
            visibility: visible;
            opacity: 1;
        }

        #spinner {
            width: 600px;
            padding: 50px;
            text-align: center;
            display: none;
            position: absolute;
            z-index: 9999999;
        }


        .noticia_principal:hover > .ModifyContents {
            visibility: visible;
            opacity: 1;
        }

        .ModifyContents {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.5s linear;
        }

        .icon_mod {
            background-color: #e1e1e1;
            border: solid 1px #c3c3c3;
            border-radius: 50px;
            padding: 5px;
            text-align: center;
            height: 50px;
            width: 50px;
            cursor: pointer;
            float: left;
            margin-left: 5px;
            margin-right: 5px;
            -webkit-box-shadow: 3px 6px 44px 0px rgba(0,0,0,0.9);
            -moz-box-shadow: 3px 6px 44px 0px rgba(0,0,0,0.9);
            box-shadow: 3px 6px 44px 0px rgba(0,0,0,0.9);
            white-space: nowrap;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        #bs-modal-images, #bs-modal-users {
            z-index: 99999999;
        }

        i.mce-i-icon-users:before {
            content: "\f1c0";
            font-family: FontAwesome, sans-serif;
        }
    </style>
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/templates") }}">{{ trans('Newsletter::admin_lang_template.templates') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.errors')
    @include('admin.includes.success')

    @include('Newsletter::admin_partials.media')
    @include('Newsletter::templates.admin_partials.data_vars')

    <!-- MODALES -->
    <div class="modal modal-add-info fade in" id="modifyStyle">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('Newsletter::admin_lang_template.modificar_fila') }}</h4>
                </div>
                <div id="content-add-info" class="modal-body">
                    <input type="hidden" id="row_file" name="row_file" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('padding', trans('Newsletter::admin_lang_template.padding'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                                <div class="col-md-8">
                                    <select name="padding" id="padding" class="form-control">
                                        @for($nX=0; $nX<21; $nX++)
                                            <option value="{{$nX}}px">{{ $nX }}px</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br clear="all">

                    <h5 class="text-primary">{{ trans("Newsletter::admin_lang_template.Background") }}</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('inicial', trans('Newsletter::admin_lang_template.inicial'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                                <div class="col-md-8">
                                    <div class="input-group my-colorpicker2">
                                        <div class="input-group-addon">
                                            <i aria-hidden="true"></i>
                                        </div>
                                        {!! Form::text('inicial', null, array('placeholder' => trans('Newsletter::admin_lang_template.inicial'), 'class' => 'form-control color-picker', 'id' => 'inicial')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('final', trans('Newsletter::admin_lang_template.final'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                                <div class="col-md-8">
                                    <div class="input-group my-colorpicker2">
                                        <div class="input-group-addon">
                                            <i aria-hidden="true"></i>
                                        </div>
                                        {!! Form::text('final', null, array('placeholder' => trans('Newsletter::admin_lang_template.final'), 'class' => 'form-control color-picker', 'id' => 'final')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br clear="all">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('radial', trans('Newsletter::admin_lang_template.radial'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                                <div class="col-md-8">
                                    <select name="radial" id="radial" class="form-control">
                                        <option value="to top">{{ trans("Newsletter::admin_lang_template.top") }}</option>
                                        <option value="to left">{{ trans("Newsletter::admin_lang_template.left") }}</option>
                                        <option value="to right">{{ trans("Newsletter::admin_lang_template.right") }}</option>
                                        <option value="to bottom">{{ trans("Newsletter::admin_lang_template.bottom") }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br clear="all">

                    <h5 class="text-primary">{{ trans("Newsletter::admin_lang_template.Boder_inferior") }}</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('tamanyo', trans('Newsletter::admin_lang_template.tamanyo'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                                <div class="col-md-8">
                                    <select name="tamanyo" id="tamanyo" class="form-control">
                                        @for($nX=0; $nX<21; $nX++)
                                            <option value="{{$nX}}px">{{ $nX }}px</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('border_color_2', trans('Newsletter::admin_lang_template.border_color_2'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                                <div class="col-md-8">
                                    <div class="input-group my-colorpicker2">
                                        <div class="input-group-addon">
                                            <i aria-hidden="true"></i>
                                        </div>
                                        {!! Form::text('border_color_2', null, array('placeholder' => trans('Newsletter::admin_lang_template.border_color_2'), 'class' => 'form-control color-picker', 'id' => 'border_color_2')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("Newsletter::admin_lang.cerrar") }}</button>
                    <button type="button" class="btn btn-primary" onclick="save_field();">{{ trans("Newsletter::admin_lang.guardar") }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="modal modal-add-info fade in" id="modalContenidos">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('Newsletter::admin_lang_template.contenidos') }}</h4>
                </div>
                <div id="content-add-info" class="modal-body">
                    <input type="hidden" id="row_file_content" name="row_file_content" value="">
                    <input type="hidden" id="col_file_content" name="col_file_content" value="">

                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <?php
                            $nX = 1;
                            ?>
                            @foreach ($idiomas as $key => $valor)
                                <li @if($nX==1) class="active" @endif>

                                    <a href="#tab_lang_{{ $valor->code }}" data-toggle="tab" data-value="text">
                                        {{ $valor->name }}
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
                            @foreach ($idiomas as $key => $valor)
                                <div id="tab_lang_{{ $valor->code }}" class="tab-pane @if($nX==1) active @endif">
                                    <div class="form-group">
                                        {!! Form::textarea('userlang['.$valor->code.'][body]', null, array('class' => 'form-control textarea', 'id' => 'body_'.$valor->code)) !!}
                                    </div>
                                </div>
                                <?php
                                $nX++;
                                ?>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("Newsletter::admin_lang.cerrar") }}</button>
                    <button type="button" class="btn btn-primary" onclick="save_contenido();">{{ trans("Newsletter::admin_lang.guardar") }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- fin modales -->

    <div class="row">

        <div class="col-md-8">

            <div class="box box-primary">
                <div class="box-header  with-border">
                    <h3 class="box-title">{{ trans("Newsletter::admin_lang_template.vista_previa") }}</h3>
                </div>
                <div class="box-body" style="padding: 0;">
                    <div id="plantilla" style="background: {{ $template->background_content }}; padding: 20px;">
                        <div class="newsletter">
                            <table class="structure" style="background: {{ $template->background_page }}; @if($template->border && !$template->border_shadow) border:solid 1px {{ $template->border_color }} @elseif($template->border) box-shadow: 0px 0px 1px {{ $template->border_color }}; @endif " aria-hidden="true">
                                <thead>
                                <tr>
                                    <td id="header" class="header sortable_content">
                                        <div id="spinner" class="overlay"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>

                                        <div class="not_info text-danger" style="padding: 30px; padding-top: 60px; height:150px; @if($template->header!='') display: none; @endif">
                                            <i class="fa fa-ban" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang.sin_contenido") }}
                                        </div>

                                        {!! $template->header !!}
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="body" class="content_newsletter">
                                            <table cellspacing="0" cellpadding="0" border="0" style=" width: 100%;" class="news_block" aria-hidden="true">
                                                <tbody>
                                                <tr>
                                                    <td class="noticia_principal" style="position : relative; width: 100%" valign="top">
                                                        <table width='100%' class="resaltar"
                                                               style="
                                                                        background-color: {{ $template->resaltar_background_color }};
                                                                        @if($template->resaltar_border && !$template->resaltar_sombra)
                                                                            border:solid 1px {{ $template->resaltar_border_color }}
                                                                        @elseif($template->resaltar_border)
                                                                            box-shadow: 0px 0px 17px 1px {{ $template->resaltar_border_color }};
                                                                        @endif
                                                                       " aria-hidden="true">
                                                            <tbody class='noSortable'>
                                                            <tr>
                                                                <td style='text-align: left;'>
                                                                    <div class='titulo' style="color: {{ $template->title_font_color }};">Este será el contenedor las cajas resaltadas</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="texto" style="color: {{ $template->font_color }};">
                                                                        <p>Maecenas eget odio mi. Proin eget nisl vel nunc consectetur sagittis. Nullam sodales elit vitae nulla faucibus, vel hendrerit nisi pulvinar. Phasellus eget convallis ante. Integer pretium tempor tortor, id mollis leo molestie a. Praesent sit amet malesuada lectus. Phasellus non vulputate magna.</p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        <br ><br>
                                                        <table width='100%' aria-hidden="true">
                                                            <tbody class='noSortable'>
                                                            <tr>
                                                                <td style='text-align: left;'>
                                                                    <div class='titulo' style="color: {{ $template->title_font_color }};">Este será el contenedor de la newsletter para noticias y textos</div>
                                                                </td>
                                                            </tr>
                                                            <tr><td class='paddingTxtH2'></td></tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="texto" style="color: {{ $template->font_color }};">
                                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi consectetur tincidunt nibh, sit amet sagittis nisi ornare vel. Vestibulum placerat sapien orci, ut maximus nibh porttitor sit amet. Pellentesque sodales nulla ut purus dapibus, eget lacinia sapien maximus. Nullam sagittis, tortor sit amet porttitor elementum, diam tortor vestibulum quam, at commodo elit turpis eu purus. Duis dapibus id libero eu placerat. Proin eget ipsum sodales, lobortis ante ut, bibendum quam. Morbi in ornare odio, ut varius justo. Donec eros diam, dapibus nec quam in, ornare tincidunt ex. Morbi lacinia ex vel mattis commodo. Phasellus justo diam, efficitur at mi a, vehicula tempus nunc. Donec turpis leo, convallis vitae velit nec, pharetra varius enim.</p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td id="footer" class="footer sortable_content">
                                            <div class="not_info text-danger" style="padding: 30px; padding-top: 60px; height:150px; @if($template->footer!='') display: none; @endif">
                                                <i class="fa fa-ban" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang.sin_contenido") }}
                                            </div>

                                            {!! $template->footer !!}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a href="javascript:exit_preview();" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button type="button" id="newsletter-builder-save" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
                </div>
            </div>

        </div>

        <div class="col-md-4">

            {!! Form::model($template, $form_data, array('role' => 'form')) !!}
                {!! Form::hidden('id', null, array('id' => 'id')) !!}
                {!! Form::hidden('header', null, array('id' => 'header_template')) !!}
                {!! Form::hidden('footer', null, array('id' => 'footer_template')) !!}

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("Newsletter::admin_lang_template.funcinalidades") }}</h3></div>
                    <div class="box-body">
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
                    <div class="box-header with-border"><h3 class="box-title">{{ trans("Newsletter::admin_lang_template.colores") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('background_content', trans('Newsletter::admin_lang_template.background_content'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-6">
                                <div class="input-group my-colorpicker2">
                                    <div class="input-group-addon">
                                        <i aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('background_content', null, array('placeholder' => trans('Newsletter::admin_lang_template.background_content'), 'class' => 'form-control color-picker', 'id' => 'background_content')) !!}
                                </div>
                            </div>
                            <a class="btn btn-primary btn-flat col-md-1" href="javascript:deshacerColor('background_content', '#f1f1f1', $('#plantilla'), 'background-color');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                        </div>

                        <div class="form-group">
                            {!! Form::label('background_page', trans('Newsletter::admin_lang_template.background_page'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-6">
                                <div class="input-group my-colorpicker2">
                                    <div class="input-group-addon">
                                        <i aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('background_page', null, array('placeholder' => trans('Newsletter::admin_lang_template.background_page'), 'class' => 'form-control color-picker', 'id' => 'background_page')) !!}
                                </div>
                            </div>
                            <a class="btn btn-primary btn-flat col-md-1" href="javascript:deshacerColor('background_page', '#ffffff', $('.structure'), 'background-color');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                        </div>

                        <div class="form-group">
                            {!! Form::label('title_font_color', trans('Newsletter::admin_lang_template.title_font_color'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-6">
                                <div class="input-group my-colorpicker2">
                                    <div class="input-group-addon">
                                        <i aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('title_font_color', null, array('placeholder' => trans('Newsletter::admin_lang_template.title_font_color'), 'class' => 'form-control color-picker', 'id' => 'title_font_color')) !!}
                                </div>
                            </div>
                            <a class="btn btn-primary btn-flat col-md-1" href="javascript:deshacerColor('title_font_color', '#53545e', $('.titulo'), 'color');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                        </div>

                        <div class="form-group">
                            {!! Form::label('font_color', trans('Newsletter::admin_lang_template.font_color'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-6">
                                <div class="input-group my-colorpicker2">
                                    <div class="input-group-addon">
                                        <i aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('font_color', null, array('placeholder' => trans('Newsletter::admin_lang_template.font_color'), 'class' => 'form-control color-picker', 'id' => 'font_color')) !!}
                                </div>
                            </div>
                            <a class="btn btn-primary btn-flat col-md-1" href="javascript:deshacerColor('font_color', '#8e8e90', $('.texto'), 'color');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                        </div>

                        <div class="form-group">
                            {!! Form::label('border', trans('Newsletter::admin_lang_template.border'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-6">
                                <div class="col-md-10">
                                    <div class="radio-list">
                                        <label class="radio-inline">{!! Form::radio('border', 0, true, array('id'=>'border_0')) !!} {{ Lang::get('general/admin_lang.no') }}</label>
                                        <label class="radio-inline">{!! Form::radio('border', 1, false, array('id'=>'border_1')) !!} {{ Lang::get('general/admin_lang.yes') }} </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="info_border_page" @if(!$template->border) style="display: none;" @endif>
                            <div class="form-group">
                                {!! Form::label('border_color', trans('Newsletter::admin_lang_template.border_color'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                                <div class="col-md-6">
                                    <div class="input-group my-colorpicker2">
                                        <div class="input-group-addon">
                                            <i aria-hidden="true"></i>
                                        </div>
                                        {!! Form::text('border_color', null, array('placeholder' => trans('Newsletter::admin_lang_template.border_color'), 'class' => 'form-control color-picker', 'id' => 'border_color')) !!}
                                    </div>
                                </div>
                                <a class="btn btn-primary btn-flat col-md-1" href="javascript:deshacerBorder('#C0C0C0');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                            </div>

                            <div class="form-group">
                                {!! Form::label('border_shadow', trans('Newsletter::admin_lang_template.border_shadow'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                                <div class="col-md-6">
                                    <div class="col-md-10">
                                        <div class="radio-list">
                                            <label class="radio-inline">{!! Form::radio('border_shadow', 0, true, array('id'=>'border_shadow_0')) !!} {{ Lang::get('general/admin_lang.no') }}</label>
                                            <label class="radio-inline">{!! Form::radio('border_shadow', 1, false, array('id'=>'border_shadow_1')) !!} {{ Lang::get('general/admin_lang.yes') }} </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("Newsletter::admin_lang_template.funcinalidades_2") }}</h3></div>
                    <div class="box-body">
                        <div class="form-group">
                            {!! Form::label('resaltar_background_color', trans('Newsletter::admin_lang_template.resaltar_background_color'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-6">
                                <div class="input-group my-colorpicker2">
                                    <div class="input-group-addon">
                                        <i aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('resaltar_background_color', null, array('placeholder' => trans('Newsletter::admin_lang_template.resaltar_background_color'), 'class' => 'form-control color-picker', 'id' => 'resaltar_background_color')) !!}
                                </div>
                            </div>
                            <a class="btn btn-primary btn-flat col-md-1" href="javascript:deshacerColor('resaltar_background_color', '#ffffff', $('.resaltar'), 'background-color');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                        </div>

                        <div class="form-group">
                            {!! Form::label('resaltar_border', trans('Newsletter::admin_lang_template.resaltar_border'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-6">
                                <div class="col-md-10">
                                    <div class="radio-list">
                                        <label class="radio-inline">{!! Form::radio('resaltar_border', 0, true, array('id'=>'resaltar_border_0')) !!} {{ Lang::get('general/admin_lang.no') }}</label>
                                        <label class="radio-inline">{!! Form::radio('resaltar_border', 1, false, array('id'=>'resaltar_border_1')) !!} {{ Lang::get('general/admin_lang.yes') }} </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="info_border_box" @if(!$template->resaltar_border) style="display: none;" @endif>
                            <div class="form-group">
                                {!! Form::label('resaltar_border_color', trans('Newsletter::admin_lang_template.resaltar_border_color'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                                <div class="col-md-6">
                                    <div class="input-group my-colorpicker2">
                                        <div class="input-group-addon">
                                            <i aria-hidden="true"></i>
                                        </div>
                                        {!! Form::text('resaltar_border_color', null, array('placeholder' => trans('Newsletter::admin_lang_template.resaltar_border_color'), 'class' => 'form-control color-picker', 'id' => 'resaltar_border_color')) !!}
                                    </div>
                                </div>
                                <a class="btn btn-primary btn-flat col-md-1" href="javascript:deshacerBorderBox('#C0C0C0');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                            </div>

                            <div class="form-group">
                                {!! Form::label('resaltar_sombra', trans('Newsletter::admin_lang_template.resaltar_sombra'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                                <div class="col-md-6">
                                    <div class="col-md-10">
                                        <div class="radio-list">
                                            <label class="radio-inline">{!! Form::radio('resaltar_sombra', 0, true, array('id'=>'resaltar_sombra_0')) !!} {{ Lang::get('general/admin_lang.no') }}</label>
                                            <label class="radio-inline">{!! Form::radio('resaltar_sombra', 1, false, array('id'=>'resaltar_sombra_1')) !!} {{ Lang::get('general/admin_lang.yes') }} </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            {!! Form::close() !!}
        </div>

    </div>

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset("assets/admin/vendor/colorpicker/js/bootstrap-colorpicker.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("assets/admin/vendor/tinymce/tinymce.min.js") }}"></script>
    <script>
        var changes = false;
        var objQuery;
        var tinyMCE;

        $(document).ready(function() {
            $('.my-colorpicker2').colorpicker();

            tinymce.init({
                selector: "textarea.textarea",
                menubar: false,
                height: 300,
                convert_urls : false,
                resize:false,
                plugins: [
                    "advlist autolink lists link image charmap print preview anchor",
                    "searchreplace visualblocks code fullscreen table textcolor",
                    "insertdatetime media table contextmenu paste"
                ],
                toolbar: "insertfile undo redo | styleselect | fontsizeselect | forecolor, backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | link media image | code, user_button",
                file_browser_callback : function(field_name, url, type, win) {

                    openImageController(field_name, '0');

                },
                setup : function(ed) {

                    my_editor = ed;

                    // Add a custom button
                    ed.addButton('user_button', {
                        title: 'Datos de generados',
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

            /* Actualización de colores */
            $("#background_content").change(function(e) {
                e.preventDefault();
                changes = true;
                changeColor($("#plantilla"), $(this), 'background-color');
            });

            $("#background_page").change(function(e) {
                e.preventDefault();
                changes = true;
                changeColor($(".structure"), $(this), 'background-color');
            });

            $("#title_font_color").change(function(e) {
                e.preventDefault();
                changes = true;
                changeColor($(".titulo"), $(this), 'color');
            });

            $("#font_color").change(function(e) {
                e.preventDefault();
                changes = true;
                changeColor($(".texto"), $(this), 'color');
            });

            $("#resaltar_background_color").change(function(e) {
                e.preventDefault();
                changes = true;
                changeColor($(".resaltar"), $(this), 'background-color');
            });

            $("#border_0").click(function(e) {
                changes = true;
                bordeContenido(false);
            });

            $("#border_1").click(function(e) {
                changes = true;
                bordeContenido(true);
            });

            $("#resaltar_border_0").click(function(e) {
                changes = true;
                bordeContenidoBox(false);
            });

            $("#resaltar_border_1").click(function(e) {
                changes = true;
                bordeContenidoBox(true);
            });

            $("#border_shadow_0, #border_shadow_1").click(function(e) {
                var $_obj = $(".structure");
                changes = true;
                $_obj.css("border", "none");
                $_obj.css("box-shadow", "none");
                printBorder($_obj, $("#border_color"));
            });

            $("#resaltar_sombra_0, #resaltar_sombra_1").click(function(e) {
                var $_obj = $(".resaltar");
                changes = true;
                $_obj.css("border", "none");
                $_obj.css("box-shadow", "none");
                printBorderBox($_obj, $("#resaltar_border_color"));
            });

            $("#border_color").change(function(e) {
                e.preventDefault();
                changes = true;
                printBorder($(".structure"), $(this));
            });

            $("#resaltar_border_color").change(function(e) {
                e.preventDefault();
                changes = true;
                printBorderBox($(".resaltar"), $(this));
            });

            $("#newsletter-builder-save").click(function(e) {
                e.preventDefault();
                save_info();
            });

            // Drag And Drop columnas
            $( ".sortable_content" ).sortable({
                revert: true,
                receive: function() {
                    $(this).children(".not_info").css("display", "none");
                    updateListado();
                },
                stop: function( event, ui ) {
                    $("#spinner").css('display',"none");
                }
            });

            $( ".columns_btn" ).draggable({
                connectToSortable: ".sortable_content",
                helper: "clone",
                revert: "invalid",
                stop: function(event, ui) {
                    objQuery = $(ui.helper);
                }
            });

            $( "ul, li" ).disableSelection();
        });

        function updateListado() {
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
            objQuery.html(createGroupTable(numColumns, data));
            objQuery.children("TABLE").addClass("news_block");
            objQuery.children("TABLE").attr("data-value", data);
            objQuery.attr("id", "col_" + data);
            objQuery.prepend( '<div class="modifyItem"><a href="javascript:openFormato(\'' + data + '\');" class="text-primary"><i class="fa fa-pencil" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang_template.modificar_fila") }}</a></div>');
            objQuery.prepend( '<div class="removeItem"><a href="javascript:remove_file(\'' + data + '\');" class="text-danger"><i class="fa fa-times" aria-hidden="true"></i> {{ trans("Newsletter::admin_lang.elimiar_fila") }}</a></div>');
        }

        function createGroupTable(numColumns, row_id) {
            var strTableNews = "";
            var perwidth = 100 / numColumns;

            strTableNews+= "<table border='0' cellpadding='0' cellspacing='0' style='width: 100%;' class='tables_info' aria-hidden='true'>";
            strTableNews+= "<tr>";
            strTableNews+= tdContent(row_id, 1, perwidth);
            if(numColumns>1) {
                strTableNews+= tdContent(row_id, 2, perwidth);
            }
            if(numColumns>2) {
                strTableNews+= tdContent(row_id, 3, perwidth);
            }
            strTableNews+= "</tr>";
            strTableNews+= "</table>";

            return strTableNews;
        }

        function tdContent(row_id, posicion, perwidth) {
            var strTableNews = "";

            strTableNews+= "<td id='"+row_id+"_"+posicion+"' class='noticia_principal' style='width: " + perwidth + "%; position : relative; cursor: move;' valign='top'>";
            strTableNews+= "<div class='text-center action_sort'><a href='javascript:showModal(\""+row_id+"\", \""+posicion+"\");'>{{ trans("Newsletter::admin_lang.arrastrar_contenido_aqui") }}<br><i class='fa fa-share-square-o' style='font-size:18px; margin-top:10px;'></i></a></div>";
            strTableNews+= "</td>";

            return strTableNews;
        }

        function remove_file(fileToRemove) {

            if(confirm("{!! trans('Newsletter::admin_lang.sure_delete') !!}")) {
                var spinner = $("#spinner");
                var $_obj = $("#col_" + fileToRemove);

                spinner.css('height',$(".structure").height() + 150);
                spinner.css('display',"block");

                _parent = $_obj.parent(".sortable_content");

                $_obj.remove();
                if(_parent.children( ".groupNew" ).length==0) {
                    _parent.children(".not_info").css("display", "block");
                }
                spinner.css('display',"none");

            }

        }

        function deshacerColor(obj_id, new_color, $_html, $_css) {
            var $obj = $('#' + obj_id);
            $obj.val(new_color);
            $obj.parent(".my-colorpicker2")
                    .children(".input-group-addon")
                    .children("I")
                    .css("background-color", new_color);
            changeColor($_html, $obj, $_css);
        }

        function changeColor($_html, $_input, $_css) {
            $_html.css($_css, $_input.val());
        }

        function bordeContenido(show) {
            var $_obj = $(".structure");
            var $_content = $("#info_border_page");
            if(!show) {
                $_obj.css("border", "none");
                $_obj.css("box-shadow", "none");
                $_content.slideUp(500);
            } else {
                printBorder($_obj, $("#border_color"));
                $_content.slideDown(500);
            }
        }

        function bordeContenidoBox(show) {
            var $_obj = $(".resaltar");
            var $_content = $("#info_border_box");
            if(!show) {
                $_obj.css("border", "none");
                $_obj.css("box-shadow", "none");
                $_content.slideUp(500);
            } else {
                printBorderBox($_obj, $("#resaltar_border_color"));
                $_content.slideDown(500);
            }
        }

        function printBorder($_html, $_obj) {
            if($("#border_shadow_0").is(":checked")) $_html.css("border", "solid 1px " + $_obj.val());
            if($("#border_shadow_1").is(":checked")) $_html.css("box-shadow", "0px 0px 1px "  + $_obj.val());
        }

        function printBorderBox($_html, $_obj) {
            if($("#resaltar_sombra_0").is(":checked")) $_html.css("border", "solid 1px " + $_obj.val());
            if($("#resaltar_sombra_1").is(":checked")) $_html.css("box-shadow", " 0px 0px 17px 1px "  + $_obj.val());
        }

        function deshacerBorder(new_color) {
            var $_obj = $("#border_color");
            $_obj.val(new_color);
            $_obj.parent(".my-colorpicker2")
                    .children(".input-group-addon")
                    .children("I")
                    .css("background-color", new_color);
            printBorder($(".structure"), $_obj)
        }

        function deshacerBorderBox(new_color) {
            var $_obj = $("#resaltar_border_color");
            $_obj.val(new_color);
            $_obj.parent(".my-colorpicker2")
                    .children(".input-group-addon")
                    .children("I")
                    .css("background-color", new_color);
            printBorderBox($(".resaltar"), $_obj)
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

        function save_field() {
            var _parent = $("#col_" + $("#row_file").val()).children("TABLE").children("TBODY").children("TR").children(".noticia_principal");

            _parent.css("padding", $("#padding").val());
            _parent.css("border-bottom-width", $("#tamanyo").val());
            _parent.css("border-bottom-style", "solid");
            _parent.css("border-bottom-color", $("#border_color_2").val());
            if($("#inicial").val()!='' ||  $("#final").val()!='') {
                bcolor = ($("#inicial").val()!='') ? $("#inicial").val() : $("#final").val();
                _parent.css("background-color", bcolor);
            } else {
                _parent.css("background-color", "transparent");
            }
            if($("#inicial").val()!='' && $("#final").val()!='') {
                _parent.css("background-image", "linear-gradient("+$("#radial").val()+", "+$("#inicial").val()+", "+$("#final").val()+")");
            } else {
                _parent.css("background-image", "none");
                if($("#inicial").val()!='') _parent.css("background-color", $("#inicial").val());
                if($("#final").val()!='') _parent.css("background-color", $("#final").val());
            }


            $('#modifyStyle').modal('hide');
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

        /* ------------------------*/
        /* INSERCIÓN DE CONTENIDOS */
        /*------------------------ */
        function showModal(row_id, posicion) {
            $("#row_file_content").val(row_id);
            $("#col_file_content").val(posicion);
            var _parent = $("#" + row_id + "_" + posicion);

            for (i=0; i < tinyMCE.editors.length; i++) {
                var data = "";
                console.log(_parent.html());
                if(_parent.children("." + tinyMCE.editors[i].id).length>0) data = _parent.children("." + tinyMCE.editors[i].id).html();
                tinyMCE.editors[i].setContent(data);
            }

            $('#modalContenidos').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });
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

        function save_contenido() {
            var row_id = $("#row_file_content").val();
            var col_id = $("#col_file_content").val();
            var $_html = $("#"+row_id+"_"+col_id);
            var content = "";

            $_html.html("");

            for (i=0; i < tinyMCE.editors.length; i++) {
                var html_tiny = tinyMCE.editors[i].getContent();
                if(html_tiny!='') {
                    if(content=='') {
                        content+= "<div class='" + tinyMCE.editors[i].id + "'>" + tinyMCE.editors[i].getContent() + "</div>";
                    } else {
                        content+= "<div style='display:none;' class='" + tinyMCE.editors[i].id + "'>" + tinyMCE.editors[i].getContent() + "</div>";
                    }
                }
            }

            if(content=='') {
                content="<div class='text-center action_sort'><a href='javascript:showModal(\""+row_id+"\", \""+col_id+"\");'>{{ trans("Newsletter::admin_lang.arrastrar_contenido_aqui") }}<br><i class='fa fa-share-square-o' style='font-size:18px; margin-top:10px;'></i></a></div>";
            } else {
                content+= "<div class='ModifyContents'><div onclick='showModal(\""+row_id+"\", \""+col_id+"\");' class='text-success icon_mod'><i class='fa fa-pencil' style='font-size: 36px;'></i></div>";
            }

            $_html.html(content);

            $('#modalContenidos').modal('hide');
        }

        /* funciones del tinymce */
        function execTC(word) {
            $('#bs-modal-users').modal("hide");
            tinyMCE.execCommand('mceInsertContent',false, word);
        }

        /* FUNCIONES DE GUARDADO Y SALIDA */
        function save_info() {
            $(".not_info, #spinner").remove();
            $("#header_template").val($("#header").html());
            $("#footer_template").val($("#footer").html());

            $("#formData").submit();
        }

        function exit_preview() {
            var exit = true;

            if(changes) {
                if(!confirm("Ha realizado cambios en la plantilla, ¿esta seguro que desea salir y perder los cambios?")) exit = false;
            }

            if(exit) window.location = "{{ url('/admin/templates') }}";
        }
    </script>
@stop
