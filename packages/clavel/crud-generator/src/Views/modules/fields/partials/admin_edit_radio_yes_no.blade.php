<div class="box box-primary">
    <div class="box-header  with-border"><h3 class="box-title">{{ trans("crud-generator::fields/admin_lang.extra_options") }} - Checkbox/Visible</h3></div>
    <div class="box-body">
        <div class="form-group">
            {!! Form::label('default_value', trans('crud-generator::fields/admin_lang.default_value'), array('class' => 'col-sm-2 control-label')) !!}
            <div class="col-sm-10">
                <select name="default_value" id="default_value" class="form-control select2">

                    <option value="is_unchecked" @if('is_unchecked'==$field->default_value) selected @endif>Desmarcado por defecto</option>
                    <option value="is_checked" @if('is_checked'==$field->default_value) selected @endif>Marcado por defecto</option>

                </select>

            </div>
        </div>
    </div>
</div>
