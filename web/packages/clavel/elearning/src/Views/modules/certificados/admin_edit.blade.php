@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("assets/front/css/certificados.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/assets/admin/vendor/jquery/css/jquery-ui.min.css") }}" rel="stylesheet" type="text/css" />

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
    <li><a href="{{ url("admin/certificados") }}">{{ trans('elearning::certificados/admin_lang.certificados') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.errors')
    @if (Session::get('success',"") != "")
        <div class="alert alert-success">
            <button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button>
            <strong>{{ date('d/m/Y H:i:s') }}</strong>
            {{ Session::get('success',"") }}
        </div>
    @endif

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

    <!-- Eliminar elementos  -->
    <div class="modal modal-note fade in" id="bs-modal-element">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title-element">{{ trans('elearning::certificados/admin_lang.deletelement') }}</h4>
                </div>
                <div class="modal-body">
                    <p>{{ trans('elearning::certificados/admin_lang.suredelete') }}</p>
                    <div class="pull-right">
                        {!! Form::hidden('idelement_to_del','',array("id"=>"idelement_to_del")) !!}
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.cancelar') }}</button>
                        <button type="button" onclick="javascript:deleteElement();" class="btn btn-info">{{ trans('general/admin_lang.borrar_item') }}</button>
                    </div>
                    <br clear="all">
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div class="row">
        {!! Form::model($certificado, $form_data, array('role' => 'form')) !!}
            {!! Form::hidden("paginas", null) !!}

            <div class="col-md-10">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('activo', trans('elearning::certificados/admin_lang.activo'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
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
                            {!! Form::label('nombre', trans('elearning::certificados/admin_lang.nombre'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('nombre', null, array('placeholder' => trans('elearning::certificados/admin_lang.nombre'), 'class' => 'form-control', 'nombre')) !!}
                            </div>
                        </div>

                    </div>
                </div>
                @if ($certificado->id != "")
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
                            <br clear="all">
                            <p><em class="text-warning">* {{ trans("elearning::certificados/admin_lang.pagina_fill_white") }}</em></p>
                            <br clear="all">
                            <?php
                            $nX = 1;
                            ?>
                            @foreach ($a_trans as $key => $valor)
                                <div id="tab_{{ $key }}" class="tab-pane @if($nX==1) active @endif">

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div id="accordion_{{ $key }}" class="box-group">

                                                @for($nY=1; $nY<=$certificado->paginas; $nY++)
                                                    <!-- Aqui hacemos el diseño de cada una de las páginas -->
                                                    {!! Form::hidden("keypag", (isset($certificado->paginasCertificado[$nY-1])) ? $certificado->paginasCertificado[$nY-1]->id : null,array('id'=>$key.'_'.$nY)) !!}
                                                    <div class="panel box box-primary">
                                                        <div class="box-header with-border">
                                                            <h4 class="box-title">
                                                                <a href="#meta_{{ $key }}_{{ $nY }}" data-parent="#accordion_{{ $key }}_{{ $nY }}" data-toggle="collapse" aria-expanded="false" class="collapsed">
                                                                    {{ trans('elearning::certificados/admin_lang.pagina') }} {{ $nY }} - {{ $valor["idioma"] }}
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div class="panel-collapse collapse" id="meta_{{ $key }}_{{ $nY }}" aria-expanded="false" style="height: 0px;">
                                                            <div class="box-body">
                                                                <div class="col-sm-6">
                                                                    <span>{{ trans('elearning::certificados/admin_lang.elements4') }}</span>
                                                                    <ul style="list-style: none; padding-left:15px;">
                                                                        <li style="padding:5px"><strong>{{ trans('elearning::certificados/admin_lang.tag1') }}</strong>{{ trans('elearning::certificados/admin_lang.tag1_aux') }}</li>
                                                                        <li style="padding:5px"><strong>{{ trans('elearning::certificados/admin_lang.tag2') }}</strong>{{ trans('elearning::certificados/admin_lang.tag2_aux') }}</li>
                                                                        <li style="padding:5px"><strong>{{ trans('elearning::certificados/admin_lang.tag3') }}</strong>{{ trans('elearning::certificados/admin_lang.tag3_aux') }}</li>
																		<li style="padding:5px"><strong>{{ trans('elearning::certificados/admin_lang.tag4') }}</strong>{{ trans('elearning::certificados/admin_lang.tag4_aux') }}</li>
                                                                    </ul>
                                                                </div>
                                                                <div class="form-group col-sm-6">
                                                                    <div class="col-sm-12">
                                                                        <div class="input-group">
                                                                            {!! Form::text('certlang['.$nY.']['.$key.'][plantilla]', (isset($certificado->paginasCertificado[$nY-1])) ? $certificado->paginasCertificado[$nY-1]->{'plantilla:'.$key} : null, array('placeholder' => trans('elearning::certificados/admin_lang.plantilla'), 'class' => 'form-control', 'id' => 'certlang_'.$key.'_'.$nY.'_plantilla','readonly'=>'true')) !!}
                                                                            <span class="input-group-btn">
                                                                                <button class="btn bg-olive btn-flat" onclick="javascript:openImageController('{{ 'certlang_'.$key.'_'.$nY."_plantilla" }}', '1');" type="button">{{ trans('elearning::certificados/admin_lang.selecciona_una_image') }}</button>
                                                                            </span>
                                                                            <span class="input-group-btn">
                                                                                <button class="btn bg-olive btn-flat" onclick="javascript:getBGCertificado('{{ $key }}', '{{ $nY }}');" type="button">{{ trans('elearning::certificados/admin_lang.previsualizar') }}</button>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <br clear="all" /><br clear="all" />
                                                                    <label class="col-sm-12"><strong>{{ trans('elearning::certificados/admin_lang.elements1') }}</strong> {{ trans('elearning::certificados/admin_lang.elements2') }}</label>
                                                                    <br clear="all" />
                                                                    <div class="col-sm-12 formElement" data-type="1">
                                                                        <span class="fa mediumIcon">&#xf036;</span>&nbsp;
                                                                        {{ trans('elearning::certificados/admin_lang.elements3') }}
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <!-- working -->
                                                                    <div id="formDesginer_{{ $key }}_{{ $nY }}" class="buttonsOfBlcok2" style="overflow: hidden; @if(isset($certificado->paginasCertificado[$nY-1])) background-image: url({{ $certificado->paginasCertificado[$nY-1]->{'plantilla:'.$key} }}); @endif">
                                                                        @if(isset($certificado->paginasCertificado[$nY-1]->translations->where("locale",$key)->first()->elementosPagina))

                                                                            @foreach($certificado->paginasCertificado[$nY-1]->translations->where("locale",$key)->first()->elementosPagina as $keyEl=>$valEl)
                                                                                <?php
                                                                                    $strStyle = "top:".$valEl->mtop."px; left:".$valEl->mleft."px;";
                                                                                    $strStyle .= "width:".$valEl->width."px !important; height:".$valEl->height."px !important;";
                                                                                    $strStyle .= "color: ".$valEl->fontcolor."; ";
                                                                                    $strStyle .= "font-family: ".$valEl->fontfamily."; ";
                                                                                    $strStyle .= "font-size: ".$valEl->fontsize."; ";
                                                                                ?>
                                                                                <div id="ele_<?php echo $valEl->id;?>" class="elementInfo" style="<?php echo $strStyle;?>" data-value="<?php echo $valEl->id;?>">
                                                                                    <div class="functionsScript">
                                                                                        <a href="javascript:editelement('<?php echo $valEl->id;?>');"><span class="fa fa-pencil mediumIcon "></span></a>
                                                                                        <a href="javascript:deleteelement('<?php echo $valEl->id;?>');"><span class="fa fa-times-circle mediumIcon "></span></a>
                                                                                    </div>
                                                                                    <?=$valEl->name;?>
                                                                                </div>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                @endfor

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
                @endif

                <div class="box box-solid">

                    <div class="box-footer">

                        <a href="{{ url('/admin/certificados') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                    </div>

                </div>

            </div>

            <div class="col-md-2">
                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title"><i class="fa fa-search" aria-hidden="true"></i> {{ trans("elearning::certificados/admin_lang.previsualizar") }}</h3></div>
                    <div class="box-body">
                        <p>{{ trans("elearning::certificados/admin_lang.previsualizar_info") }}</p>
                        <div class="text-center" style="padding-bottom: 10px; padding-top: 10px;">
                            <button type="button" class="btn btn-success" onclick="javascript:previewPDF({{ $certificado->id }});">{{ trans("elearning::certificados/admin_lang.ver_cert") }}</button>
                        </div>
                    </div>
                </div>
            </div>


        {!! Form::close() !!}
    </div>

@endsection

@section('foot_page')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        function previewPDF(id) {
            window.open("{{ url("admin/certificados/pdf/") }}/"+id,'_blank');
        }

        var inputChange = '';
        function openImageController(input, only_img) {
            inputChange = input;
            $('#bs-modal-images')
            .on('hidden.bs.modal', function(){
                $("#"+inputChange).val($("#selectedFile").val())
            })
            .modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#responsibe_images").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
            $("#responsibe_images").load("{{ url("admin/media/viewer-simple/") }}/" + only_img);

        }

        function getBGCertificado(lang,npag) {
            {{-- $("#"+lang+"_"+npag).val() nos devuelve el id de certificado_pagina --}}
            if($("#certlang_"+lang+"_"+npag+"_plantilla").val() != "") {
                $.ajax({
                    method: "POST",
                    url: "{{ url("admin/certificados/plantilla/") }}",
                    data: { plantilla: $("#certlang_"+lang+"_"+npag+"_plantilla").val(), certificado_pagina_id: $("#"+lang+"_"+npag).val(), locale: lang },
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"}
                }).done(function( data ) {
                    $("#formDesginer_"+lang+"_"+npag).css("background-image","url("+$("#certlang_"+lang+"_"+npag+"_plantilla").val()+")");
                    //$("#formDesginer_"+lang+"_"+npag).css("background-size","100% 100%");
                });
            } else {
                // TODO - mostrar la notificación en un modal <---
                alert("{{ trans('elearning::certificados/admin_lang.empty_plantilla') }}");
            }
        }

        function createNewElementForm(elementSel, top, left) {
            $(".modal-title").html("{{ trans('elearning::certificados/admin_lang.crear_texto') }}");
            $('#bs-modal-images').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            var element = elementSel.split("_");
            var idioma = element[1];
            var idplantilla = $("#" + element[1] + "_" + element[2]).val(); // $("{lang}_{n}").val() == certificado_pagina_id

            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#responsibe_images").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
            $("#responsibe_images").load("{{ url("admin/certificados/elements/") }}/" + idioma + "/" + idplantilla + "/" + top + "/" + left);
        }

        function deleteelement(idelement) {
            $('#bs-modal-element').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });
            $("#idelement_to_del").val(idelement);
        }

        function deleteElement() {
            idelement = $("#idelement_to_del").val();
            $.ajax({
                method: "DELETE",
                url: "{{ url("admin/certificados") }}/"+idelement+"/delelement",
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"}
            }).done(function() {
                $("#ele_"+idelement).remove();
                $('#bs-modal-element').modal('hide');
            });
        }

        function editelement(idelement) {
            $(".modal-title").html("{{ trans('elearning::certificados/admin_lang.editar_texto') }}");
            $('#bs-modal-images').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#responsibe_images").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
            $("#responsibe_images").load("{{ url("admin/certificados/editelement/") }}/" + idelement);
        }

        $(document).ready(function() {

            $(".formElement").draggable({
                helper: "clone",
                cursor: "move",
                drag: function(event, ui) {
                    ui.helper.css('z-index', "9999999");
                }
            });

            $(".buttonsOfBlcok2").droppable({
                accept: '.formElement',
                activeClass: "ui-state-highlight",
                drop: function( event, ui ) {
                    var pos     = ui.helper.offset(), dPos = $(this).offset();
                    var top     = (pos.top - dPos.top);
                    var left    = (pos.left - dPos.left);
                    createNewElementForm($(this).attr("id"), top, left);
                }
            });

            $( ".elementInfo" ).draggable({
                cursor: "move",
                stop: function(event, ui) {
                    // Obtengo la posición de drop.
                    var Stoppos = $(this).offset(), dPos = $(this).parent().offset();
                    var idelement = $(this).attr("data-value");
                    var top     = (Stoppos.top - dPos.top);
                    var left    = (Stoppos.left - dPos.left);

                    $.ajax({
                        method: "POST",
                        url: "{{ url("admin/certificados/move-element/") }}",
                        data: { mtop: top, mleft: left, id: idelement},
                        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"}
                    });
                }
            });

            $( ".elementInfo" ).resizable({
                helper: "ui-resizable-helper",
                stop: function(event, ui) {
                    // Obtengo el height y el width para su modificación;
                    var width       = $(event.target).width();
                    var height      = $(event.target).height();
                    var idelement   = $(this).attr("data-value");

                    $.ajax({
                        method: "POST",
                        url: "{{ url("admin/certificados/move-element/") }}",
                        data: { width: $(event.target).width(), height: $(event.target).height(), id: idelement},
                        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"}
                    });
                }
            });
        });
    </script>

    {!! JsValidator::formRequest('Clavel\Elearning\Requests\CertificadosRequest')->selector('#formData') !!}
@stop
