@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section("head_page")

@stop

@section('breadcrumb')

    <li><a href="{{ url("admin/alumnos") }}">{{ trans('elearning::alumnos/admin_lang.alumnos') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    <link href="{{ asset("/assets/admin/vendor/datepicker/css/bootstrap-datepicker.min.css") }}" rel="stylesheet" type="text/css" />
    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::model($user, $form_data, array('role' => 'form')) !!}
        {!! Form::hidden('iduser', $user->id, array('id' => 'iduser')) !!}
        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                <div class="box-body">


                    <div class="form-group">
                        {!! Form::label('userProfile[first_name]', trans('elearning::alumnos/admin_lang.nombre'), array('class' => 'col-sm-2 control-label required-input')) !!}
                        <div class="col-md-10">
                            {!! Form::text('userProfile[first_name]', null, array('placeholder' => trans('elearning::alumnos/admin_lang.nombre'), 'class' => 'form-control', 'id' => 'first_name')) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('userProfile[last_name]', trans('elearning::alumnos/admin_lang.apellidos'), array('class' => 'col-sm-2 control-label required-input')) !!}
                        <div class="col-md-10">
                            {!! Form::text('userProfile[last_name]', null, array('placeholder' => trans('elearning::alumnos/admin_lang.apellidos'), 'class' => 'form-control', 'id' => 'last_name')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('userProfile[birthdate]', trans('elearning::alumnos/admin_lang.birthdate'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                         <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                    </span>
                            {!! Form::text('userProfile[birthdate]', $user->userProfile->birthdate_formatted ?? null, array('placeholder' => trans('elearning::alumnos/admin_lang.birthdate'), 'readonly'=>'true','class' => 'form-control', 'id' => 'birthdate')) !!}
                                </div>
                            </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('email', trans('elearning::alumnos/admin_lang.email'), array('class' => 'col-md-2 control-label required-input')) !!}
                        <div class="col-md-10">
                            <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                    </span>
                                {!! Form::text('email', null, array('placeholder' =>  trans('elearning::alumnos/admin_lang.email'), 'class' => 'form-control')) !!}
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('userProfile[gender]', trans('elearning::alumnos/admin_lang.genero'), array('class' => 'col-md-2 control-label required-input')) !!}
                        <div class="col-md-10">
                            <div class="radio-list">
                                <div class="rdio rdio-primary radio-inline">
                                    <input id="male" name="userProfile[gender]" type="radio" name="radio" value="male" checked="checked" required />
                                    {!! trans('elearning::alumnos/admin_lang.hombre') !!}
                                </div>
                                <div class="rdio rdio-primary radio-inline">
                                    <input id="female" name="userProfile[gender]" type="radio" name="radio" value="female" @if (!empty($user->userProfile) && $user->userProfile->gender == 'female') checked="checked" @endif />
                                    {!! trans('elearning::alumnos/admin_lang.mujer') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('userProfile[user_lang]', trans('users/lang._USER_LANG'), array('class' => 'col-md-2 control-label required-input')) !!}
                        <div class="col-md-10">

                            <select name="userProfile[user_lang]" class="form-control">
                                @foreach(\App\Models\Idioma::active()->get() as $key=>$value)
                                    <option value="{{ $value->code }}" @if(!empty($user->userProfile) && $value->code==$user->userProfile->user_lang) selected @endif>{{ $value->name }}</option>
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
                        {!! Form::label('sel_grupos', trans('elearning::alumnos/admin_lang.grupos'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div class="col-md-10">
                            <select class="form-control select2" name="sel_grupos[]" multiple="multiple" data-placeholder="{{ trans('elearning::alumnos/admin_lang.grupos') }}" style="width: 100%;">
                                @foreach($grupos as $grupo)
                                    <option value="{{ $grupo->id }}"  @if($grupo->alumnoSelected($user->id)) selected @endif>({{ $grupo->id }}) {{ $grupo->nombre }}</option>
                                @endforeach
                            </select>

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
                            @if((Auth::user()->can('admin-alumnos-create') && $user->id==0) || (Auth::user()->can('admin-users-update') && $user->id!=0))
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
                    <br>
                </div>
            </div>

            <div class="box box-solid">
                <div class="box-footer">
                    <a href="{{ url('/admin/alumnos') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    @if((Auth::user()->can('admin-alumnos-create') && $user->id==0) || (Auth::user()->can('admin-alumnos-update') && $user->id!=0))
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
                    @endif
                </div>
            </div>
        </div>

        {!! Form::close() !!}
    </div>

@endsection

@section('foot_page')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/assets/admin/vendor/datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/assets/front/vendor/datepicker/locales/bootstrap-datepicker.'.config('app.locale'). '.js')}}"></script>

    <script>
        var pwd1 = true;
        var login = true;

        $(document).ready(function() {
            $(".select2").select2();

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

            var startDate = new Date();
            startDate.setFullYear( startDate.getFullYear() - 15 );

            $("#birthdate").datepicker({
                format: 'dd/mm/yyyy',
                language: '{!!  config('app.locale') !!}',
                autoclose: true,
                startDate: '-100y',
                endDate: '0d',
                weekStart: 1,
                orientation: "bottom left"
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
    @if(empty($user->id))
        {!! JsValidator::formRequest('Clavel\Elearning\Requests\AdminAlumnoCreateRequest')->selector('#formData') !!}
    @else
        {!! JsValidator::formRequest('Clavel\Elearning\Requests\AdminAlumnoUpdateRequest')->selector('#formData') !!}
    @endif
@stop
