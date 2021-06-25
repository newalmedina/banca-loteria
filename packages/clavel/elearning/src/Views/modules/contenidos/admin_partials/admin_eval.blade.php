<style>
    .label_disabled {
        text-align:left !important;
        color:#aaaaaa;
    }
</style>
<div class="box box-primary">
    <div class="box-header  with-border"><h3 class="box-title">{{ trans("elearning::contenidos/admin_lang.info_page") }}</h3></div>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">

            <?php
            $nX = 1;
            ?>
            @foreach ($a_trans as $key => $valor)
                <li @if($nX==1) class="active" @endif>
                    <a href="#tabpagina_{{ $key }}" data-toggle="tab">
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
                <div id="tabpagina_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                    <div class="form-group">
                        {!! Form::label('userlang['.$key.'][contenido]', trans('elearning::contenidos/admin_lang.descripcion'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-10">
                            {!! Form::textarea('userlang['.$key.'][contenido]', $contenido->{'contenido:'.$key} , array('class' => 'form-control textarea', 'id' => 'contenido_'.$key)) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('userlang['.$key.'][contenido_aprobado]', trans('elearning::contenidos/admin_lang.contenido_aprobado'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-10">
                            {!! Form::textarea('userlang['.$key.'][contenido_aprobado]', $contenido->{'contenido_aprobado:'.$key} , array('class' => 'form-control textarea', 'id' => 'contenido_aprobado_'.$key)) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('userlang['.$key.'][contenido_suspendido]', trans('elearning::contenidos/admin_lang.contenido_suspendido'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-10">
                            {!! Form::textarea('userlang['.$key.'][contenido_suspendido]', $contenido->{'contenido_suspendido:'.$key} , array('class' => 'form-control textarea', 'id' => 'contenido_suspendido_'.$key)) !!}
                        </div>
                    </div>
                </div>
                <?php
                $nX++;
                ?>
            @endforeach
        </div>
    </div>
</div>
<div class="box box-primary">
    <div class="box-header  with-border"><h3 class="box-title">{{ trans("elearning::contenidos/admin_lang.info_eval") }}</h3></div>
    <div class="box-body">
        <div class="form-group">
            {!! Form::hidden('evaluacion[id]', null, array('id' => 'id')) !!}
            {!! Form::hidden('evaluacion[contenido_id]', $contenido->id, array('id' => 'contenido_id')) !!}
            {!! Form::hidden('evaluacion[modulo_id]', $contenido->modulo->id, array('id' => 'modulo_id')) !!}
            {!! Form::label('evaluacion[mostrar_respuesta]', trans('elearning::contenidos/admin_lang.mostrar_respuesta'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[mostrar_respuesta]', '0', true, array('id'=>'mostrar_respuesta_0')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[mostrar_respuesta]', '1', false, array('id'=>'mostrar_respuesta_1')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('evaluacion[mostrar_resultado]', trans('elearning::contenidos/admin_lang.mostrar_resultado'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[mostrar_resultado]', '0', true, array('id'=>'mostrar_resultado_0')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[mostrar_resultado]', '1', false, array('id'=>'mostrar_resultado_1')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('evaluacion[preguntas_aleatorias]', trans('elearning::contenidos/admin_lang.preguntas_aleatorias'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[preguntas_aleatorias]', '0', true, array('id'=>'preguntas_aleatorias_0')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[preguntas_aleatorias]', '1', false, array('id'=>'preguntas_aleatorias_1')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('evaluacion[respuestas_aleatorias]', trans('elearning::contenidos/admin_lang.respuestas_aleatorias'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[respuestas_aleatorias]', '0', true, array('id'=>'respuestas_aleatorias_0')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[respuestas_aleatorias]', '1', false, array('id'=>'respuestas_aleatorias_1')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('evaluacion[presencial]', trans('elearning::contenidos/admin_lang.presencial'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[presencial]', '0', true, array('id'=>'presencial_0')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[presencial]', '1', false, array('id'=>'presencial_1')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('evaluacion[permitir_resetear]', trans('elearning::contenidos/admin_lang.permitir_resetear'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[permitir_resetear]', '0', true, array('id'=>'permitir_resetear_0','onclick'=>'javascript:setReset(this.value)')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[permitir_resetear]', '1', false, array('id'=>'permitir_resetear_1','onclick'=>'javascript:setReset(this.value)')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>
        <?php
            $tPermiso = "disabled";
            if (isset($contenido->evaluacion) && (bool) $contenido->evaluacion->permitir_resetear) {
                $tPermiso = "";
            }
        ?>
        <div class="form-group">
            {!! Form::label('evaluacion[numero_resets]', trans('elearning::contenidos/admin_lang.numero_resets'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[numero_resets]', '1', true, array('id'=>'numero_resets_1')) !!}
                        1 </label>
                    <label class="radio-inline disabled permitir_resetear">
                        {!! Form::radio('evaluacion[numero_resets]', '2', false, array('class'=>'permitir_resetear','id'=>'numero_resets_2',$tPermiso)) !!}
                        2 </label>
                    <label class="radio-inline disabled permitir_resetear">
                        {!! Form::radio('evaluacion[numero_resets]', '3', false, array('class'=>'permitir_resetear','id'=>'numero_resets_3',$tPermiso)) !!}
                        3 </label>
                    <label class="radio-inline disabled permitir_resetear">
                        {!! Form::radio('evaluacion[numero_resets]', '999', false, array('class'=>'permitir_resetear','id'=>'numero_resets_3',$tPermiso)) !!}
                        Ilimitado </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('evaluacion[porcentaje_aprobado]', trans('elearning::contenidos/admin_lang.porcentaje_aprobado'), array('class' => 'col-md-2 control-label')) !!}
            <div class="col-md-4">
                {!! Form::text('evaluacion[porcentaje_aprobado]', null , array('placeholder' => trans('elearning::contenidos/admin_lang.porcentaje_aprobado'), 'class' => 'form-control','maxlength'=>'3', 'id' => 'porcentaje_aprobado')) !!}
            </div>
        </div>
        {!! Form::hidden('porcentaje_aprobado_old', (isset($contenido->evaluacion)) ? $contenido->evaluacion->porcentaje_aprobado : null, array('id' => 'porcentaje_aprobado_old')) !!}

        <div class="form-group">
            {!! Form::label('evaluacion[numero_preguntas_visibles]', trans('elearning::contenidos/admin_lang.numero_preguntas_visibles'), array('class' => 'col-md-2 control-label')) !!}
            <div class="col-md-4">
                {!! Form::text('evaluacion[numero_preguntas_visibles]', null , array('placeholder' => trans('elearning::contenidos/admin_lang.numero_preguntas_visibles'), 'class' => 'form-control','maxlength'=>'3', 'id' => 'numero_preguntas_visibles')) !!}
            </div>
            {!! Form::label('ilimit', trans('elearning::contenidos/admin_lang.ilimit'), array('class' => 'col-md-4 control-label label_disabled')) !!}
        </div>

        <div class="form-group">
            {!! Form::label('evaluacion[grupos_preguntas]', trans('elearning::contenidos/admin_lang.activar_grupo_preguntas'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[grupos_preguntas]', '0', true, array('id'=>'grupos_preguntas_0')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[grupos_preguntas]', '1', false, array('id'=>'grupos_preguntas_1')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('evaluacion[puntua]', trans('elearning::contenidos/admin_lang.puntua'), array('class' => 'col-md-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[puntua]', '0', true, array('id'=>'puntua_0','onclick'=>'javascript:setPeso(this.value)')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('evaluacion[puntua]', '1', false, array('id'=>'puntua_1','onclick'=>'javascript:setPeso(this.value)')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>
        {!! Form::hidden('puntua_old', (isset($contenido->evaluacion)) ? $contenido->evaluacion->puntua : null, array('id' => 'puntua_old')) !!}
        <?php
        $tPermiso = "disabled";
        if (isset($contenido->evaluacion) && (bool) $contenido->evaluacion->puntua) {
            $tPermiso = "";
        }
        ?>
        <div class="form-group">
            {!! Form::label('evaluacion[peso]', trans('elearning::contenidos/admin_lang.peso'), array('class' => 'col-md-2 control-label')) !!}
            <div class="col-md-4">
                {!! Form::text('evaluacion[peso]', null , array('placeholder' => trans('elearning::contenidos/admin_lang.peso'), 'class' => 'form-control',$tPermiso,'maxlength'=>'4', 'id' => 'peso')) !!}
            </div>
        </div>
        <?php

         ?>
        {!! Form::hidden('peso_old', (isset($contenido->evaluacion)) ? $contenido->evaluacion->peso : null, array('id' => 'peso_old')) !!}
    </div>
</div>

<script>
    function setReset(val) {
        if(val == 1) {
            $(".permitir_resetear").attr("disabled",false);
            $("label.permitir_resetear").removeClass("disabled");
        }else{
            $(".permitir_resetear").attr("disabled",true);
            $("label.permitir_resetear").addClass("disabled");
            $("#numero_resets_1").prop("checked", true);
        }
    }

    function setPeso(val) {
        if(val == 1) {
            $("#peso").attr("disabled",false);
        }else{
            $("#peso").attr("disabled",true);
        }
    }
</script>
