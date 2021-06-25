<link href="{{ asset("/assets/admin/vendor/datepicker/css/bootstrap-datepicker.min.css") }}" rel="stylesheet" type="text/css" />
<style>
    .datepicker {
        z-index:1050 !important;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        {!! Form::model($convocatoria, ['role' => 'form', 'id' => 'formDataConvocatoria', 'method' => 'POST']) !!}
            {!! Form::hidden('id', null, array('id' => 'id')) !!}
            {!! Form::hidden('modulo_id', null, array('id' => 'modulo_id')) !!}
            {!! Form::hidden('convocatoria_id', null, array('id' => 'convocatoria_id')) !!}

            <div class="row form-group">
                <div class="col-md-6">
                    {!! Form::label('fecha_inicio', trans('convocatorias/admin_lang.fecha_inicio'), array('class' => 'control-label')) !!}
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                        </div>
                        {!! Form::text('fecha_inicio', $convocatoria->fecha_inicio_formatted, array('placeholder' => trans('convocatorias/admin_lang.fecha_inicio_insertar'),'readonly'=>'true', 'class' => 'form-control', 'id' => 'fecha_inicio')) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    {!! Form::label('fecha_fin', trans('convocatorias/admin_lang.fecha_fin'), array('class' => 'control-label')) !!}
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                        </div>
                        {!! Form::text('fecha_fin', $convocatoria->fecha_fin_formatted, array('placeholder' => trans('convocatorias/admin_lang.fecha_fin_insertar'),'readonly'=>'true', 'class' => 'form-control', 'id' => 'fecha_fin')) !!}
                    </div>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-md-6">
                    {!! Form::label('porcentaje', trans('convocatorias/admin_lang.porcentaje_2'), array('class' => 'control-label')) !!}
                    <div class="input-group">
                        <div class="input-group-addon">
                            %
                        </div>
                        {!! Form::text('porcentaje', null, array('placeholder' => trans('convocatorias/admin_lang.porcentaje_2'), 'class' => 'form-control', 'porcentaje')) !!}
                    </div>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-md-12">
                    {!! Form::label('consultar', trans('convocatorias/admin_lang.consultar_2'), array('class' => 'control-label')) !!}
                    <div class="radio-list">
                        <label class="radio-inline">
                            {!! Form::radio('consultar', '0', true, array('id'=>'consultar_0')) !!}
                            {{ Lang::get('general/admin_lang.no') }}</label>
                        <label class="radio-inline">
                            {!! Form::radio('consultar', '1', false, array('id'=>'consultar_1')) !!}
                            {{ Lang::get('general/admin_lang.yes') }} </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a id="cancel" data-dismiss="modal" class="btn btn-default pull-left">{{ trans('general/admin_lang.close') }}</a>
                <a id="sender" class="btn btn-info pull-right" href="javascript:senderForm();">{{ trans('general/admin_lang.save') }}</a>
            </div>

        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript" src="{{ asset('assets/js/datepicker/bootstrap-datepicker.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/datepicker/locales/bootstrap-datepicker.'.config('app.locale'). '.js')}}"></script>
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
<script>
    $(document).ready(function() {
        $("#sender").html("{{ trans('general/admin_lang.save') }}");
        $("#sender").removeClass("disabled");
        $(".select2").select2();

        $("#fecha_inicio, #fecha_fin").datepicker({
            isRTL: false,
            format: 'dd/mm/yyyy',
            autoclose:true,
            language: 'es',
            startDate: new Date('{{ $convocatoriaAsignatura->fecha_inicio }}'),
            endDate: new Date('{{ $convocatoriaAsignatura->fecha_fin }}')
        });

        $("#formDataConvocatoria").submit(function( event ) {
            if(! $(this).valid()) {
                $("#sender").html("{{ trans('general/admin_lang.save') }}");
                $("#sender").removeClass("disabled");
                return false;
            }
            $.ajax({
                type:'POST',
                url:$(this).attr("action"),
                data:$(this).serialize(),
                success:function(response) {
                    if(response.success) {
                        $("#modalConvocatoria").modal("hide");
                    } else {
                        $("#sender").html("{{ trans('general/admin_lang.save') }}");
                        $("#sender").removeClass("disabled");
                        alert("ERROR GUARDANDO");
                    }
                    return false;
                }
            });
            return false;
        });
    });

    function senderForm() {
        $("#sender").html("<div class='overlay'><i class='fa fa-refresh fa-spin'></i> {{ trans('asignaturas/admin_lang.save_in') }}</div>");
        $("#sender").addClass("disabled");
        $("#formDataConvocatoria").submit();
    }
</script>

{!! JsValidator::formRequest('App\Http\Requests\ConvocatoriaModuloRequest')->selector('#formDataConvocatoria') !!}
