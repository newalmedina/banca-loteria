<div class="box box-primary">
    <div class="box-header  with-border"><h3 class="box-title">{{ trans("crud-generator::fields/admin_lang.extra_options") }} - Textarea</h3></div>
    <div class="box-body">
        <div class="form-group">
            {!! Form::label('is_multilang', trans('crud-generator::fields/admin_lang.is_multilang'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('is_multilang', '0', true, array('id'=>'is_multilang_0')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('is_multilang', '1', false, array('id'=>'is_multilang_1')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('use_editor', trans('crud-generator::fields/admin_lang.use_editor'), array('class' => 'col-sm-2 control-label')) !!}
            <div class="col-sm-10">
                <select name="use_editor" id="use_editor" class="form-control select2">

                    <option value="no" @if('no'==$field->use_editor) selected @endif>No</option>
                    <option value="tiny" @if('tiny'==$field->use_editor) selected @endif>Tiny Editor</option>

                </select>

            </div>
        </div>
    </div>
</div>
