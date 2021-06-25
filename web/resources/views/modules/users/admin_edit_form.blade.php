@if($user->id!=0 && auth()->user()->can("admin-users-suplantar"))
    <div class="alert bg-gray mb-md">
        Para suplantar la identidad de este usuario pulsa el botón <a href="{{ url("admin/users/suplantar/".$id) }}" class="btn btn-primary btn-sm" style="margin-left:15px; text-decoration: none !important; "><i class="fa fa-user-secret"></i> Suplantar</a>
    </div>
@endif
{!! Form::model($user, $form_data, array('role' => 'form')) !!}
{!! Form::hidden('iduser', $user->id, array('id' => 'iduser')) !!}
<div class="form-group">
    {!! Form::label('userProfile[first_name]', trans('users/lang._NOMBRE_USUARIO'), array('class' => 'col-sm-2 control-label required-input')) !!}
    <div class="col-md-10">
        {!! Form::text('userProfile[first_name]', null, array('placeholder' => trans('users/lang._INSERTAR_NOMBRE_USUARIO'), 'class' => 'form-control', 'id' => 'first_name')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('userProfile[last_name]', trans('users/lang._APELLIDOS_USUARIO'), array('class' => 'col-sm-2 control-label required-input')) !!}
    <div class="col-md-10">
        {!! Form::text('userProfile[last_name]', null, array('placeholder' => trans('users/lang._INSERTAR_APELLIDOS_USUARIO'), 'class' => 'form-control', 'id' => 'last_name')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('email', Lang::get('users/lang._EMAIL_USUARIO'), array('class' => 'col-md-2 control-label required-input')) !!}
    <div class="col-md-10">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </span>
            {!! Form::text('email', null, array('placeholder' =>  Lang::get('users/lang._INSERTAR_EMAIL_USUARIO'), 'class' => 'form-control')) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('userProfile[gender]', Lang::get('users/lang._genero_sexusal'), array('class' => 'col-md-2 control-label required-input')) !!}
    <div class="col-md-10">
        <div class="radio-list">
            <div class="rdio rdio-primary radio-inline">
                <input id="male" name="userProfile[gender]" type="radio" name="radio" value="male" checked="checked" required />
                {!! trans('users/lang.hombre') !!}
            </div>
            <div class="rdio rdio-primary radio-inline">
                <input id="female" name="userProfile[gender]" type="radio" name="radio" value="female" @if ($user->userProfile->gender == 'female') checked="checked" @endif />
                {!! trans('users/lang.mujer') !!}
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('userProfile[user_lang]', Lang::get('users/lang._USER_LANG'), array('class' => 'col-md-2 control-label required-input')) !!}
    <div class="col-md-10">

            <select name="userProfile[user_lang]" class="form-control">
                @foreach(\App\Models\Idioma::active()->get() as $key=>$value)
                    <option value="{{ $value->code }}" @if($value->code==$user->userProfile->user_lang) selected @endif>{{ $value->name }}</option>
                @endforeach
            </select>

    </div>
</div>

<div class="form-group">
    {!! Form::label('username', trans('users/lang.usuario'), array('class' => 'col-md-2 control-label required-input')) !!}
    <div class="col-md-10">
        {!! Form::text('username', null, array('placeholder' => trans('users/lang._INSERTAR_USUSARIO_USUARIO'), 'class' => 'form-control input-xlarge')) !!}
        <div id="login_info" style="display: none;" class="has-error">
            <span class="help-block"><i class="fa fa-times-circle-o" aria-hidden="true"></i> {{ trans('users/lang._NOTCORRECTUSERLOGIN') }}</span>
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('password', trans('users/lang._CONTASENYA_USUARIO'), array('class' => 'col-md-2 control-label required-input', 'autocomplete'=>'off')) !!}
    <div class="col-md-4">
        {!! Form::text('password', '', array('class' => 'form-control input-xlarge', 'id' => 'password')) !!}
        <div id="pswd_info" style="display: none;">
            <h4>{{ trans('users/lang._KEY_POSIBILITIES_INFO') }}</h4>
            <ul>
                <li id="letter" class="invalid">{{ trans('users/lang._KEY_POSIBILITIES_001') }} <strong>{{ trans('users/lang._KEY_POSIBILITIES_003') }}</strong></li>
                <li id="capital" class="invalid">{{ trans('users/lang._KEY_POSIBILITIES_001') }} <strong>{{ trans('users/lang._KEY_POSIBILITIES_004') }}</strong></li>
                <li id="number" class="invalid">{{ trans('users/lang._KEY_POSIBILITIES_001') }} <strong>{{ trans('users/lang._KEY_POSIBILITIES_005') }}</strong></li>
                <li id="length" class="invalid">{{ trans('users/lang._KEY_POSIBILITIES_002') }}  <strong>{{ trans('users/lang._KEY_POSIBILITIES_006') }}</strong></li>
            </ul>
        </div>
    </div>

    <div class="col-md-5">
        @if((Auth::user()->can('admin-users-create') && $id==0) || (Auth::user()->can('admin-users-update') && $id!=0))
            <button  id="generatePass" class="btn btn-success">
                <i class="fa  fa-key" aria-hidden="true"></i> {{ trans('users/lang._FICPAC_GENERATE_PASS_AUTO') }}
            </button>
        @endif
    </div>

</div>

<div class="form-group">
    {!! Form::label('password_confirmation', trans('users/lang._REPETIR_CONTASENYA_USUARIO'), array('class' => 'col-md-2 control-label required-input', 'autocomplete'=>'off')) !!}
    <div class="col-md-4">
        {!! Form::text('password_confirmation', '', array('class' => 'form-control input-xlarge')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('active', trans('users/lang._ACTIVAR_USUARIO_USUARIO'), array('class' => 'col-md-2 control-label required-input')) !!}
    <div class="col-md-9">
        <div class="radio-list">
            <label class="radio-inline">
                {!! Form::radio('active', 0, true, array('id'=>'active_0')) !!}
                {{ trans('general/admin_lang.no') }}</label>
            <label class="radio-inline">
                {!! Form::radio('active', 1, false, array('id'=>'active_1')) !!}
                {{ trans('general/admin_lang.yes') }} </label>
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('confirmed', trans('users/lang._CONFIRMAR_USUARIO_USUARIO'), array('class' => 'col-md-2 control-label required-input')) !!}
    <div class="col-md-9">
        <div class="radio-list">
            <label class="radio-inline">
                {!! Form::radio('confirmed', 0, true, array('id'=>'confirmed_0')) !!}
                {{ trans('general/admin_lang.no') }}</label>
            <label class="radio-inline">
                {!! Form::radio('confirmed', 1, false, array('id'=>'confirmed_1')) !!}
                {{ trans('general/admin_lang.yes') }} </label>
        </div>
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
    var pwd1 = true;
    var login = true;

    $(document).ready(function() {
        $("#username").change(function() {

            $.ajax({
                url: "{{url('/admin/users/exists/login')}}",
                type: "POST",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                data: {
                    user_id: '{{ $user->id }}',
                    login: $("#username").val()
                },
                success       : function ( data ) {
                    if(data=='NOK') {
                        login = false;
                        $('#login_info').fadeIn(500);
                    } else {
                        login = true;
                        $('#login_info').fadeOut(500);
                    }
                }
            });

        });

        $('#generatePass').click(function () {

            pwd1 = true;

            $.ajax({
                url: "{{url('/admin/users/generate/pass')}}",
                type: "POST",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                success       : function ( data ) {
                    $("#password").val(data);
                    $("#password_confirmation").val(data);
                    $('#pswd_info').fadeOut(500);
                }
            });

            return false;

        });

        if($("#password").val()!='') pwd1  = checkform($("#password").val());

        $('#password').keyup(function() {

            pwd1 = checkform($(this).val());

        }).blur(function() {

            $('#pswd_info').fadeOut(500);

        });

        $("#formData").submit(function( event ) {

            if(!pwd1 && ($("#password").val()!='')) {
                return checkform($("#password").val());
            }

            if(!login) {
                return false;
            }


        });

    });

    function checkform(pswd) {
        var pswdlength 		= false;
        var pswdletter 		= false;
        var pswduppercase 	= false;
        var pswdnumber 		= false;

        if ( pswd.length >= 7 ) {
            $('#length').removeClass('invalid').addClass('valid');
            pswdlength=true;
        } else {
            $('#length').removeClass('valid').addClass('invalid');
        }

        if ( pswd.match(/[A-z]/) ) {
            $('#letter').removeClass('invalid').addClass('valid');
            pswdletter=true;
        } else {
            $('#letter').removeClass('valid').addClass('invalid');
        }

        if ( pswd.match(/[A-Z]/) ) {
            $('#capital').removeClass('invalid').addClass('valid');
            pswduppercase=true;
        } else {
            $('#capital').removeClass('valid').addClass('invalid');
        }

        if ( pswd.match(/\d/) ) {
            $('#number').removeClass('invalid').addClass('valid');
            pswdnumber=true;
        } else {
            $('#number').removeClass('valid').addClass('invalid');
        }

        if( pswdlength && pswdletter && pswduppercase && pswdnumber){
            $('#pswd_info').fadeOut(500);
            return true;
        }else{
            $('#pswd_info').fadeIn(500);
            return false;
        }
    }
</script>

{!! JsValidator::formRequest('App\Http\Requests\AdminUsersRequest')->selector('#formData') !!}
