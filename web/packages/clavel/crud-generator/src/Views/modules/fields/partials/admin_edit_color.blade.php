<div class="box box-primary">
    <div class="box-header  with-border"><h3 class="box-title">{{ trans("crud-generator::fields/admin_lang.extra_options") }} - Color</h3></div>
    <div class="box-body">
        <div class="form-group">
            {!! Form::label('default_value', trans('crud-generator::fields/admin_lang.color'), array('class' => 'col-sm-2 control-label')) !!}
            <div class="col-sm-10">
                <div class="input-group default_value_colorpicker colorpicker-element">
                    {!! Form::text('default_value', null, array('placeholder' => trans('crud-generator::fields/admin_lang.select_color'), 'class' => 'form-control', 'id' => 'default_value')) !!}

                    <div class="input-group-addon">
                        <em style="background-color: rgb(136, 119, 119);"></em>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
