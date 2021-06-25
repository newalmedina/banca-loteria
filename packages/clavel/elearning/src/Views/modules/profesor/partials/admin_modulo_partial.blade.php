<?php $idContenidosVistos = $tracksChildren->pluck("contenido_id")->toArray() ?>
<div>
    <br/>
    <div class="px-sm">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h6 class="box-title">{{ trans("elearning::profesor/admin_lang.datos_modulo") }}</h6>
            </div>
            <div class="box-body d-flex flex-wrap">
                <ul class="nav nav-stacked min-w-33 px-sm">
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.puntua") }}</strong> <span class="pull-right">{{ $trackScope->modulo->puntua ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.peso") }}</strong> <span class="pull-right">{{ $trackScope->modulo->peso }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.fecha_inicio") }}</strong> <span class="pull-right">{{ $trackScope->fecha_inicio_formatted }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.fecha_fin") }}</strong> <span class="pull-right">{{ $trackScope->fecha_fin_formatted }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.completado") }}</strong> <span class="pull-right label label-{{ $trackScope->completado ? "success" : "warning" }}">{{ $trackScope->completado ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.aprobado") }}</strong> <span class="pull-right label label-{{ $trackScope->aprobado ? "success" : "danger" }}">{{ $trackScope->aprobado ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.nota") }}</strong> <span class="pull-right">{{ $trackScope->nota }}</span></li>
                </ul>
                <div class="d-flex justify-content-space-around flex-grow-1">
                    <div class="px-sm">
                        <h4 class="text-center">{{ trans("elearning::profesor/admin_lang.total_contenidos") }}: <strong>{{ $infoAdicional["totales"] }}</strong></h4>
                        <div class="chart-responsive">
                            <canvas id="contenidos_chart_{{ $trackScope->modulo_id }}_{{ $trackScope->convocatoria_id }}"></canvas>
                        </div>
                    </div>
                    <div class="px-sm">
                        <h4 class="text-center">{{ trans("elearning::profesor/admin_lang.obligatorios") }}: <strong>{{ $infoAdicional["obligatorios"] }}</strong></h4>
                        <div class="chart-responsive">
                            <canvas id="obligatorios_chart_{{ $trackScope->modulo_id }}_{{ $trackScope->convocatoria_id }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h6 class="box-title">{{ trans("elearning::profesor/admin_lang.lista_contenidos") }}</h6>
            </div>
            <div class="box-body max-h-overflow-y">
                @if($totales->count() > 0)
                    <table class="table table-striped" aria-hidden="true">
                        <tbody>
                        <tr>
                            <th scope="col">{{ trans("elearning::profesor/admin_lang.contenido") }}</th>
                            <th scope="col">{{ trans("elearning::profesor/admin_lang.obligatorio") }}</th>
                            <th scope="col">{{ trans("elearning::profesor/admin_lang.tipo") }}</th>
                            <th scope="col">{{ trans("elearning::profesor/admin_lang.visto") }}</th>
                        </tr>
                        @foreach($totales as $contenido)
                            <?php $contenidoVisto = (in_array($contenido->id, $idContenidosVistos)); ?>
                            <tr>
                                <td>{{ $contenido->nombre }}</td>
                                <td><span class="label label-{{ $contenido->obligatorio ? "success" : "info" }}">{{ $contenido->obligatorio ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></td>
                                <td>{{ $contenido->tipo->nombre }}</td>
                                <td><span class="label label-{{ $contenidoVisto ? "success" : "warning" }}">{{ $contenidoVisto ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning">
                        {{ trans("elearning::profesor/admin_lang.no_contenidos") }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <h4 class="px-sm">{{ trans("elearning::profesor/admin_lang.info_contenidos") }}</h4>
    <div class="d-flex justify-content-center flex-wrap">
        @forelse($tracksChildren as $track)
            <div class="px-sm min-w-50 width-unset flex-grow-1">
                <div id="trackbox-{{ $track->contenido->id }}_{{ $track->convocatoria_id }}" class="box box-{{ $track->completado ? "success" : "warning" }} contenido-box collapsed-box">
                    <div class="box-header with-border">
                        <h5 class="box-title">{{ $track->contenido->nombre }}</h5>
                        <div class="box-tools pull-right">
                            @if($track->completado)
                                <span class="label label-success">{{ trans("elearning::profesor/admin_lang.completado") }}</span>&nbsp;&nbsp;
                            @endif
                            <button onclick="getUserStats(this)" data-target="{{ $track->contenido->id }}_{{ $track->convocatoria_id }}" data-scope="contenido" type="button" class="btn btn-box-tool">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="overlay-{{ $track->contenido->id }}_{{ $track->convocatoria_id }}" class="overlay contenido-overlay" style="display: none">
                            <i class="fa fa-refresh fa-spin" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="box-body no-padding" style="display: none;">
                        <div id="trackbody-{{ $track->contenido->id }}_{{ $track->convocatoria_id }}" class="contenido-body col-sm-12">{{-- *** --}}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning">
                {{ trans("elearning::profesor/admin_lang.no_progress") }}
            </div>
        @endforelse
    </div>
</div>
<script>
    $(function () {
        var contenidosChartCanvas = $("#contenidos_chart_{{ $trackScope->modulo_id }}_{{ $trackScope->convocatoria_id }}").get(0).getContext("2d");
        var contenidosChart = new Chart(contenidosChartCanvas);
        var PieData1 = [
            {
                value: parseInt("{{ ($infoAdicional["totales"] - $infoAdicional["totales_vistos"]) }}"),
                color: "#f9f9f9",
                highlight: "#a1a1a1",
                label: "{{ trans("elearning::profesor/admin_lang.no_vistos") }}"
            },
            {
                value: parseInt("{{ $infoAdicional["totales_vistos"] }}"),
                color: "#028517",
                highlight: "#03b31f",
                label: "{{ trans("elearning::profesor/admin_lang.vistos") }}"
            }
        ];
        contenidosChart.Doughnut(PieData1);

        var obligatoriosChartCanvas = $("#obligatorios_chart_{{ $trackScope->modulo_id }}_{{ $trackScope->convocatoria_id }}").get(0).getContext("2d");
        var obligatoriosChart = new Chart(obligatoriosChartCanvas);
        var PieData2 = [
            {
                value: parseInt("{{ ($infoAdicional["obligatorios"] - $infoAdicional["obligatorios_vistos"]) }}"),
                color: "#f9f9f9",
                highlight: "#a1a1a1",
                label: "{{ trans("elearning::profesor/admin_lang.no_vistos") }}"
            },
            {
                value: parseInt("{{ $infoAdicional["obligatorios_vistos"] }}"),
                color: "#852225",
                highlight: "#be3135",
                label: "{{ trans("elearning::profesor/admin_lang.vistos") }}"
            }
        ];
        obligatoriosChart.Doughnut(PieData2);
    });
</script>
