<ul class="timeline">
@if($modulo->asignatura->convocatorias()->count()>0)

    @foreach($modulo->asignatura->convocatorias()->orderBy("fecha_inicio", "DESC")->orderBy("fecha_fin", "DESC")->get() as $convocatoria)
            <!-- timeline item -->
            <li>
            <?php
            $object_rango = $convocatoria->getRagoModulo($modulo->id);
            $color = "bg-blue";
            if ($convocatoria->fecha_inicio<=\Carbon\Carbon::today()->format('Y-m-d') && $convocatoria->fecha_fin>=Carbon\Carbon::today()->format('Y-m-d')) {
                $color = "bg-maroon";
            }

            if (!is_null($object_rango)) {
                $convocatoria->consultar=$object_rango->consultar;
                $convocatoria->fecha_inicio=$object_rango->fecha_inicio;
                $convocatoria->fecha_fin=$object_rango->fecha_fin;
                $convocatoria->porcentaje=$object_rango->porcentaje;
            }
            $consulta = ($convocatoria->consultar=='1') ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<i class="fa fa-times text-danger" aria-hidden="true"></i>';

            ?>
            <!-- timeline icon -->
                <i class="fa fa-calendar {{ $color }}" aria-hidden="true"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fa  fa-clock-o" aria-hidden="true"></i> {{ $convocatoria->fecha_inicio_formatted }} - {{ $convocatoria->fecha_fin_formatted }}</span>

                    <h3 class="timeline-header">{{ $convocatoria->nombre }}</h3>

                    <div class="timeline-body">
                        @if($convocatoria->porcentaje!='')<p><strong>{{ trans("asignaturas/admin_lang.porcentaje") }}:</strong> {{ $convocatoria->porcentaje }}%</p>@endif
                        @if($convocatoria->creditos!='')<p><strong>{{ trans("asignaturas/admin_lang.creditos") }}:</strong> {{ $convocatoria->creditos }}</p>@endif
                        @if($convocatoria->certificado_id!='')<p><strong>{{ trans("asignaturas/admin_lang.certificado") }}:</strong> {{ $convocatoria->certificado->nombre }} <a class="btn btn-primary btn-xs" target="_blank" href="{{ url("admin/certificados/pdf/$convocatoria->certificado_id") }}" style="margin-top: -5px; margin-left: 5px;"><i class="fa fa-search" aria-hidden="true"></i></a></p>@endif
                        @if($convocatoria->gruposPivot->count()>0)
                            <p>
                                <strong>{{ trans("asignaturas/admin_lang.grupos") }}:</strong>
                                @foreach($convocatoria->gruposPivot as $grupo)
                                    <a href="javascript:modalUsuariosGrupo('{{ $grupo->id }}');" class="btn btn-primary btn-xs" style="margin-top: -5px; margin-left: 5px;">{{ $grupo->nombre }}</a>
                                @endforeach
                            </p>
                        @endif
                        <p><strong>{{ trans("asignaturas/admin_lang.consultar") }}:</strong> {!! $consulta !!}</p>
                    </div>

                    @if($modulo->id!='')
                        <div class="timeline-footer pull-right">
                            @if(Auth::user()->can("admin-asignaturas-convocatorias-update"))
                                <a class="btn btn-primary btn-xs" onclick="openConvocatoria('{{ $convocatoria->id }}');"><i class="fa fa-pencil" aria-hidden="true"></i> {{ trans("general/admin_lang.modificar") }}</a>
                            @endif
                        </div>
                    @endif
                    <br clear="all">
                </div>
            </li>
            <!-- END timeline item -->
        @endforeach

    @else
        <li class="time-label">
                            <span class="bg-red">
                                {{ trans("asignaturas/admin_lang.no_existe_convocatorias") }}
                            </span>
        </li>
    @endif

    <li>
        <i class="fa fa-clock-o bg-gray" aria-hidden="true"></i>
    </li>

</ul>

<script>
    $(document).ready(function() {
        $('#modalConvocatoria').on('hidden.bs.modal', function () {
            load_info_convocatorias();
        });
    });

    function openConvocatoria(id) {
        var url = "{{ url('admin/modulos/convocatorias/'.$modulo->id) }}/formulario/" + id;
        var style = "width: 100%;padding: 50px; text-align: center;";
        $("#responsibe_convocatoria").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
        $("#modalConvocatoria").modal("toggle");
        $("#responsibe_convocatoria").load(url);
    }
</script>
