<!-- Modal para la creación/Modificación de convocatorias -->
<div id="modalConvocatoria" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg">
        <div id="content_block" class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{{ trans('elearning::asignaturas/admin_lang.gestionar_convocatoria') }}</h4>
            </div>
            <div id="responsibe_convocatoria" class="modal-body">

            </div>
        </div>
    </div>
</div>
<!-- Fin Modal para la creación/Modificación de convocatorias -->

<!-- Modal para la visualizar usuarios de grupo -->
<div id="modalUsuariosGrupo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg">
        <div id="content_block" class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{{ trans('elearning::asignaturas/admin_lang.usuarios_grupo_id') }}</h4>
            </div>
            <div id="responsibe_grupo" class="modal-body"></div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button">{{ trans('general/admin_lang.close') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal para la visualizar usuarios de grupo -->

@if(Auth::user()->can("admin-asignaturas-convocatorias-create"))
    <button type="button" class="btn btn-success" onclick="javascript:openConvocatoria('');">
        <i class="fa fa-plus" style="margin-right: 5px;" aria-hidden="true"></i> {{ trans("elearning::asignaturas/admin_lang.nueva_convocatoria") }}
    </button>
    <br clear="all">
    <br clear="all">
@endif

<ul class="timeline">

    @if(count($convocatorias)>0)

        @foreach($convocatorias as $convocatoria)
            <!-- timeline item -->
            <li>
                <?php
                $color = "bg-blue";
                $consulta = ($convocatoria->consultar=='1') ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<i class="fa fa-times text-danger" aria-hidden="true"></i>';
                if ($convocatoria->fecha_inicio<=\Carbon\Carbon::today()->format('Y-m-d') && $convocatoria->fecha_fin>=Carbon\Carbon::today()->format('Y-m-d')) {
                    $color = "bg-maroon";
                }
                ?>
                <!-- timeline icon -->
                <i class="fa fa-calendar {{ $color }}" aria-hidden="true"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fa  fa-clock-o" aria-hidden="true"></i> {{ $convocatoria->fecha_inicio_formatted }} - {{ $convocatoria->fecha_fin_formatted }}</span>

                    <h3 class="timeline-header">{{ $convocatoria->nombre }}</h3>

                    <div class="timeline-body">
                        @if($convocatoria->porcentaje!='')<p><strong>{{ trans("elearning::asignaturas/admin_lang.porcentaje") }}:</strong> {{ $convocatoria->porcentaje }}%</p>@endif
                        @if($convocatoria->creditos!='')<p><strong>{{ trans("elearning::asignaturas/admin_lang.creditos") }}:</strong> {{ $convocatoria->creditos }}</p>@endif
                        @if($convocatoria->certificado_id!='')<p><strong>{{ trans("elearning::asignaturas/admin_lang.certificado") }}:</strong> {{ $convocatoria->certificado->nombre }} <a class="btn btn-primary btn-xs" href="{{ url("admin/certificados/pdf/$convocatoria->certificado_id") }}" target="_blank" style="margin-top: -5px; margin-left: 5px;"><i class="fa fa-search" aria-hidden="true"></i></a></p>@endif
                        @if($convocatoria->gruposPivot->count()>0)
                            <p>
                                <strong>{{ trans("elearning::asignaturas/admin_lang.grupos") }}:</strong>
                                @foreach($convocatoria->gruposPivot as $grupo)
                                    <a href="javascript:modalUsuariosGrupo('{{ $grupo->id }}');" class="btn btn-primary btn-xs" style="margin-top: -5px; margin-left: 5px;">{{ $grupo->nombre }}</a>
                                @endforeach
                            </p>
                        @endif
                        <p><strong>{{ trans("elearning::asignaturas/admin_lang.consultar") }}:</strong> {!! $consulta !!}</p>
                    </div>

                    <div class="timeline-footer pull-right">
                        @if(Auth::user()->can("admin-asignaturas-convocatorias-update"))
                            <a class="btn btn-primary btn-xs" onclick="openConvocatoria('{{ $convocatoria->id }}');"><i class="fa fa-pencil" aria-hidden="true"></i> {{ trans("general/admin_lang.modificar") }}</a>
                        @endif
                        @if(Auth::user()->can("admin-asignaturas-convocatorias-delete"))
                            <a class="btn btn-danger btn-xs" href="javascript:deleteElement('{{ url('admin/asignaturas/convocatorias/'.$convocatoria->id.'/destroy') }}');"><i class="fa fa-times" aria-hidden="true"></i> {{ trans("general/admin_lang.borrar") }}</a>
                        @endif
                    </div>
                    <br clear="all">
                </div>
            </li>
            <!-- END timeline item -->
        @endforeach

    @else
        <li class="time-label">
            <span class="bg-red">
                {{ trans("elearning::asignaturas/admin_lang.no_existe_convocatorias") }}
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
        var url = "{{ url('admin/asignaturas/convocatorias/'.$asignatura_id) }}/formulario/" + id;
        var style = "width: 100%;padding: 50px; text-align: center;";
        $("#responsibe_convocatoria").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
        $("#modalConvocatoria").modal("toggle");
        $("#responsibe_convocatoria").load(url);
    }

    function modalUsuariosGrupo(grupo_id) {
        var url = "{{ url('admin/grupos/') }}/"+grupo_id+"/usuarios";
        var style = "width: 100%;padding: 50px; text-align: center;";
        $("#responsibe_grupo").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
        $("#modalUsuariosGrupo").modal("toggle");
        $("#responsibe_grupo").load(url);
    }

    function deleteElement(url) {
        var strBtn = "";

        $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
        $("#confirmModalBody").html("{{ trans('general/admin_lang.delete_question') }}");
        strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
        strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo(\''+url+'\');">{{ trans('general/admin_lang.borrar_item') }}</button>';
        $("#confirmModalFooter").html(strBtn);
        $('#modal_confirm').modal('toggle');
    }

    function deleteinfo(url) {
        $.ajax({
            url     : url,
            type    : 'POST',
            "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            success : function(data) {
                $('#modal_confirm').modal('hide');
                if(data) {
                    load_info_convocatorias();
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
</script>
