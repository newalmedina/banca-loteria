<div>
    <br/>
    <div class="px-sm">
        <div class="box box-primary">
            <div class="box-body d-flex flex-wrap">
                <ul class="nav nav-stacked min-w-50 px-sm">
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.obligatorio") }}</strong> <span class="pull-right">{{ $trackScope->contenido->obligatorio ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.tipo_contenido") }}</strong> <span class="pull-right">{{ $trackScope->contenido->tipo->nombre }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.fecha_lectura") }}</strong> <span class="pull-right">{{ $trackScope->fecha_lectura_formatted }}</span></li>
                    <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.completado") }}</strong> <span class="pull-right label label-{{ $trackScope->completado ? "success" : "warning" }}">{{ $trackScope->completado ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></li>
                    @if($trackScope->contenido->tipo->slug == "eval" && $infoAdicional["trackEval"] !== null)
                        <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.aprobado") }}</strong> <span id="aprobado_{{ $infoAdicional["trackEval"]->id }}" class="pull-right label label-{{ $infoAdicional["trackEval"]->aprobado ? "success" : "danger" }}">{{ $infoAdicional["trackEval"]->aprobado ? trans("general/admin_lang.yes") : trans("general/admin_lang.no") }}</span></li>
                        <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.corte") }}</strong> <span class="pull-right">{{ ($trackScope->contenido->evaluacion->porcentaje_aprobado / 10) }}</span></li>
                        <li class="py-sm"><strong>{{ trans("elearning::profesor/admin_lang.nota") }}</strong> <span id="nota_{{ $infoAdicional["trackEval"]->id }}" class="pull-right">{{ $infoAdicional["trackEval"]->nota }}</span></li>
                    @endif
                </ul>
                <div class="px-sm flex-grow-1 d-flex" style="align-items: end">
                    @if($trackScope->contenido->tipo->slug == "eval"&& $infoAdicional["trackEval"] !== null)
                        <button onclick="recalcular('{{ url("admin/profesor/contenido/recalcular/" . $infoAdicional["trackEval"]->id) }}')" class="btn btn-success">{{ trans("elearning::profesor/admin_lang.recalcular") }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
