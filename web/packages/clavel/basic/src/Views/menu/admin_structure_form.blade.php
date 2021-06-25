<style>
    .select2-container--default .select2-selection--multiple {
        height: auto !important;
    }
</style>

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

{!! Form::model($item, $form_data, array('role' => 'form')) !!}
{!! Form::hidden('id', null, array('id' => 'id')) !!}
{!! Form::hidden('menu_id', $menu_id, array('id' => 'menu_id')) !!}
<div class="box box-primary">

    <div class="box-header with-border"><h3 class="box-title">{{ trans("basic::menu/admin_lang.info_menu") }}@if($item->title!='') - {!! $item->{'title:'.config("app.default_locale")} !!} @endif</h3></div>

    <div class="box-body">

        <div class="form-group">
            {!! Form::label('item_type_id', trans('basic::menu/admin_lang.tipo_contenido'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <select id="item_type_id" name="item_type_id" class="form-control">
                    @foreach($idtypes as $type)
                        <option value="{{ $type->id }}" data-rel="{{ $type->slug }}" @if(isset($item->item_type_id) && $item->item_type_id==$type->id) selected @endif>{{ $type->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="selector_pagina" class="form-group selector" style=" display: none;">
            {!! Form::label('page_id', trans('basic::menu/admin_lang.pagina'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-8">
                <select id="page_id" name="page_id" class="form-control">
                    <option value="">{{ trans("basic::menu/admin_lang.nothing") }}</option>
                    @foreach($pages as $page)
                        <option value="{{ $page->id }}"  @if($page->id==$item->page_id) selected @endif>{{ $page->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <a class="btn bg-purple btn-sm pull-right" href="javascript:showPreview();" data-content="{{ trans('general/admin_lang.ver') }}" data-placement="right" data-toggle="popover"><i class="fa fa-search" aria-hidden="true"></i> Vista previa</a>
            </div>
        </div>

        <div id="selector_modulo" class="form-group selector" style=" display: none;">
            {!! Form::label('module_name', trans('basic::menu/admin_lang.module'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <select id="module_name" name="module_name" class="form-control">
                    <option value="">{{ trans("basic::menu/admin_lang.nothing") }}</option>
                    @foreach(config("modules.enable") as $modulo)
                        <option value="{{ $modulo["route"] }}" @if($item->module_name==$modulo["route"]) selected @endif>{{ $modulo["name"] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="selector_system" class="form-group selector" style=" display: none;">
            {!! Form::label('system_name', trans('basic::menu/admin_lang.fixed_menu'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <select id="system_name" name="system_name" class="form-control">
                    <option value="">{{ trans("basic::menu/admin_lang.nothing") }}</option>

                        <option value="language" @if($item->module_name=='language') selected @endif>Idiomas</option>
                        <option value="profile_name" @if($item->module_name=='profile_name') selected @endif>Nombre usuario</option>
                        <option value="divider" @if($item->module_name=='divider') selected @endif>División</option>
                        <option value="logout" @if($item->module_name=='logout') selected @endif>Cerrar sesión</option>

                </select>
            </div>
        </div>

        <div id="selector_interno" class="form-group selector" style=" display: none;">
            {!! Form::label('uri', trans('basic::menu/admin_lang.uri'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                {!! Form::text('uri', null, array('placeholder' => trans('basic::menu/admin_lang.uri_insertar'), 'class' => 'form-control', 'id' => 'uri')) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('target', trans('basic::menu/admin_lang.target'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <select id="target" name="target" class="form-control">
                    <option value="" @if(isset($item->target) && $item->target==null) selected @endif>{{ trans('basic::menu/admin_lang.same') }}</option>
                    <option value="_blank" @if(isset($item->target) && $item->target=='_blank') selected @endif>{{ trans('basic::menu/admin_lang.newpage') }}</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('status', trans('basic::menu/admin_lang.status'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('status', 0, true, array('id'=>'status_0')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('status', 1, false, array('id'=>'status_1')) !!}
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
                    @if($nX==1)- <span class="text-success">{{ trans('basic::menu/admin_lang._defecto') }}</span>@endif
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
                {!!  Form::hidden('userlang['.$key.'][menu_item_id]', $item->id, array('id' => 'menu_item_id')) !!}

                <div class="form-group">
                    {!! Form::label('userlang['.$key.'][title]', trans('basic::menu/admin_lang.title'), array('class' => 'col-sm-2 control-label')) !!}
                    <div class="col-sm-10">
                        {!! Form::text('userlang['.$key.'][title]', $item->{'title:'.$key} , array('placeholder' => trans('basic::menu/admin_lang._INSERTAR_title'), 'class' => 'form-control textarea', 'id' => 'title_'.$key)) !!}
                    </div>
                </div>

                <div id="selector" class="form-group selector_externo" style=" display: none;">
                    {!! Form::label('userlang['.$key.'][url]', trans('basic::menu/admin_lang.url'), array('class' => 'col-sm-2 control-label')) !!}
                    <div class="col-sm-10">
                        {!! Form::text('userlang['.$key.'][url]', $item->{'url:'.$key}, array('placeholder' => trans('basic::menu/admin_lang._INSERTAR_url'), 'class' => 'form-control textarea', 'id' => 'url_'.$key)) !!}
                    </div>
                </div>

                <div id="selector" class="form-group ">
                    {!! Form::label('userlang['.$key.'][generate_url]', trans('basic::menu/admin_lang.url_generated'), array('class' => 'col-sm-2 control-label')) !!}
                    <div class="col-sm-10">
                        <div class="input-group">
                            <span id="strUrl" class="input-group-addon">{{ url('') }}/</span>
                            {!! Form::text('userlang['.$key.'][generate_url]', $item->{'generate_url:'.$key}, array('readonly' => 'readonly', 'class' => 'form-control textarea', 'id' => 'generate_url_'.$key)) !!}
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

<div class="box box-primary">
    <div class="box-header  with-border"><h3 class="box-title"><i class="fa  fa-key" aria-hidden="true"></i> {{ trans("basic::menu/admin_lang.permisos") }}</h3></div>
    <div class="box-body">
        <div class="form-group">
            <div class="col-md-12">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('permission', 0, true, array('id'=>'permission_0')) !!}
                        {{ Lang::get('basic::menu/admin_lang.always_visible') }}</label><br>
                    <label class="radio-inline">
                        {!! Form::radio('permission', 2, true, array('id'=>'permission_2')) !!}
                        {{ Lang::get('basic::menu/admin_lang.not_authentified') }}</label><br>
                    <label class="radio-inline">
                        {!! Form::radio('permission', 1, false, array('id'=>'permission_1')) !!}
                        {{ Lang::get('basic::menu/admin_lang.only_authentified') }} </label>
                </div>

                <div id="roles" style="@if($item->permission!='1') display: none; @endif">
                    <div id="sel_roles" class="selector-roles" style="margin-left: 20px;">
                        <br clear="all">
                        <select class="form-control select2" id="sel_roles_list" name="sel_roles[]" multiple="multiple" data-placeholder="{{ trans('basic::menu/admin_lang.seleccion_roles') }}" style="width: 100%;">
                            @foreach($roles as $value)
                                <option value="{{ $value->id }}" @if($value->menusSelected($item->id)) selected @endif>{{ $value->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="box box-solid">

    <div class="box-footer">

        <a href="javascript:formclose();" class="btn btn-default">{{ trans('general/admin_lang.close') }}</a>
        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

    </div>

</div>

{!! Form::close() !!}

<script>
    var viewerSelected = $( "#item_type_id option:selected" ).attr("data-rel");
    $(document).ready(function() {
        if(viewerSelected!='externo') {
            $("#selector_" + viewerSelected).css("display","block");
        } else {
            $(".selector_externo").css("display","block");
            $("#strUrl").css("text-decoration","line-through");
        }


        $( "#item_type_id").change(function() {
            changeOption();
        });

        $("#blocks_top_0, #blocks_right_0, #blocks_left_0, #blocks_bottom_0").click(function() {
            var divHider = $(this).attr('data-select');

            $("#" + divHider).slideUp(500);
        });

        $("#blocks_top_1, #blocks_right_1, #blocks_left_1, #blocks_bottom_1").click(function() {
            var divHider = $(this).attr('data-select');

            $("#" + divHider).slideDown(500);
        });

        $("#permission_0, #permission_2").click(function() {
            $("#roles").slideUp(500);
            $('#sel_roles_list').val('').trigger('change')
        });

        $("#permission_1").click(function() {
            $("#roles").slideDown(500);
        });

        $(".select2").select2();
    });

    function changeOption() {
        var viewer = $( "#item_type_id option:selected" ).attr("data-rel");



        if(viewer == 'externo') {
            $(".selector_externo").fadeIn(500);
            $("#selector_" + viewerSelected).fadeOut(200);
            viewerSelected = viewer;
            $("#strUrl").css("text-decoration","line-through");
        } else {
            $("#strUrl").css("text-decoration","none");
            if(viewerSelected=='externo') {
                $(".selector_externo").fadeOut(500);
                $("#selector_" + viewer).fadeIn(200);
                viewerSelected = viewer;
            } else {
                $("#selector_" + viewerSelected).fadeOut(200, function() {
                    viewerSelected = viewer;
                    $("#selector_" + viewer).fadeIn(500);
                });
            }

        }

    }

    function showPreview() {
        var page_id = $("#page_id").val();

        if(page_id!='') {
            $("#content-preview").html('<div id="spinner2" class="overlay" style="text-align: center"><i class="fa fa-refresh fa-spin" style="font-size: 64px;" aria-hidden="true"></i></div>');
            $('#bs-modal-preview').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });
            $("#content-preview").load("{{ url("admin/pages/preview/") }}/" + page_id);
        }

    }


</script>
