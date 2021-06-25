@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />


    <style>
        .requestwizard-modal{
            background: rgba(255, 255, 255, 0.8);
            box-shadow: rgba(0, 0, 0, 0.3) 20px 20px 20px;
        }
        .requestwizard-step p {
            margin-top: 10px;
        }

        .requestwizard-row {
            display: table-row;
        }

        .requestwizard {
            display: table;
            width: 100%;
            position: relative;
        }

        .requestwizard-step button[disabled] {
            opacity: 1 !important;
            filter: alpha(opacity=100) !important;
        }

        .requestwizard-row:before {
            top: 14px;
            bottom: 0;
            position: absolute;
            content: " ";
            width: 100%;
            height: 1px;
            background-color: #ccc;
            z-index: 0;

        }

        .requestwizard-step {
            display: table-cell;
            text-align: center;
            position: relative;
        }

        .btn-circle {
            width: 30px;
            height: 30px;
            text-align: center;
            padding: 6px 0 6px 0;
            font-size: 12px;
            line-height: 1.428571429;
            border-radius: 15px;
        }
    </style>
@stop

@section('breadcrumb')

    <li><a href="{{ url("admin/asignaturas/") }}">{{ trans('elearning::asignaturas/admin_lang.asignaturas') }}</a></li>
    <li><a href="{{ url("admin/asignaturas/".$contenido->modulo->asignatura->id."/modulos/") }}">{{ trans('elearning::modulos/admin_lang.modulos_listado')." ".$contenido->modulo->asignatura->{"titulo:es"} }}</a></li>
    <li><a href="{{ url("admin/modulos/".$contenido->modulo_id."/contenidos/") }}">{{ trans('elearning::contenidos/admin_lang.contenidos')." ".$contenido->modulo->{"nombre:es"} }}</a></li>
    <li class="active">{{ trans('elearning::preguntas/admin_lang.wizard_bread') }}</li>
@stop

@section('content')

    <div class="modal fade" id="modal_wizard" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-1"><i class="fa fa-magic text-primary fa-2x" aria-hidden="true"></i></div>
                        <div class="col-md-11"><h4 class="text-primary">{{ trans("elearning::preguntas/admin_lang.generador_wizard") }}</h4></div>
                    </div>
                    <div class="clearfix"></div>
                    <br clear="all">

                    <div class="requestwizard">
                        <div class="requestwizard-row setup-panel">
                            <div class="requestwizard-step">
                                <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                                <p>{{ trans("elearning::preguntas/admin_lang.wizard_questions") }}</p>
                            </div>
                            <div class="requestwizard-step">
                                <a href="#step-2" type="button" class="btn btn-default btn-circle disabled">2</a>
                                <p>{{ trans("elearning::preguntas/admin_lang.wizard_responses") }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <br clear="all">
                    <form role="form">
                        <div class="row setup-content" id="step-1">
                            <div class="col-xs-12">
                                <div class="col-md-12">
                                    {{ trans("elearning::preguntas/admin_lang.generador_wizard_info") }}
                                    <br clear="all">
                                    <br clear="all">
                                    <div class="form-group">
                                        <label class="control-label">{{ trans("elearning::preguntas/admin_lang.wizard_tipo_pregunta") }}:</label>
                                        <select id="tipo_pregunta" name="tipo_pregunta" class="form-control">
                                            @foreach($tipos as $tipo)
                                                <option value="{{$tipo->id}}">{{$tipo->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">

                                            <?php
                                            $nX = 1;
                                            ?>
                                            @foreach ($a_trans as $key => $valor)
                                                <li @if($nX==1) class="active" @endif>
                                                    <a href="#tab_w1_{{ $key }}" data-toggle="tab">
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
                                            <?php
                                            $nX = 1;
                                            ?>
                                            @foreach ($a_trans as $key => $valor)
                                                @if($nX==1)<input type="hidden" name="default_locale" value="{{ $key }}" id="default_locale">@endif
                                                <div id="tab_w1_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                                                    <div class="form-group">
                                                        <label class="control-label">{{ trans("elearning::preguntas/admin_lang.wizard_listado_preguntas") }}</label>
                                                        <textarea id="preguntas_{{ $key }}" name="preguntas_{{ $key }}" class="form-control" style="resize: none; height:200px;" data-rel="{{ $key }}"></textarea>
                                                    </div>
                                                </div>
                                                <?php
                                                $nX++;
                                                ?>
                                            @endforeach
                                        </div>
                                    </div>

                                    <button class="btn btn-primary nextBtn pull-right" type="button" >{{ trans("elearning::preguntas/admin_lang.wizard_siguiente") }} <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row setup-content" id="step-2">
                            <div class="col-xs-12">
                                <div class="col-md-12">

                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">

                                            <?php
                                            $nX = 1;
                                            ?>
                                            @foreach ($a_trans as $key => $valor)
                                                <li @if($nX==1) class="active" @endif>
                                                    <a href="#tab_w2_{{ $key }}" data-toggle="tab">
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
                                            <?php
                                            $nX = 1;
                                            ?>
                                            @foreach ($a_trans as $key => $valor)
                                                <div id="tab_w2_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                                                    <div id="respuestas_{{ $key }}"></div>
                                                </div>
                                                <?php
                                                $nX++;
                                                ?>
                                            @endforeach
                                        </div>
                                    </div>


                                    <button class="btn btn-primary nextPrev " type="button" ><i class="fa fa-arrow-left" aria-hidden="true"></i> {{ trans("elearning::preguntas/admin_lang.wizard_anterior") }}</button>
                                    <button class="btn btn-success nextGenerar pull-right" type="button" >{{ trans("elearning::preguntas/admin_lang.generar") }} <i class="fa fa-cogs" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('elearning::preguntas/admin_lang.wizard_close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">


            <div id="saveInfo" class="callout callout-warning">
                <h4>{{ trans('elearning::preguntas/admin_lang.wizard_atencion') }}</h4>
                <p>{{ trans('elearning::preguntas/admin_lang.wizard_atencion_info') }}</p>
            </div>

            <div class="box ">

                <div class="box-header"><h3 class="box-title">{{ trans('elearning::preguntas/admin_lang.wizard')}}</h3></div>

                <div class="box-body">
                    {!! Form::model(null, $form_data, array('role' => 'form')) !!}
                        {!! Form::hidden('contenido_id', $contenido->id, array('id' => 'contenido_id')) !!}
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
                                <?php
                                $nX = 1;
                                ?>
                                @foreach ($a_trans as $key => $valor)
                                    <div id="tab_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                                        <?php //@if($contenido->preguntas()->count()==0)?>
                                            <p class="not_questions text-warning text-center">
                                                {{ trans("elearning::preguntas/admin_lang.not_questions") }}<br><br>
                                                <a href="javascript:create_questions();">
                                                    <i class="fa fa-question fa-2x" aria-hidden="true"></i><br>
                                                    {{ trans('elearning::preguntas/admin_lang.insertar_preguntas') }}
                                                </a>
                                            </p>
                                            <?php //@endif?>

                                        <div id="preview_{{ $key }}" class="preview"></div>
                                    </div>
                                    <?php
                                    $nX++;
                                    ?>
                                @endforeach
                            </div>
                        </div>

                        <div class="box-footer">
                            <a href="{{ url("admin/modulos/".$contenido->modulo_id."/contenidos/") }}" class="btn btn-default">{{ trans('elearning::modulos/admin_lang.volver') }}</a>
                            <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
                        </div>
                    {!! Form::close() !!}
                </div>


            </div>
        </div>
    </div>

@endsection

@section("foot_page")

    <script>
        var navListItems = $('div.setup-panel div a');
        var allWells = $('.setup-content');
        var allNextBtn = $('.nextBtn');
        var allPrevBtn = $('.nextPrev');
        var allGenerarBtn = $('.nextGenerar');
        var preguntas = "";

        $(document).ready(function () {
            navListItems.click(function (e) {
                e.preventDefault();
                var $target = $($(this).attr('href'));
                var $item = $(this);

                if (!$item.hasClass('disabled')) {
                    navListItems.removeClass('btn-primary').addClass('btn-default');
                    $item.addClass('btn-primary');
                    allWells.hide();
                    $target.show();
                    $target.find('input:eq(0)').focus();
                }
            });

            allNextBtn.click(function(){
                var curStep = $(this).closest(".setup-content");
                var curStepBtn = curStep.attr("id");
                var nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a");
                var curInputs = $("#preguntas_" + $("#default_locale").val());

                if (curInputs.val()!='') {
                    crearPreguntas(curStep);
                    nextStepWizard.removeClass('disabled').trigger('click');
                } else {
                    alert("{{ trans("elearning::preguntas/admin_lang.obligatorios_campos") }}");
                }
            });

            allPrevBtn.click(function(){
                var curStep = $(this).closest(".setup-content");
                var curStepBtn = curStep.attr("id");
                var nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

                nextStepWizard.trigger('click');
            });

            allGenerarBtn.click(function(){
                var curStep = $(this).closest(".setup-content");
                var curInputs = curStep.find("INPUT");
                var curAreas = curStep.find(".textarea_" + $("#default_locale").val());
                var isValid = true;
                var aRapid = [];

                curAreas.each(function() {
                    if($(this).val()=='') isValid=false;
                });

                if(isValid) {
                    $(".not_questions").remove();
                    $("#saveInfo").slideDown(500);
                    aRapid = generarArray(curInputs);
                    printArray(aRapid);
                    $("#modal_wizard").modal("hide");
                } else {
                    alert("{{ trans("elearning::preguntas/admin_lang.obligatorios_campos_respuestas") }}");
                }

            });
        });

        function setupSteps() {
            // Ocultamos todas las tabs de entrada
            allWells.hide();
            // Ponemos todos los casos como disabled
            navListItems.addClass("disabled");
            // Limpiamos los textareas
            $("textarea").val("");
            // Realizamos el trigger de click en la primera
            $('div.setup-panel div a[href="#step-1"]').removeClass('disabled');
            $('div.setup-panel div a[href="#step-1"]').trigger('click');
        }

        function create_questions() {
            var enter = true;
            if($("#preview_" + $("#default_locale").val()).html() != '') {
                if(!confirm("{!! trans("elearning::preguntas/admin_lang.perder_info") !!}")) enter=false;
            }
            if(enter) {
                setupSteps();
                $("#modal_wizard").modal("toggle");
            }
        }

        function crearPreguntas(curStep) {
            curStep.find("textarea").each(function() {
                var locale = $(this).attr("data-rel");
                var arrayOfLines;
                var preguntas = '<div class="box-group" id="accordion_'+locale+'">';

                if($(this).val()!='') {
                    arrayOfLines = $(this).val().split('\n');
                } else {
                    arrayOfLines = $("#preguntas_" + $("#default_locale").val()).val().split('\n');
                }

                preguntas+= '</div>';

                arrayOfLines.forEach(function(item, index) {
                    preguntas+=defineAccordionItem(item, index, locale);
                });

                $("#respuestas_" + $(this).attr("data-rel")).html(preguntas);
            });

        }

        function defineAccordionItem(item, index, locale) {
            var accordion = '';

            accordion+= '<div class="panel box box-primary"><div class="box-header with-border"><h4 class="box-title"><a data-toggle="collapse" data-parent="#accordion_'+locale+'" href="#collapse'+locale+index+'">';
            accordion+= item;
            accordion+= '</a></h4></div><div id="collapse'+locale+index+'" class="panel-collapse collapse"><div class="box-body">';
            accordion+= '<div class="form-group">';
            accordion+= '<input type="hidden" name="question_'+locale+'_'+index+'" id="question_'+locale+'_'+index+'" value="'+item+'" data-rel="repuestas_'+locale+'_'+index+'" data-index="'+ index +'" data-lang="'+locale+'">';
            accordion+= '<label class="control-label">{{ trans("elearning::preguntas/admin_lang.wizard_listado_respuestas") }}</label>';
            accordion+= '<textarea id="repuestas_'+locale+'_'+index+'" name="respuestas_'+locale+'_'+index+'" class="form-control textarea_'+locale+'" style="resize: none; height:100px;" data-rel="'+locale+'"></textarea>';
            accordion+= '</div>';
            accordion+= '</div></div></div>';

            return accordion;
        }

        function generarArray(curInputs) {
            var aRapid = [];
            var contador = $(".drag").length;

            curInputs.each(function() {
                var $obj = {
                    index: $(this).attr("data-index"),
                    lang: $(this).attr("data-lang"),
                    type: $("#tipo_pregunta").val(),
                    question: $(this).val(),
                    responses: $("#" + $(this).attr("data-rel")).val().split('\n')
                };
                aRapid.push($obj);
            });

            return aRapid;
        }

        function printArray(aRapid) {
            $(".preview").html("");
            aRapid.forEach(function(item, index) {
                var $curDiv = $("#preview_" + item.lang);
                var strPrinter = $curDiv.html();
                strPrinter += createBlock(item);
                $curDiv.html(strPrinter);
            });
        }

        function createBlock(block) {
            var strReturn = "";

            strReturn += "<div class='drag' id='block_"+block.index+"'>";
            strReturn += '<label for="userlang['+block.lang+']['+block.index+'][pregunta]" class="control-label">{{ trans("elearning::preguntas/admin_lang.wizard_pregunta") }}:</label>';
            strReturn += '<input type="text" name="userlang['+block.lang+']['+block.index+'][pregunta]" value="' + block.question + '" class="form-control">';
            strReturn += '<label for="userlang['+block.lang+']['+block.index+'][tipo]" class="control-label">{{ trans("elearning::preguntas/admin_lang.wizard_tipo_pregunta_list") }}:</label>';
            strReturn += '<select name="userlang['+block.lang+']['+block.index+'][tipo]" class="form-control">';
            @foreach($tipos as $tipo)
                var checked = ("{{ $tipo->id }}" == block.type) ? "selected" : "";
                strReturn += '<option value="{{$tipo->id}}" '+checked+'>{{$tipo->nombre}}</option>';
            @endforeach
            strReturn += '</select>';

            strReturn += '<table border="0" style=" width: 100%" aria-hidden="true">';
            strReturn += "<tr>";
            strReturn += "<td width='10' style='padding: 5px;'><strong>{{ trans("elearning::preguntas/admin_lang.wizard_correcta") }}</strong></td>";
            strReturn += '<td  style="padding: 5px;"><strong>{{ trans("elearning::preguntas/admin_lang.wizard_respuestas") }}</strong></td>';
            strReturn += '<td width="140" style="padding: 5px;"><strong>{{ trans("elearning::preguntas/admin_lang.puntos_is_acierta") }}</strong></td>';
            strReturn += "</tr>";
            block.responses.forEach(function(response, rindex) {
                strReturn += "<tr>";
                strReturn += "<td width='10' style='padding: 5px;'><input type='checkbox' value='1' name='userlang["+block.lang+"]["+block.index+"][responses]["+rindex+"][correcta]'></td>";
                strReturn += '<td style="padding: 5px;"><input type="text" name="userlang['+block.lang+']['+block.index+'][responses]['+rindex+'][respuesta]" value="' + response + '" class="form-control"></td>';
                strReturn += '<td width="150" style="padding: 5px;"><input type="text" name="userlang['+block.lang+']['+block.index+'][responses]['+rindex+'][puntos]" value="" class="form-control"></td>';
                strReturn += "</tr>";
            });
            strReturn += '</table>';
            strReturn += "<hr>";
            strReturn += "</div>";

            return strReturn;
        }
    </script>

@stop
