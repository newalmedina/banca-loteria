@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <style>
        /** SPINNER CREATION **/
        .loader {
            position: relative;
            text-align: center;
            margin: 15px auto 35px auto;
            z-index: 9999;
            display: block;
            width: 80px;
            height: 80px;
            border: 10px solid rgba(0, 0, 0, .3);
            border-radius: 50%;
            border-top-color: #000;
            animation: spin 1s ease-in-out infinite;
            -webkit-animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @-webkit-keyframes spin {
            to {
                -webkit-transform: rotate(360deg);
            }
        }

        .loader-txt p {
            font-size: 13px;
            color: #666;
        }

        .loader-txt p small {
            font-size: 11.5px;
            color: #999;
        }

    </style>

@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/translator") }}">{{ trans("translator-manager::translator/admin_lang.traducciones") }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.errors')
    @include('admin.includes.success')
    @include('admin.includes.modals')

    <!-- Modal para la Modificación de textos -->
    <div id="modalCambioTexto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCambioTexto"
         data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div id="content_block" class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                        <span id="btnEnviarTextoCerrarCheck" aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        {{ trans('translator-manager::translator/admin_lang.texto_idioma') }}&nbsp;-&nbsp;
                        <span id="container_cambio_texto_titulo"></span>
                    </h4>
                </div>
                <div id="container_cambio_texto_contenido" class="modal-body">
                    <form>
                        <input type="hidden" id="locale_cambio_texto" name="locale_cambio_texto" value="">
                        <input type="hidden" id="key_cambio_texto" name="key_cambio_texto" value="">
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">{{ trans('translator-manager::translator/admin_lang.texto') }}:</label>
                            <textarea class="form-control" id="texto_cambio_texto"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnEnviarTextoCerrar" class="btn btn-default pull-left"
                            onclick="javascript:doClose();">
                        {{ trans("general/front_lang.cerrar") }}
                    </button>
                    <button type="button" id="btnEnviarTexto" class="btn btn-primary" onclick="javascript:doSave();">{{ trans('general/admin_lang.save') }}</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Fin Modal para la Modificación de estado de proyectos -->

    <!-- Modal Enviando-->
    <div class="modal fade" id="sendingAddKeysModal" tabindex="-1" role="dialog" aria-labelledby="sendingAddKeysModal">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="loader"></div>
                    <div clas="loader-txt">
                        <p>{!! trans("translator-manager::translator/admin_lang.enviando_datos") !!}<br><br><small>{!! trans("translator-manager::translator/admin_lang.paciencia") !!}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">
                            {{ trans('translator-manager::translator/admin_lang.add_key') }}
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <textarea class="form-control" rows="3" id="keys" name="keys"
                                      placeholder="{{ trans('translator-manager::translator/admin_lang.add_key_placeholder') }}"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="button" id="btnEnviarAddKeys"  class="btn btn-primary"
                                    onclick="javascript:addKey();"
                                    data-placement="right" data-toggle="popover">
                                <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;
                                {{ trans('translator-manager::translator/admin_lang.add_keys') }}
                            </button>

                        </div>
                    </div>
                    <!-- /.box -->
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">
                            {{ trans('translator-manager::translator/admin_lang.auto_traduccion') }}
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        {!! Form::open(array('url' => 'admin/translator/group/auto_translate', 'method' => 'POST', 'id' => 'frmTranslate', 'class' => "form-add-locale autotranslate-block-group")) !!}

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="base-locale">{{ trans('translator-manager::translator/admin_lang.base_auto_traduccion') }}</label>
                                        <select name="base-locale" id="base-locale" class="form-control">
                                            <option value=""></option>
                                            @foreach($locales as $locale)
                                            <option value="{{ $locale }}">{{ $locale }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="new-locale">{{ trans('translator-manager::translator/admin_lang.clave_auto_traduccion') }}</label>
                                        <input type="text" name="new-locale" class="form-control" id="new-locale" placeholder="{{ trans('translator-manager::translator/admin_lang.clave_auto_traduccion') }}" />
                                    </div>
                                    <div class="form-group">
                                        <label for="forced">{{ trans('translator-manager::translator/admin_lang.forzar_auto_traduccion') }}</label>
                                        &nbsp;&nbsp;{!! Form::checkbox('forced', null, false, array( 'id' => 'forced', 'class' => 'minimal')) !!}
                                    </div>


                                    <div class="form-group">
                                        <input type="hidden" name="with-translations" value="1">
                                        <input type="hidden" name="file" value="{{ $group }}">
                                        <button type="button" id="btnAutoTranslate" style="margin-right: 15px;" class="btn btn-default btn-block" onclick="javascript:doAutoTranslate();">
                                            {{ trans('translator-manager::translator/admin_lang.usar_auto_traduccion') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                    <!-- /.box -->
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">
                            {{ trans('translator-manager::translator/admin_lang.total').' '.$numTranslations }} -
                            {{ trans('translator-manager::translator/admin_lang.cambios').' '.$numChanged }}
                        </h3>
                    </div>
                    <div class="box-body pull-right">
                        <button type="button" id="btnBuscarTextos" style="margin-right: 15px;" class="btn btn-danger" onclick="javascript:doPublish();">
                            {{ trans('translator-manager::translator/admin_lang.publicar_grupo') }}
                        </button>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <table id="table_languages" class="table table-bordered table-striped" aria-hidden="true">
                            <thead>
                            @php
                                $with = intval( (100 - 20) / sizeof($locales)) ;
                            @endphp
                            <tr>
                                <th scope="col" style="width: 15%">{{ trans('translator-manager::translator/admin_lang.key') }}</th>
                                @foreach ($locales as $locale)
                                    <th scope="col" width="{{ $with."%" }}">
                                        <strong>{{ $locale }}</strong>
                                        <button type="button" class="btn bg-purple btn-sm pull-right"
                                                onclick="javascript:doPublishLocale('{{ $locale }}');"
                                                data-content="{{ trans('translator-manager::translator/admin_lang.publicar_grupo') }}"
                                                data-placement="right" data-toggle="popover">
                                            <i class="fa fa-cog" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                @endforeach
                                @if ($deleteEnabled)
                                    <th scope="col" style="width: 5%">{{ trans('translator-manager::translator/admin_lang.actions') }}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($translations as $key => $translation)
                                <tr id="{!! htmlentities($key, ENT_QUOTES, 'UTF-8', false) !!}">
                                    <td>{!! htmlentities($key, ENT_QUOTES, 'UTF-8', false) !!}</td>
                                    @foreach ($locales as $locale)
                                        @php
                                            $t = isset($translation[$locale]) ? $translation[$locale] : null
                                        @endphp
                                        <td>
                                            <a href="javascript:openCambioTexto('{{ $locale }}', '{{ htmlentities($key, ENT_QUOTES, 'UTF-8', false) }}')">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                                <div style="display: inline;" id="{{ $locale }}-{{ htmlentities($key, ENT_QUOTES, 'UTF-8', false) }}">{!!  $t ? htmlentities($t->value, ENT_QUOTES, 'UTF-8', false) : '' !!}</div>

                                            </a>
                                        </td>
                                    @endforeach
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm"
                                                onclick="javascript:deleteKey('{{ $locale }}', '{{ htmlentities($key, ENT_QUOTES, 'UTF-8', false) }}');"
                                                data-content="{{ trans('translator-manager::translator/admin_lang.delete') }}"
                                                data-placement="right" data-toggle="popover">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->

@endsection

@section("foot_page")
    <script src="{{ asset("/assets/admin/vendor/jquery-validation/jquery.validate.js") }}" type="text/javascript"></script>
    <script type="text/javascript">

        function openCambioTexto(locale, key) {
            var btnEnviar = $("#btnEnviarTexto");
            btnEnviar.removeClass('disabled');
            btnEnviar.find('span').remove()

            var btnClose = $("#btnEnviarTextoCerrar");
            btnClose.removeClass('disabled');

            var btnCerrarCheck = $("#btnEnviarTextoCerrarCheck");
            btnCerrarCheck.show();

            $("#modalCambioTexto").modal("toggle");
            $("#container_cambio_texto_titulo").text(locale);
            $("#locale_cambio_texto").val(locale);
            $("#key_cambio_texto").val(key);
            $("#texto_cambio_texto").val($("#"+locale+"-"+key).text());
        }

        const locales = [
                    @foreach ($locales as $locale)
                        {{ $loop->first ? '' : ', ' }}'{{ $locale }}'
                    @endforeach
            ];
        function addKey() {
            var btnEnviarAddKeys = $("#btnEnviarAddKeys");
            if (btnEnviarAddKeys.hasClass('disabled')) {
                return false;
            }

            btnEnviarAddKeys.addClass('disabled');
            btnEnviarAddKeys.prepend('<span><i class="fa fa-spinner fa-spin" aria-hidden="true"></i>&nbsp;</span>');

            $("#sendingAddKeysModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });

            $.ajax({
                url: "{{url('/admin/translator/group')}}",
                type: "POST",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                data: {
                    group: '{{ $group }}',
                    keys: $("#keys").val()
                },
                success       : function ( data ) {

                    $("#keys").val('');
                    var btnEnviarAddKeys = $("#btnEnviarAddKeys");
                    btnEnviarAddKeys.removeClass('disabled');
                    btnEnviarAddKeys.find('span').remove()

                    $("#sendingAddKeysModal").modal("toggle");
                    if(data) {
                        if(data.success) {
                            var keys = data.keys;
                            for (var key in keys) {
                                const value = keys[key]['key'];

                                $('#table_languages tr:last').after('<tr id="'+value+'"></tr>');

                                $('tr#'+value)
                                    .append($('<td>').text(value));
                                for(var locale in locales) {
                                    $('tr#'+value)
                                        .append($('<td>').html(
                                            '<a href="javascript:openCambioTexto(\''+locales[locale]+'\', \''+value+'\')"><i class="fa fa-pencil" aria-hidden="true"></i><div style="display: inline;" id="'+locales[locale]+'-'+value+'"></div></a>'
                                        ));
                                }
                            }
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;



                }
            });

        }

        function doSave() {
            var btnEnviar = $("#btnEnviarTexto");
            if (btnEnviar.hasClass('disabled')) {
                return false;
            }

            btnEnviar.addClass('disabled');
            btnEnviar.prepend('<span><i class="fa fa-spinner fa-spin" aria-hidden="true"></i>&nbsp;</span>');

            var locale = $("#locale_cambio_texto").val();
            var key = $("#key_cambio_texto").val();
            $("#"+locale+"-"+key).text($("#texto_cambio_texto").val());


            var btnCerrar = $("#btnEnviarTextoCerrar");
            btnCerrar.addClass('disabled');

            var btnCerrarCheck = $("#btnEnviarTextoCerrarCheck");
            btnCerrarCheck.hide();

            $.ajax({
                url: "{{url('/admin/translator/group/update')}}",
                type: "POST",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                data: {
                    _method: 'patch',
                    group: '{{ $group }}',
                    locale: locale,
                    key: key,
                    content: $("#texto_cambio_texto").val()
                },
                success       : function ( data ) {

                    $("#modalCambioTexto").modal("toggle");
                }
            });


        }

        function doClose() {
            var btnClose = $("#btnEnviarTextoCerrar");
            if (btnClose.hasClass('disabled')) {
                return false;
            }
            $("#modalCambioTexto").modal("toggle");
        }

        function deleteKey(locale, key) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('translator-manager::translator/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('translator-manager::translator/admin_lang.delete_question') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo(\''+locale+'\',\''+key+'\');">{{ trans('general/admin_lang.borrar_item') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function deleteinfo(locale, key) {
            $("#sendingAddKeysModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $('#modal_confirm').modal('hide');

            $.ajax({
                url: "{{url('/admin/translator/group/delete')}}",
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                data: {
                    _method: 'delete',
                    group: '{{ $group }}',
                    locale: locale,
                    key: key
                },
                success : function(data) {
                    $("#sendingAddKeysModal").modal("toggle");

                    if(data) {
                        if(data.success) {
                            $("#modal_alert").addClass('modal-success');
                            $("#alertModalHeader").html('{{ trans('translator-manager::translator/admin_lang.registro_borrado') }}');
                            $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                            $('table#table_languages tr#'+key).remove();
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }
            });
            return false;
        }

        function doPublish() {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('translator-manager::translator/admin_lang.seguro_publicar') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:goPublish();">{{ trans('translator-manager::translator/admin_lang.publicar') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function goPublish() {
            $("#sendingImportModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $('#modal_confirm').modal('hide');

            $.ajax({
                url: "{{url('admin/translator/group/publish')}}",
                type: "POST",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                data: {
                    group: '{{ $group }}'
                },
                success       : function ( data ) {
                    $('#sendingImportModal').modal('hide');
                    if(data) {
                        if(data.status) {
                            $("#modal_alert").addClass('modal-success');
                            $("#alertModalHeader").html('{{ trans('translator-manager::translator/admin_lang.import_ok') }}');
                            $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                            window.location.reload();
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('translator-manager::translator/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;

                }
            });
            return false;
        }

        function doPublishLocale(locale) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('translator-manager::translator/admin_lang.seguro_publicar_uno') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:goPublishLocale(\''+locale+'\');">{{ trans('translator-manager::translator/admin_lang.publicar') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function goPublishLocale(locale) {

            $("#sendingImportModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $('#modal_confirm').modal('hide');

            $.ajax({
                url: "{{url('admin/translator/group/publish_locale')}}",
                type: "POST",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                data: {
                    group: '{{ $group }}',
                    locale: locale,
                },
                success       : function ( data ) {
                    $('#sendingImportModal').modal('hide');
                    if(data) {
                        if(data.status) {
                            $("#modal_alert").addClass('modal-success');
                            $("#alertModalHeader").html('{{ trans('translator-manager::translator/admin_lang.import_ok') }}');
                            $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                            window.location.reload();
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('translator-manager::translator/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;

                }
            });
            return false;
        }


        function doAutoTranslate() {
            var form = $("#frmTranslate");

            form.validate({
                rules: {
                    'new-locale': {
                        required: true
                    },
                    'base-locale': {
                        required: true
                    }
                },
                messages: {
                    'new-locale': {
                        required: "{{ trans('translator-manager::translator/admin_lang.campo_obligatorio') }}"
                    },
                    'base-locale': {
                        required: "{{ trans('translator-manager::translator/admin_lang.campo_obligatorio') }}"
                    }
                },
                highlight: function (element) {
                    $(element).closest('.form-group').addClass('has-error');
                },
                unhighlight: function (element) {
                    $(element).closest('.form-group').removeClass('has-error');
                },
                errorElement: 'span',
                errorClass: 'help-block',


                errorPlacement: function (error, element) {
                    if (element.prop('type') === 'radio') {
                        error.insertAfter(element.closest('.input-group'));
                        // else just place the validation message immediatly after the input
                    } else if (element.prop('type') === 'checkbox') {
                        error.insertAfter(element.closest('.input-group'));
                        // else just place the validation message immediatly after the input
                    } else if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                        // else just place the validation message immediatly after the input
                    } else {
                        error.insertAfter(element);
                    }
                }


            });

            if (form.valid()) {
                var strBtn = "";

                $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
                $("#confirmModalBody").html("{{ trans('translator-manager::translator/admin_lang.seguro_auto') }}");
                strBtn += '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
                strBtn += '<button type="button" class="btn btn-primary" onclick="javascript:goAutoTranslate();">{{ trans('translator-manager::translator/admin_lang.auto_traduccion') }}</button>';
                $("#confirmModalFooter").html(strBtn);
                $('#modal_confirm').modal('toggle');
            }
        }

        function goAutoTranslate() {
            $("#sendingImportModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $('#modal_confirm').modal('hide');

            $.ajax({
                url: "{{url('admin/translator/group/auto_translate')}}",
                type: "POST",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                data: {
                    'file': '{{ $group }}',
                    'base-locale': $("#base-locale").val(),
                    'new-locale': $("#new-locale").val(),
                    'with-translations': '1',
                    'forced': $('#forced').is(":checked")
                },
                success       : function ( data ) {
                    $('#sendingImportModal').modal('hide');
                    if(data) {
                        if(data.status) {
                            $("#modal_alert").addClass('modal-success');
                            $("#alertModalHeader").html('{{ trans('translator-manager::translator/admin_lang.import_ok') }}');
                            $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                            window.location.reload();
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('translator-manager::translator/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;

                }
            });
            return false;
        }

    </script>
@stop
