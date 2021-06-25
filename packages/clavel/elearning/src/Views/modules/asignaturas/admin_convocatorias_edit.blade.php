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
            {!! Form::hidden('asignatura_id', null, array('id' => 'asignatura_id')) !!}

            <div class="row form-group">
                <div class="col-md-12">
                    {!! Form::label('nombre', trans('elearning::convocatorias/admin_lang.nombre'), array('class' => 'control-label')) !!}
                    {!! Form::text('nombre', null, array('placeholder' => trans('elearning::convocatorias/admin_lang.nombre'), 'class' => 'form-control', 'nombre')) !!}
                </div>
            </div>

            <div class="row form-group">
                <div class="col-md-6">
                    {!! Form::label('fecha_inicio', trans('elearning::convocatorias/admin_lang.fecha_inicio'), array('class' => 'control-label')) !!}
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                        </div>
                        {!! Form::text('fecha_inicio', $convocatoria->fecha_inicio_formatted, array('placeholder' => trans('elearning::convocatorias/admin_lang.fecha_inicio_insertar'),'readonly'=>'true', 'class' => 'form-control', 'id' => 'fecha_inicio')) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    {!! Form::label('fecha_fin', trans('elearning::convocatorias/admin_lang.fecha_fin'), array('class' => 'control-label')) !!}
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                        </div>
                        {!! Form::text('fecha_fin', $convocatoria->fecha_fin_formatted, array('placeholder' => trans('elearning::convocatorias/admin_lang.fecha_fin_insertar'),'readonly'=>'true', 'class' => 'form-control', 'id' => 'fecha_fin')) !!}
                    </div>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-md-6">
                    {!! Form::label('porcentaje', trans('elearning::convocatorias/admin_lang.porcentaje_2'), array('class' => 'control-label')) !!}
                    <div class="input-group">
                        <div class="input-group-addon">
                            %
                        </div>
                        {!! Form::text('porcentaje', null, array('placeholder' => trans('elearning::convocatorias/admin_lang.porcentaje_2'), 'class' => 'form-control', 'porcentaje')) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    {!! Form::label('creditos', trans('elearning::convocatorias/admin_lang.creditos'), array('class' => 'control-label')) !!}
                    {!! Form::text('creditos', null, array('placeholder' => trans('elearning::convocatorias/admin_lang.creditos'), 'class' => 'form-control', 'creditos')) !!}
                </div>
            </div>

        <div class="row form-group">
            <div class="col-md-12">
                {!! Form::label('limite_finalizacion', trans('elearning::convocatorias/admin_lang.control'), array('class' => 'control-label')) !!}
                {!! Form::text('limite_finalizacion', null, array('placeholder' => trans('elearning::convocatorias/admin_lang.control_1'), 'class' => 'form-control', 'control')) !!}
            </div>
        </div>

            <div class="row form-group">
                <div class="col-md-12">
                    {!! Form::label('certificado_id', trans('elearning::convocatorias/admin_lang.certificado_id'), array('class' => 'control-label')) !!}
                    <select name="certificado_id" class="form-control">
                        <option value="">{{ trans("elearning::convocatorias/admin_lang.sin_certificado") }}</option>
                        @foreach($certificados as $certificado)
                            <option value="{{ $certificado->id }}" @if($certificado->id==$convocatoria->certificado_id) selected @endif>{{ $certificado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-md-12">
                    {!! Form::label('sel_grupos', trans('elearning::convocatorias/admin_lang.sel_grupos'), array('class' => 'control-label')) !!}
                    <select class="form-control select2" name="sel_grupos[]" multiple="multiple" data-placeholder="{{ trans('elearning::convocatorias/admin_lang.sel_grupos') }}" style="width: 100%;">
                        @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id }}" @if($convocatoria->grupoSelected($grupo->id)) selected @endif>{{ $grupo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-md-12">
                    {!! Form::label('consultar', trans('elearning::convocatorias/admin_lang.consultar_2'), array('class' => 'control-label')) !!}
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

<script type="text/javascript" src="{{ asset('/assets/admin/vendor/datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/admin/vendor/datepicker/locales/bootstrap-datepicker.'.config('app.locale'). '.min.js')}}"></script>
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
            language: 'es'
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
        $("#sender").html("<div class='overlay'><i class='fa fa-refresh fa-spin'></i> {{ trans('elearning::asignaturas/admin_lang.save_in') }}</div>");
        $("#sender").addClass("disabled");
        $("#formDataConvocatoria").submit();
    }
</script>

{!! JsValidator::formRequest('Clavel\Elearning\Requests\ConvocatoriaRequest')->selector('#formDataConvocatoria') !!}
