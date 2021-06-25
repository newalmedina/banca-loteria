{!! Form::model($timesheet, $form_data, array('role' => 'form')) !!}

{!! Form::hidden('id', null, array('id' => 'id')) !!}
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="box-body">
                <div class="form-group">
                    {!! Form::label('customer_id', trans('timetracker::mytimes/admin_lang.customer'),
                     array('class' => 'col-sm-2 control-label ')) !!}
                    <div class="col-sm-10">
                        {!! Form::text('customer_id', !empty($timesheet->project_id) ? $timesheet->project->customer->name : null,
                                     array('placeholder' => trans('timetracker::mytimes/admin_lang.customer'),
                                     'class' => 'form-control input-xlarge',
                                     'id' => 'customer_id',
                                     'readonly' => true)) !!}
                    </div>
                </div>


                <div class="form-group">
                    {!! Form::label('project_id', trans('timetracker::mytimes/admin_lang.project'),
                    array('class' => 'col-sm-2 control-label ')) !!}
                    <div class="col-sm-10">
                        {!! Form::text('project_id', !empty($timesheet->project_id) ? $timesheet->project->name : null,
                                     array('placeholder' => trans('timetracker::mytimes/admin_lang.project'),
                                     'class' => 'form-control input-xlarge',
                                     'id' => 'project_id',
                                     'readonly' => true)) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('activity_id', trans('timetracker::mytimes/admin_lang.activity'),
                    array('class' => 'col-sm-2 control-label ')) !!}
                    <div class="col-sm-10">
                        {!! Form::text('activity_id', !empty($timesheet->activity_id) ? $timesheet->activity->name : null,
                                     array('placeholder' => trans('timetracker::mytimes/admin_lang.activity'),
                                     'class' => 'form-control input-xlarge',
                                     'id' => 'activity_id',
                                     'readonly' => true)) !!}
                    </div>
                </div>



                <div class="form-group">
                    {!! Form::label('description', trans('timetracker::mytimes/admin_lang.description').': ', array('class' => 'col-sm-2 control-label', 'style'=>'text-align: left;')) !!}
                    <div class="col-sm-10">
                        {!! Form::textarea('description', null,
                                    array('placeholder' => trans('timetracker::mytimes/admin_lang.description'),
                                    'class' => 'form-control textarea',
                                    'rows' => 3,
                                    'id' => 'description')) !!}
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

        $("#frmDataDescription").submit(function (event) {

            $.ajax({
                type: 'POST',
                url: $(this).attr("action"),
                data: $(this).serialize(),
                success: function (response) {
                    if (response) {
                        $("#modalDescription").modal("hide");
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
        $("#frmDataDescription").submit();
    }
</script>

