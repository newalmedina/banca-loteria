@extends('admin.layouts.popup')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- ColorPicker -->
    <link href="{{ asset("/assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}" rel="stylesheet" type="text/css"/>
@stop


@section('content')
    @if(!empty($plantilla))
        @include('admin.includes.modals')
        {!! Form::model($element, ['route' => array('admin.certificados.createelement'), 'method' => 'POST', 'id' => 'formElement']) !!}
        {!! Form::hidden('id', $element->id, array('id' => 'id')) !!}
        {!! Form::hidden('certificado_pagina_translation_id', $plantilla->id, array('id' => 'certificado_pagina_translation_id')) !!}
        {!! Form::hidden('mtop', $element->mtop, array('id' => 'top')) !!}
        {!! Form::hidden('mleft', $element->mleft, array('id' => 'left')) !!}
        {!! Form::hidden('width', $element->width, array('id' => 'width')) !!}
        {!! Form::hidden('height', $element->height, array('id' => 'height')) !!}

        <div class="row">
            <div class="col-xs-12 col-md-6">
                    <div class="col-xs-6">
                        <select id="fontfamily" name="fontfamily" onchange="changePreview();">
                            <option value="">{{ trans('elearning::certificados/admin_lang.font-family') }}</option>
                            @foreach ($fontFamily as $key => $valor)
                                <option value="{{$valor}}" @if($element->fontfamily == $valor) selected @endif>{{$valor}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <select id="fontsize" name="fontsize" onchange="changePreview();">
                            <option value="">{{ trans('elearning::certificados/admin_lang.font-size') }}</option>
                            @foreach ($fontSize as $key => $valor)
                                <option value="{{$valor}}" @if($element->fontsize == $valor) selected @endif>{{$valor}}</option>
                            @endforeach
                        </select>
                    </div>
                    <br clear="all"><br clear="all">
                    {!! Form::label('fontcolor', trans('elearning::certificados/admin_lang.font-color'), array('class' => 'col-xs-5 control-label', 'readonly' => true)) !!}
                    <div class="col-xs-7">
                        <div class="colorSelector" id="fontcolor" style="float:left">
                            <div></div>
                        </div>
                        <input type="hidden" id="fontcolor_input" name="fontcolor" value="{{ $element->fontcolor }}"/>
                    </div>
                    <br clear="all"><br clear="all">
                    {!! Form::label('name', trans('elearning::certificados/admin_lang.texto'), array('class' => 'col-xs-3 control-label', 'readonly' => true)) !!}
                    <div class="col-xs-9">
                        <input type="text" name="name" id="name_label" style="width:100%;" value="{{ $element->name }}"
                               onkeyup="changePreview();" />
                    </div>

            </div>

            <div id="preview" class="previewInfo col-xs-12 col-md-6"></div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
            </div>
        </div>
        <br clear="all">
        {!! Form::close() !!}
    @else
        <div class="row">
            <div class="col-xs-12 center">
                <div class="alert alert-lg alert-light">
                    <h4 class="text-center">
                        {{ trans('elearning::certificados/admin_lang.empty_plantilla') }}
                    </h4>
                </div>
            </div>
        </div>
    @endif
@endsection

@section("foot_page")
    @if(!empty($plantilla))
        <script type="text/javascript" src="{{ asset('/assets/admin/vendor/colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
        <script>
            function changePreview() {
                var strStyle = "";

                if ($("#fontsize").val() != '') strStyle += "font-size: " + $("#fontsize").val() + " !important;";
                if ($("#fontfamily").val() != '') strStyle += "font-family: " + $("#fontfamily").val() + " !important;";
                if ($("#fontcolor_input").val() != '') strStyle += "color: " + $("#fontcolor_input").val() + " !important;";

                textIntercented = '<p style="' + strStyle + '">' + $("#name_label").val() + '</p>';

                $("#preview").html('<div class="prev">' + textIntercented + '</div>');

            }

            function saveElement(lang, npag) {
                if ($("#certlang_" + lang + "_" + npag + "_plantilla").val() != "") {
                    $.ajax({
                        method: "POST",
                        url: "{{ url("admin/certificados/plantilla/") }}",
                        data: {
                            plantilla: $("#certlang_" + lang + "_" + npag + "_plantilla").val(),
                            certificado_pagina_id: $("#" + lang + "_" + npag).val(),
                            locale: lang
                        },
                        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    }).done(function (data) {
                        $("#formDesginer_" + lang + "_" + npag).css("background-image", "url(" + $("#certlang_" + lang + "_" + npag + "_plantilla").val() + ")");
                        $("#formDesginer_" + lang + "_" + npag).css("background-size", "100% 100%");
                    });
                }
            }

            $(document).ready(function () {
                $('#fontcolor')
                    .colorpicker({color : "{{ $element->fontcolor }}"})
                    .on('changeColor', function (ev) {
                    $(this).find('div').css("background-color", ev.color.toHex());
                    $("#fontcolor_input").val(ev.color.toHex());
                    changePreview();
                });

                $('#fontcolor').css('background-color', '{{ $element->fontcolor }}');

                $('#formElement').on('submit', function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: $(this).attr("action"),
                        data: $(this).serialize(),
                        success: function (msg) {
                            $('#bs-modal-images').modal('hide');
                            if($('#id').val() !== '') {
                                var elemento = '#ele_'+$('#id').val();
                                $(elemento).remove();
                            }
                            $("#formDesginer_{{ $plantilla->locale }}_{{ $page_number }}").append(msg);

                            $(".elementInfo").draggable({
                                cursor: "move",
                                stop: function (event, ui) {
                                    // Obtengo la posición de drop.
                                    var Stoppos = $(this).offset(), dPos = $(this).parent().offset();
                                    var idelement = $(this).attr("data-value");
                                    var top = (Stoppos.top - dPos.top);
                                    var left = (Stoppos.left - dPos.left);

                                    $.ajax({
                                        method: "POST",
                                        url: "{{ url("admin/certificados/move-element/") }}",
                                        data: {mtop: top, mleft: left, id: idelement},
                                        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"}
                                    });
                                }
                            });

                            $(".elementInfo").resizable({
                                helper: "ui-resizable-helper",
                                stop: function (event, ui) {
                                    // Obtengo el height y el width para su modificación;
                                    var width = $(event.target).width();
                                    var height = $(event.target).height();
                                    var idelement = $(this).attr("data-value");

                                    $.ajax({
                                        method: "POST",
                                        url: "{{ url("admin/certificados/move-element/") }}",
                                        data: {
                                            width: $(event.target).width(),
                                            height: $(event.target).height(),
                                            id: idelement
                                        },
                                        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"}
                                    });
                                }
                            });
                        }
                    });
                });
            })
        </script>
    @endif
@stop
