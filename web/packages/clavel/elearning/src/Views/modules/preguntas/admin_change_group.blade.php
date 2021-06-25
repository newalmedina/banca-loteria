{!! Form::model($pregunta, $form_data, array('role' => 'form')) !!}
{!! Form::hidden('contenido_id', $contenido->id, array('id' => 'contenido_id')) !!}
{!! Form::hidden('id', null, array('id' => 'id')) !!}

<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="box-body">
                <div class="form-group">
                    {!! Form::label('name', trans('elearning::contenidos/admin_lang.pregunta'), array('class' => 'col-sm-4 control-label required required-input')) !!}
                    <div class="col-sm-8">
                        {!! Form::text('name', strip_tags($pregunta->nombre),
                            array('placeholder' => trans('elearning::contenidos/admin_lang.pregunta'),
                            'class' => 'form-control input-xlarge',
                            'disabled' => 'disabled',
                            'id' => 'name')) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('grupo', trans('elearning::contenidos/admin_lang.grupo_preguntas'), array('class' => 'col-sm-4 control-label ')) !!}
                    <div class="col-sm-8">
                        <select name="grupo" id="grupo" class="form-control select2">
                            <option value="0">{{ trans('elearning::contenidos/admin_lang.sin_grupo_preguntas') }}</option>
                            @foreach($gruposList as $key=>$value)
                                <option value="{{ $value->id }}"
                                        @if($value->id==$pregunta->grupo_pregunta_id) selected @endif>{{ $value->titulo }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>


            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <a id="cancel" data-dismiss="modal" class="btn btn-default pull-left">{{ trans('general/admin_lang.close') }}</a>
    <a id="sender" class="btn btn-info pull-right"
       href="javascript:senderForm();">{{ trans('general/admin_lang.save') }}</a>
</div>

{!! Form::close() !!}

<script>
    $(document).ready(function () {
        $(".select2").select2();

        $("#sender").html("{{ trans('general/admin_lang.save') }}");
        $("#sender").removeClass("disabled");

        $("#frmDataGrupoPregunta").submit(function (event) {

            $.ajax({
                type: 'POST',
                url: $(this).attr("action"),
                data: $(this).serialize(),
                success: function (response) {
                    if (response) {
                        $("#modalGrupoPreguntas").modal("hide");
                        oTable.ajax.reload(null, false);
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
        $("#sender").html("<div class='overlay'><i class='fa fa-refresh fa-spin'></i> {{ trans('elearning::contenidos/admin_lang.save') }}</div>");
        $("#sender").addClass("disabled");
        $("#frmDataGrupoPregunta").submit();
    }
</script>


