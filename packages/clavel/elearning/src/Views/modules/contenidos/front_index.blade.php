@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section("head_page")
    <style>
        .list-group-item {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@stop

@section('breadcrumb')
    <li>
        <a href="{{ url("asignaturas/detalle/".$contenido->modulo->asignatura->getTranslatedURL()."/".$contenido->modulo->asignatura->id) }}">{{ $contenido->modulo->asignatura->titulo }}</a>
    </li>
    <li>{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.modals')
    <?php $asignatura = $contenido->modulo->asignatura; ?>
    @include("front.includes.modal_curso", compact("asignatura"))
    <div class="container cont">

        <div class="row">
            <div class="scrollFollow minimize @if($contenido->pantalla_completa) col-md-12 @else col-md-3 col-md-push-9 @endif">

                @if($timeToFinishAssignatura > 0)
                    <div >
                        @if($alertLevel<2)
                            <div class="col-sm-12">
                                <div class="row alert alert-danger">
                                    <strong>{{ trans("elearning::asignaturas/front_lang.cont") }}
                                    </strong><br>
                                </div>
                            </div>
                        @else
                            <div class="col-sm-12">
                                <div class="row alert alert-{{ $alertColor }}">
                                    <strong >{{ trans('elearning::contenidos/front_lang.fecha_fin_curso',
                                            [
                                                "DIAS" => $tiempo_restante->days,
                                                "HORAS" => $tiempo_restante->h,
                                                "MINUTOS" => $tiempo_restante->i
                                            ]) }}
                                    </strong><br>
                                </div>
                            </div>
                        @endif

                    </div>
                @endif

                <div class="panel panel-default">
                    @if($contenido->pantalla_completa)
                        <div class="row">
                            <div class="col-md-3">
                                @endif

                                <div class="panel-body">
                                    <h5>{{ trans("elearning::modulos/front_lang.progreso") }}</h5>
                                    <?php
                                    $totales = $contenido->modulo->contenidos()->activos()->where("tipo_contenido_id", "<>", 1)->count();
                                    $petc = number_format((count($completados) * 100) / ((empty($totales)) ? 1 : $totales));
                                    ?>
                                    <div class="circular-bar circular-bar-sm">
                                        <div class="circular-bar-chart" data-percent="{{ $petc }}"
                                             data-plugin-options="{'size': 85, 'lineWidth': 3, 'barColor': '#0088CC'}">
                                            <label class="text-primary font-weight-bold">{{ $petc }}%</label>
                                        </div>
                                    </div>

                                    @if($contenido->pantalla_completa)
                                        <div class="row" style="margin:0">
                                            <div class="col-xs-2" style="padding:0">
                                                @if(!is_null($anterior))
                                                    <a href="{{ url('contenido/detalle-contenido/'.$anterior->getTranslatedURL().'/'.$anterior->id) }}"
                                                       class=" btn btn-primary"><<</a>
                                                @endif
                                            </div>
                                            <div class="col-xs-8" style="text-align: center; padding:0">
                                                <button type="button" id="goback"
                                                        class="btn btn-primary">{{ trans("elearning::modulos/front_lang.volver") }}</button>
                                            </div>
                                            <div class="col-xs-2" style="padding:0">

                                                @if(!is_null($siguiente))
                                                    <a href="{{ url('contenido/detalle-contenido/'.$siguiente->getTranslatedURL().'/'.$siguiente->id) }}"
                                                       class="pull-right btn btn-primary">>></a>
                                                @endif

                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($contenido->pantalla_completa)
                            </div>
                            <div class="col-md-9">
                                <div class="panel-body" style="padding-bottom: 0">
                                    <h5>{{ trans("elearning::modulos/front_lang.listado_contenidos") }}</h5>
                                    @endif

                                    <div class="list-group module_navcard"
                                         style="padding: 0px; @if($contenido->pantalla_completa) height: 150px; overflow: hidden; overflow-y: auto; border: solid 1px #ddd; @endif">
                                        @foreach($contenido->modulo->contenidos()->activos()->orderBy('lft')->get() as $contenido_info)
                                            <?php
                                            $output = "";
                                            for ($i = 0; $i < $contenido_info->depth; $i++) {
                                                $output .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                            }
                                            ?>
                                            <p class="list-group-item {{ $contenido_info->id==$contenido->id ? "activo" : "" }}">
                                                @if($contenido_info->tipo->slug!='tema')
                                                    <span class="badge"
                                                          style="background-color: transparent; color:#777">
                                        @if($contenido_info->id==$contenido->id)
                                                            <i class="fa fa-search pull-right text-primary" aria-hidden="true"
                                                               style="font-size: 18px;"></i>
                                                        @else
                                                            @if(isset($completados[$contenido_info->id]))
                                                                <i class="fa fa-check-circle-o pull-right text-success" aria-hidden="true"
                                                                   style="font-size: 18px;"></i>
                                                            @else
                                                                <i class="fa fa-circle pull-right text-default" aria-hidden="true"
                                                                   style="font-size: 18px;"></i>
                                                            @endif
                                                        @endif
                                    </span>
                                                @endif
                                                {!! $output !!}
                                                @if($contenido_info->tipo->slug!='tema')<a
                                                        href="{{ url('contenido/detalle-contenido/'.$contenido_info->getTranslatedURL().'/'.$contenido_info->id) }}">@endif
                                                    {{ $contenido_info->getTranslatedNombre() }}
                                                    @if($contenido_info->tipo->slug!='tema')</a>@endif
                                                @if($contenido_info->obligatorio)
                                                    <span class="text-info">(*)</span>
                                                @endif
                                            </p>
                                        @endforeach
                                    </div>


                                    @if($contenido->pantalla_completa)
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-2">
                                    @if(!is_null($anterior))
                                        <a href="{{ url('contenido/detalle-contenido/'.$anterior->getTranslatedURL().'/'.$anterior->id) }}"
                                           class=" btn btn-primary"><<</a>
                                    @endif
                                </div>
                                <div class="col-xs-8" style="text-align: center;">
                                    <button type="button" id="goback"
                                            class="btn btn-primary">{{ trans("elearning::modulos/front_lang.volver") }}</button>
                                </div>
                                <div class="col-xs-2">

                                    @if(!is_null($siguiente))
                                        <a href="{{ url('contenido/detalle-contenido/'.$siguiente->getTranslatedURL().'/'.$siguiente->id) }}"
                                           class="pull-right btn btn-primary">>></a>
                                    @endif

                                </div>
                            </div>

                        </div>
                    @endif

                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-xs-2"><a href="#" data-toggle="modal" data-target="#more_info"
                                                     class="btn btn-info"><i
                                             class="fa fa-info" aria-hidden="true"></i> {{ trans("elearning::contenidos/front_lang.plus_info") }}</a>
                            </div>
                            <div class="col-xs-8"></div>
                            <div class="col-xs-2">
                                @if($contenido->descargar_pdf)
                                    <a href="{{ url("contenido/detalle-contenido/openPDF/".$contenido->id) }}"
                                       class="btn btn-danger pull-right "><i
                                                class="fa fa-file-pdf-o" aria-hidden="true"></i> {{ trans("elearning::contenidos/front_lang.pdf") }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="@if($contenido->pantalla_completa) col-md-12 @else col-md-9 col-md-pull-3 @endif">
                @if($contenido->pantalla_completa)<br clear="all">@endif
                @include("elearning::contenidos.front_partials.".$contenido->tipo->vista_front,compact('contenido'))

                <div class="row">
                    <div class="col-xs-6">
                        @if(!is_null($anterior))
                            <a href="{{ url('contenido/detalle-contenido/'.$anterior->getTranslatedURL().'/'.$anterior->id) }}"
                               class=" btn btn-primary"><< {{ trans("elearning::contenidos/front_lang.Anterior") }}</a>
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if(!is_null($siguiente))
                            <a href="{{ url('contenido/detalle-contenido/'.$siguiente->getTranslatedURL().'/'.$siguiente->id) }}"
                               class="pull-right btn btn-primary">{{ trans("elearning::contenidos/front_lang.Siguiente") }} >></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
    <br clear="all">
    <br clear="all">
    <br clear="all">
@endsection


@section('foot_page')
    <script>
        $(document).ready(function () {
            $("#goback").click(function (e) {
                e.preventDefault();
                @if($contenido->modulo->contenidos()->count()>1)
                    window.location = "{{ url("modulos/detalle_modulo/".$contenido->modulo->getTranslatedURL()."/".$contenido->modulo->id) }}";
                @else
                    window.location = "{{ url("asignaturas/detalle/".$contenido->modulo->asignatura->getTranslatedURL()."/".$contenido->modulo->asignatura->id) }}";
                @endif
            });

            /* Set inner scroll position */
            $(".list-group.module_navcard .list-group-item.activo")[0].offsetParent.scrollTop = $(".list-group.module_navcard .list-group-item.activo")[0].offsetTop;

            /*
            ****************************************************
            * Scrolling and hidding module info initialization *
            ****************************************************
            **/
            function resizeAdapt() {
                floatingDiv.children(".panel.panel-default").css("width", "unset");
                let panelWidth = floatingDiv.children(".panel.panel-default").width();
                floatingDiv.children(".panel.panel-default").css("width", panelWidth);
            }

            /* Definimos los elementos clave y la anchura adecuada */
            var floatingDiv = $(".scrollFollow"); // div to scroll/hide
            calculateModuleScrolling(floatingDiv);
            resizeAdapt();
            let resizeId;
            $(window).resize(function () {
                clearTimeout(resizeId);
                resizeId = setTimeout(resizeAdapt, 500);
            });
            /* * * * * * */

            /* Minimize */
            if ($(".minimize.col-md-3 .panel-body h5")) {
                var closeBtn = $(document.createElement("button"));
                closeBtn.append("<i class='fa fa-bars'></i>");
                closeBtn.addClass("minOpenBtn");
                $(".minimize.col-md-3 .panel-body h5").append(closeBtn);
                /* Minimize requires scroll to properly work */
                $(window).scroll(function (event) {
                    calculateModuleScrolling(floatingDiv);
                });
                // On open, execute toggle
                $(".minimize.col-md-3 .minOpenBtn").on("click", function () {
                    // Set containers to use
                    var minBox = $(".minimize.col-md-3");
                    var mainContent = minBox.next("div");
                    var openBtn = $(document.createElement("button"));
                    openBtn.append("<i class='fa fa-bars'></i>");
                    openBtn.addClass("minBtn");

                    openBtn.on("click", function () {
                        toggleModuleInfoVisibility(minBox, mainContent, openBtn);
                        calculateModuleScrolling(floatingDiv);
                        return true;
                    });
                    toggleModuleInfoVisibility(minBox, mainContent, openBtn);
                });
            }
            /* END - Scrolling and hidding module inf */

        });

        function goTo(url) {
            document.location = url;
        }


        /*
            *************************************
            * Scrolling and hidding module info *
            *************************************
            **/
        // Just move the info based on current container's position in relation to user's screen.
        function calculateModuleScrolling(floatingDiv) {
            if (floatingDiv[0].getBoundingClientRect().top < 73) {
                floatingDiv.children(".panel.panel-default").addClass("fixedScroll");
            } else {
                floatingDiv.children(".panel.panel-default").removeClass("fixedScroll");
            }
        }

        // Toggle visivility, mobile optimized.
        function toggleModuleInfoVisibility(minBox, mainContent, openBtn) {
            var del_baseClassBtn = "col-md-3 col-md-push-9";
            var add_baseClassBtn = "col-md-12 min hidden";
            var del_baseMainContent = "col-md-9 col-md-pull-3";
            var add_baseMainContent = "col-md-12";
            var floatingDiv = $(".scrollFollow.col-md-3");
            var mainContainerSTop = $(".main");
            if (!minBox.hasClass("min")) {
                // Do the math & finish
                minBox.removeClass(del_baseClassBtn);
                minBox.addClass(add_baseClassBtn);
                mainContent.removeClass(del_baseMainContent);
                mainContent.addClass(add_baseMainContent);
                $(document.body).append(openBtn);
            } else {
                // Just restore initial state with JS animation
                // Do the math & finish
                mainContent.removeClass(add_baseMainContent);
                mainContent.addClass(del_baseMainContent);
                minBox.removeClass(add_baseClassBtn);
                minBox.addClass(del_baseClassBtn);
                $(".minBtn").remove();
            }
        }
    </script>

    {{-- Cargamos los javascript de los diferentes partials. Esto es importante ya que no podemos invocar a librerías js
           que no esten cargadas previamente. Si ponemos js dentro del blade no podremos usar jQuery por ejemplo
     --}}
    @if(view()->exists("elearning::contenidos.front_partials.".$contenido->tipo->vista_front."_js"))
        @include("elearning::contenidos.front_partials.".$contenido->tipo->vista_front."_js")
    @endif
@endsection
