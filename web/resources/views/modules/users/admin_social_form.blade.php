{!! Form::model($user, $form_data, array('role' => 'form')) !!}
{!! Form::hidden('id', $user->id, array('id' => 'id')) !!}

<div class="form-group">
    {!! Form::label('userProfile[facebook]', Lang::get('users/lang.facebook'), array('class' => 'col-md-2 control-label')) !!}
    <div class="col-md-10">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-facebook" aria-hidden="true"></i>
            </span>
            {!! Form::text('userProfile[facebook]', null, array('placeholder' =>  Lang::get('users/lang.facebook'), 'class' => 'form-control', 'id' => 'facebook')) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('userProfile[twitter]', Lang::get('users/lang.twitter'), array('class' => 'col-md-2 control-label')) !!}
    <div class="col-md-10">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-twitter" aria-hidden="true"></i>
            </span>
            {!! Form::text('userProfile[twitter]', null, array('placeholder' =>  Lang::get('users/lang.twitter'), 'class' => 'form-control', 'id' => 'twitter')) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('userProfile[linkedin]', Lang::get('users/lang.linkedin'), array('class' => 'col-md-2 control-label')) !!}
    <div class="col-md-10">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-linkedin" aria-hidden="true"></i>
            </span>
            {!! Form::text('userProfile[linkedin]', null, array('placeholder' =>  Lang::get('users/lang.linkedin'), 'class' => 'form-control', 'id' => 'linkedin')) !!}
        </div>
    </div>
</div>


<div class="form-group">
    {!! Form::label('userProfile[youtube]', Lang::get('users/lang.youtube'), array('class' => 'col-md-2 control-label')) !!}
    <div class="col-md-10">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-youtube"></i>
            </span>
            {!! Form::text('userProfile[youtube]', null, array('placeholder' =>  Lang::get('users/lang.youtube'), 'class' => 'form-control', 'id' => 'youtube')) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('userProfile[bio]', trans('users/lang.bio'), array('class' => 'col-md-2 control-label')) !!}
    <div class="col-md-10">
        {!! Form::textarea('userProfile[bio]', null, array('placeholder' =>  Lang::get('users/lang.bio'), 'class' => 'form-control textarea', 'id' => 'bio')) !!}
    </div>
</div>


<br clear="all">

<div class="box-footer">

    <a href="{{ url('/admin/users') }}" class="btn btn-default">{{ trans('users/lang.cancelar') }}</a>
    @if((Auth::user()->can('admin-users-create') && $id==0) || (Auth::user()->can('admin-users-update') && $id!=0))
        <button type="submit" class="btn btn-info pull-right">{{ trans('users/lang.guardar') }}</button>
    @endif

</div>

{!! Form::close() !!}

<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

<script>
    $(document).ready(function() {

    });

</script>

{!! JsValidator::formRequest('App\Http\Requests\AdminUsersSocialRequest')->selector('#formData') !!}
