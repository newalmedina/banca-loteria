{!! Form::model($project, $form_data, array('role' => 'form')) !!}

{!! Form::hidden('id', null, array('id' => 'id')) !!}
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="box-body">
                <div class="form-group">
                    {!! Form::label('name', trans('timetracker::projects/admin_lang.name'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                    <div class="col-sm-10">
                        {!! Form::text('name', null,
                            array('placeholder' => trans('timetracker::projects/admin_lang.name'),
                            'class' => 'form-control input-xlarge',
                            'disabled' => 'disabled',
                            'id' => 'name')) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('slug_type', trans('timetracker::projects/admin_lang.type'), array('class' => 'col-sm-2 control-label ')) !!}
                    <div class="col-sm-10">
                        <select name="slug_type" id="slug_type" class="form-control select2">
                            @foreach($typesList as $key=>$value)
                                <option value="{{ $value->id }}"
                                        @if($value->id==$project->project_type_id) selected @endif>{{ $value->name }}</option>
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
        $("#sender").html("{{ trans('general/admin_lang.save') }}");
        $("#sender").removeClass("disabled");

        $("#frmDataTypeProject").submit(function (event) {

            $.ajax({
                type: 'POST',
                url: $(this).attr("action"),
                data: $(this).serialize(),
                success: function (response) {
                    if (response) {
                        $("#modalTipoProyecto").modal("hide");
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
        $("#sender").html("<div class='overlay'><i class='fa fa-refresh fa-spin'></i> {{ trans('timetracker::projects/admin_lang.save_in') }}</div>");
        $("#sender").addClass("disabled");
        $("#frmDataTypeProject").submit();
    }
</script>

