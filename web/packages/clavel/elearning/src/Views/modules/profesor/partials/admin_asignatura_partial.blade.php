<?php $idModulosVistos = $tracksChildren->pluck("modulo_id")->toArray() ?>
<div>
    <br/>
    <div class="px-sm">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h6 class="box-title">{{ trans("elearning::profesor/admin_lang.datos_asignatura") }}</h6>
            </div>
            <div class="box-body d-flex flex-wrap">
                <ul class="nav nav-pills nav-stacked min-w-33 px-sm">
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.convocatoria") }}</strong> <span class="pull-right">{{ $trackScope->convocatoria->nombre }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.fecha_inicio") }}</strong> <span class="pull-right">{{ $trackScope->fecha_inicio_formatted }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.fecha_fin") }}</strong> <span class="pull-right">{{ $trackScope->fecha_fin_formatted }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.completado") }}</strong> <span class="pull-right label label-{{ $trackScope->completado ? "success" : "warning" }}">{{ $trackScope->completado ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.aprobado") }}</strong> <span class="pull-right label label-{{ $trackScope->aprobado ? "success" : "danger" }}">{{ $trackScope->aprobado ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.corte") }}</strong> <span class="pull-right">{{ ($trackScope->convocatoria->porcentaje / 10) }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.nota") }}</strong> <span class="pull-right">{{ $trackScope->nota }}</span></li>
                </ul>
                <div class="d-flex justify-content-space-around flex-grow-1">
                    <div class="px-sm">
                        <h4 class="text-center">{{ trans("elearning::profesor/admin_lang.totales") }}: <strong>{{ $infoAdicional["totales"] }}</strong></h4>
                        <div class="chart-responsive">
                            <canvas id="modulos_chart_{{ $trackScope->asignatura_id }}_{{ $trackScope->convocatoria_id }}"></canvas>
                        </div>
                    </div>
                    <div class="px-sm">
                        <h4 class="text-center">{{ trans("elearning::profesor/admin_lang.que_puntua") }}: <strong>{{ $infoAdicional["que_puntua"] }}</strong></h4>
                        <div class="chart-responsive">
                            <canvas id="puntua_chart_{{ $trackScope->asignatura_id }}_{{ $trackScope->convocatoria_id }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h6 class="box-title">{{ trans("elearning::profesor/admin_lang.lista_modulos") }}</h6>
            </div>
            <div class="box-body max-h-overflow-y">
                @if($totales->count() > 0)
                    <table class="table table-striped" aria-hidden="true">
                        <tbody>
                            <tr>
                                <th scope="col">{{ trans("elearning::profesor/admin_lang.modulo") }}</th>
                                <th scope="col">{{ trans("elearning::profesor/admin_lang.puntua") }}</th>
                                <th scope="col">{{ trans("elearning::profesor/admin_lang.peso") }}</th>
                                <th scope="col">{{ trans("elearning::profesor/admin_lang.visto") }}</th>
                            </tr>
                            @foreach($totales as $modulo)
                                <?php $moduloVisto = (in_array($modulo->id, $idModulosVistos)); ?>
                                <tr>
                                    <td>{{ $modulo->nombre }}</td>
                                    <td><span class="label label-{{ $modulo->puntua ? "success" : "info" }}">{{ $modulo->puntua ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></td>
                                    <td>{{ $modulo->peso }}</td>
                                    <td><span class="label label-{{ $moduloVisto ? "success" : "warning" }}">{{ $moduloVisto ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning">
                        {{ trans("elearning::profesor/admin_lang.no_modulos") }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <h4 class="px-sm">{{ trans("elearning::profesor/admin_lang.info_modulos") }}</h4>
    <div class="d-flex justify-content-center flex-wrap">
        @forelse($tracksChildren as $track)
            <div class="px-sm min-w-50 width-unset flex-grow-1">
                <div id="trackbox-{{ $track->modulo->id }}_{{ $track->convocatoria_id }}" class="box box-{{ $track->completado ? "success" : "warning" }} modulo-box collapsed-box w-100">
                    <div class="box-header with-border">
                        <h5 class="box-title">{{ $track->modulo->nombre }}</h5>
                        <div class="box-tools pull-right">
                            @if($track->completado)
                                <span class="label label-success">{{ trans("elearning::profesor/admin_lang.completado") }}</span>&nbsp;&nbsp;
                            @endif
                            <span class="badge bg-{{ $track->aprobado ? "green" : "red" }}">
                                            <i class="fa fa-{{ $track->aprobado ? "check" : "close" }}" aria-hidden="true"></i>
                                        </span>
                            <button onclick="getUserStats(this)" data-target="{{ $track->modulo->id }}_{{ $track->convocatoria_id }}" data-scope="modulo" type="button" class="btn btn-box-tool">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="overlay-{{ $track->modulo->id }}_{{ $track->convocatoria_id }}" class="overlay modulo-overlay" style="display: none">
                            <i class="fa fa-refresh fa-spin" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="box-body no-padding" style="display: none;">
                        <div id="trackbody-{{ $track->modulo->id }}_{{ $track->convocatoria_id }}" class="modulo-body col-sm-12">{{-- *** --}}</div>
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
        var totalesChartCanvas = $("#modulos_chart_{{ $trackScope->asignatura_id }}_{{ $trackScope->convocatoria_id }}").get(0).getContext("2d");
        var totalesChart = new Chart(totalesChartCanvas);
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
        totalesChart.Doughnut(PieData1);

        var puntuaChartCanvas = $("#puntua_chart_{{ $trackScope->asignatura_id }}_{{ $trackScope->convocatoria_id }}").get(0).getContext("2d");
        var puntuaChart = new Chart(puntuaChartCanvas);
        var PieData2 = [
            {
                value: parseInt("{{ ($infoAdicional["que_puntua"] - $infoAdicional["que_puntua_vistos"]) }}"),
                color: "#f9f9f9",
                highlight: "#a1a1a1",
                label: "{{ trans("elearning::profesor/admin_lang.no_vistos") }}"
            },
            {
                value: parseInt("{{ $infoAdicional["que_puntua_vistos"] }}"),
                color: "#852225",
                highlight: "#be3135",
                label: "{{ trans("elearning::profesor/admin_lang.vistos") }}"
            }
        ];
        puntuaChart.Doughnut(PieData2);
    });
</script>
