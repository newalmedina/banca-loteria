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
                    {!! Form::label('slug_state', trans('timetracker::projects/admin_lang.state'), array('class' => 'col-sm-2 control-label ')) !!}
                    <div class="col-sm-10">
                        <select name="slug_state" id="slug_state" class="form-control select2">
                            @foreach($statesList as $key=>$value)
                                <option value="{{ $value->slug }}"
                                        @if($value->slug==$project->slug_state) selected @endif>{{ $value->name }}</option>
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

        $("#frmDataStateProject").submit(function (event) {

            $.ajax({
                type: 'POST',
                url: $(this).attr("action"),
                data: $(this).serialize(),
                success: function (response) {
                    if (response) {
                        $("#modalEstadoProyecto").modal("hide");
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
        $("#frmDataStateProject").submit();
    }
</script>

