{!! Form::model($role, $form_data, array('role' => 'form')) !!}

<div class="form-group">
    {!! Form::label('name', trans('roles/lang.roles_nombre_corto'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
    <div class="col-md-10">
        {!! Form::text('name', null, array('placeholder' => trans('roles/lang.roles_nombre_corto_insertar'), 'class' => 'form-control', 'id' => 'first_name', 'readonly' => true)) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('display_name', trans('roles/lang.nombre_roles'), array('class' => 'col-sm-2 control-label required-input')) !!}
    <div class="col-md-10">
        {!! Form::text('display_name', null, array('placeholder' => trans('roles/lang.nombre_roles_insertar'), 'class' => 'form-control', 'id' => 'first_name')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('description', trans('roles/lang.description'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-md-10">
        {!! Form::text('description', null, array('placeholder' => trans('roles/lang.description_insertar'), 'class' => 'form-control', 'id' => 'first_name')) !!}
    </div>
</div>

@if(!$role->fixed)
    <div class="form-group">
        {!! Form::label('active', Lang::get('roles/lang._activar_roles'), array('class' => 'col-md-2 control-label required-input')) !!}
        <div class="col-md-9">
            <div class="radio-list">
                <label class="radio-inline">
                    {!! Form::radio('active', 0, true, array('id'=>'active_0')) !!}
                    {{ Lang::get('general/admin_lang.no') }}</label>
                <label class="radio-inline">
                    {!! Form::radio('active', 1, false, array('id'=>'active_1')) !!}
                    {{ Lang::get('general/admin_lang.yes') }} </label>
            </div>
        </div>
    </div>
@endif

<br clear="all">

<div class="box-footer">

    <a href="{{ url('/admin/roles') }}" class="btn btn-default">{{ trans('roles/lang.cancelar') }}</a>
    @if((Auth::user()->can('admin-roles-create') && $id==0) || (Auth::user()->can('admin-roles-update') && $id!=0))
        <button type="submit" class="btn btn-info pull-right">{{ trans('roles/lang.guardar') }}</button>
    @endif

</div>

{!! Form::close() !!}

<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

{!! JsValidator::formRequest('App\Http\Requests\AdminRolesRequest')->selector('#formData') !!}