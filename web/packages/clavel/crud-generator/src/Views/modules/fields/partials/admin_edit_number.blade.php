<div class="box box-primary">
    <div class="box-header  with-border"><h3 class="box-title">{{ trans("crud-generator::fields/admin_lang.extra_options") }} - Texto</h3></div>
    <div class="box-body">
        <div class="form-group">
            {!! Form::label('min_length', trans('crud-generator::fields/admin_lang.min_value'), array('class' => 'col-sm-2 control-label')) !!}
            <div class="col-sm-10">
                {!! Form::text('min_length', null,
                    array('placeholder' => trans('crud-generator::fields/admin_lang.min_value'),
                    'class' => 'form-control input-xlarge',
                    'id' => 'min_length')) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('max_length', trans('crud-generator::fields/admin_lang.max_value'), array('class' => 'col-sm-2 control-label')) !!}
            <div class="col-sm-10">
                {!! Form::text('max_length', null,
                    array('placeholder' => trans('crud-generator::fields/admin_lang.max_value'),
                    'class' => 'form-control input-xlarge',
                    'id' => 'max_length')) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('default_value', trans('crud-generator::fields/admin_lang.default_value'), array('class' => 'col-sm-2 control-label')) !!}
            <div class="col-sm-10">
                {!! Form::text('default_value', null,
                    array('placeholder' => trans('crud-generator::fields/admin_lang.default_value'),
                    'class' => 'form-control input-xlarge',
                    'id' => 'default_value')) !!}
            </div>
        </div>
    </div>
</div>
