@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop


@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@endsection


@section("head_page")

@stop

@section('content')

    <div class="container pb-xlg">
        @include('front.includes.errors')
        <div class="d-flex justify-content-center align-items-center">

            @if(config("elearning.autentificacion.TIPO_REGISTRO")=='2' && empty($codigo_id))
                <div id="codeIntroductor" class="light-rounded-box row p-lg">
                    <div class="d-flex align-items-center pb-lg">
                        <div class="svg_img_wrapper">
                            <img src="{{ asset("assets/front/img/inicio/icon-user.svg") }}" alt="login_icon">
                        </div>
                        <h4 class="mt-xs mb-xs">{{ trans('auth/lang.registrarse_en_la_web') }}</h4>
                    </div>
                    <div class="col-sm-12">
                        <p>{{ trans("auth/lang.signup_05") }}</p>
                        <div style="padding-bottom: 30px;">
                            <div class="form-group text-left">
                                {!! Form::label('codigo', trans('profile/front_lang.codigo')) !!} <span class="text-danger">*</span>
                                {!! Form::text('codigo', null, array('placeholder' =>  trans('profile/front_lang.codigo'), 'class' => 'form-control', 'id'=>'codigo')) !!}
                            </div>

                            <div class="box-footer">
                                <button id="btnSendCode" onclick="validateCode();" class="btn btn-default text-uppercase background-color-secondary pt-sm pr-xlg pb-sm pl-xlg mt-xlg text-light has-spinner"><span class="spinner"><img src="{{ asset("assets/front/img/ajax_loader_vector.gif") }}" width="16" alt=""> </span> {{ trans('profile/front_lang.enviar') }}</button>
                            </div>
                        </div>

                    </div>
                </div>
            @endif


            <div id="register_form" @if(config("elearning.autentificacion.TIPO_REGISTRO")=='2'  && empty($codigo_id)) style="display:none;" @endif>

                {!! Form::model($user, ['role' => 'form', 'id' => 'formData', 'method' => 'POST']) !!}
                {!! Form::hidden('codigo_id', $codigo_id, array('id'=>'codigo_id')) !!}
                <div class="light-rounded-box row p-lg">
                    <div class="col-sm-12">

                        <div class="row d-flex align-items-center pb-lg">
                            <div class="svg_img_wrapper">
                                <img src="{{ asset("assets/front/img/inicio/icon-user.svg") }}" alt="login_icon">
                            </div>
                            <h4 class="mt-xs mb-xs">{{ trans('auth/lang.registrarse_en_la_web') }}</h4>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <p>{{ trans("auth/lang.signup_06") }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                {!! Form::label('user_profile[first_name]', trans('profile/front_lang._NOMBRE_USUARIO')) !!} <span class="text-danger">*</span>
                                {!! Form::text('user_profile[first_name]', null, array('placeholder' => trans('users/lang._INSERTAR_NOMBRE_USUARIO'), 'class' => 'form-control', 'id' => 'first_name')) !!}
                            </div>
                            <div class="col-lg-6 form-group">
                                {!! Form::label('user_profile[last_name]', trans('profile/front_lang._APELLIDOS_USUARIO')) !!} <span class="text-danger">*</span>
                                {!! Form::text('user_profile[last_name]', null, array('placeholder' => trans('users/lang._INSERTAR_APELLIDOS_USUARIO'), 'class' => 'form-control', 'id' => 'last_name')) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-12">
                                {!! Form::label('email', trans('profile/front_lang._EMAIL_USUARIO')) !!} <span class="text-danger">*</span>
                                {!! Form::text('email', null, array('placeholder' =>  trans('profile/front_lang._INSERTAR_EMAIL_USUARIO'), 'class' => 'form-control')) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                {!! Form::label('nif', trans('profile/front_lang.nif')) !!} <span class="text-danger">*</span>
                                {!! Form::text('nif', null, array('placeholder' => trans('profile/front_lang.nif'), 'class' => 'form-control', 'id' => 'nif')) !!}
                            </div>
                            <div class="col-lg-6 form-group">
                                {!! Form::label('centro', trans('profile/front_lang.centro_trabajo')) !!} <span class="text-danger">*</span>
                                {!! Form::text('centro', null, array('placeholder' => trans('profile/front_lang.centro_trabajo'), 'class' => 'form-control', 'id' => 'centro')) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                {!! Form::label('provincia', trans('profile/front_lang.provincia')) !!} <span class="text-danger">*</span>
                                <select class="form-control registro_select minimal" id="provincia" name="provincia"  to-update="municipio">
                                    <option  selected disabled value="">{{ trans('profile/front_lang.provincia') }}</option>
                                    @foreach($provincias as $key=>$value)
                                        <option value="{{ $value->id }}" @if($value->id==$user->provincia_id) selected @endif>{{  $value->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 form-group">
                                {!! Form::label('municipio', trans('profile/front_lang.municipio')) !!} <span class="text-danger">*</span>
                                <select class="form-control registro_select minimal" id="municipio" name="municipio">
                                    <option selected disabled value="">{{ trans('profile/front_lang.municipio') }}</option>
                                </select>
                            </div>
                        </div>

                        {{--    <div class="row">
                                <div class="col-lg-6 form-group">
                                    {!! Form::label('especialidad', trans('profile/front_lang.especialidad')) !!} <span class="text-danger">*</span>
                                    <select name="especialidad" id="especialidad" class="form-control" onchange="javascript:showorhide('especialidad');">
                                        <option selected disabled value="">{{ trans('profile/front_lang.especialidad') }}</option>
                                        @foreach($especialidades as $especialidad)
                                            <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-6 form-group" id="infoespecialidad" >
                                    {!! Form::label('especialidad_otra', trans('profile/front_lang.especialidad_otra')) !!}
                                    {!! Form::text('especialidad_otra', null, array(
                                        'placeholder' => trans('profile/front_lang.especialidad_otra'),
                                        'class' => 'form-control',
                                         'id' => 'especialidad_otra'))
                                     !!}
                                </div>
                            </div>
                            --}}

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                {!! Form::label('password', trans('profile/front_lang._CONTASENYA_USUARIO')) !!}
                                {!! Form::text('password', '', array('class' => 'form-control', 'id' => 'password', 'autocomplete'=>'off')) !!}
                                <div id="pswd_info">
                                    <h4>{{ trans('profile/front_lang._KEY_POSIBILITIES_INFO') }}</h4>
                                    <ul>
                                        <li id="letter" class="invalid">{{ trans('profile/front_lang._KEY_POSIBILITIES_001') }} <strong>{{ trans('profile/front_lang._KEY_POSIBILITIES_003') }}</strong></li>
                                        <li id="capital" class="invalid">{{ trans('profile/front_lang._KEY_POSIBILITIES_001') }} <strong>{{ trans('profile/front_lang._KEY_POSIBILITIES_004') }}</strong></li>
                                        <li id="number" class="invalid">{{ trans('profile/front_lang._KEY_POSIBILITIES_001') }} <strong>{{ trans('profile/front_lang._KEY_POSIBILITIES_005') }}</strong></li>
                                        <li id="length" class="invalid">{{ trans('profile/front_lang._KEY_POSIBILITIES_002') }}  <strong>{{ trans('profile/front_lang._KEY_POSIBILITIES_006') }}</strong></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-6 form-group">
                                {!! Form::label('password_confirmation', trans('profile/front_lang._REPETIR_CONTASENYA_USUARIO')) !!}
                                {!! Form::text('password_confirmation', '', array('class' => 'form-control', 'autocomplete'=>'off')) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-12">
                                <div class="checkbox icheck">
                                    <label>
                                        {!! Form::checkbox('user_profile[confirmed]',1,false) !!} {{ trans("profile/front_lang.confirmed") }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-12">
                                <div class="checkbox icheck">
                                    <label>
                                        {!! Form::checkbox('user_profile[consentimiento]',1,false) !!} {{ trans("profile/front_lang.consentimiento") }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-12">
                                <div id="recaptcha"></div>
                                <div id="message-error-captcha" style="color: #a94442; visibility:hidden;">El campo No soy un robot es obligatorio.</div>
                            </div>
                        </div>

                        <div class="row">
                            <button id="btn_submit" type="submit" class="btn background-color-secondary pt-sm pr-md pb-sm pl-md text-light pull-right">{{ trans('profile/front_lang.guardar') }}</button>
                        </div>

                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{ trans("profile/front_lang.informacion_codigo") }}</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("profile/front_lang.close") }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('foot_page')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl={{ config("app.locale") }}&onload=onloadCallback&render=explicit" async defer></script>

    <script>
        var pwd1 = true;
        var login = true;

        $(document).ready(function() {

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

                var g_response = grecaptcha.getResponse();
                if(g_response === ''){
                    var messageErrorCaptcha = document.getElementById('message-error-captcha')
                    messageErrorCaptcha.style.visibility='visible';
                    setTimeout(function(){
                        var messageErrorCaptcha = document.getElementById('message-error-captcha')
                        messageErrorCaptcha.style.visibility='hidden';
                    }, 3000);
                    return false;
                }

                if(!login) {
                    return false;
                }

                return true;

            });

            $("#provincia").change(function () {
                $_next_sibling = $("#" + $(this).attr("to-update"));
                field = $(this).attr("id");
                id = $(this).val();
                if (id.length) {
                    $.get("{{ url("usuarios/registro/") }}/" + field + "/" + id, function (data) {
                        $_next_sibling.html(data);
                    });
                }
            });                        


        });

        function showorhide(strIdDiv) {
            if($("#" + strIdDiv).val()!='8') {
                $("#info"+strIdDiv).fadeOut(500);
                $("#info" + strIdDiv).slideUp(500);
            } else {
                $("#info" + strIdDiv).fadeIn(500);
                $("#info" + strIdDiv).slideDown(500);
            }
        }

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

        @if(config("elearning.autentificacion.TIPO_REGISTRO")=='2')
        function validateCode() {
            var code = $("#codigo").val();

            if(code!='') {
                $("#btnSendCode").addClass("disabled");
                $("#btnSendCode").addClass("active");
                $.ajax({
                    url: "{{url('/usuarios/registro/exists/code')}}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN':"{{ csrf_token() }}"
                    },
                    data: {
                        code: code
                    },
                    success       : function ( data ) {
                        $("#btnSendCode").removeClass("disabled");
                        $("#btnSendCode").removeClass("active");
                        if(data!='NOK') {
                            $("#codigo_id").val(data.id+"|"+data.codigo);
                            $("#codeIntroductor").slideUp(500, function() {
                                $("#register_form").slideDown(500);
                            });
                        } else {
                            $("#myModal").find(".modal-body").html("{{ trans("profile/front_lang.codigo_incorrecto") }}");
                            $('#myModal').modal("show");
                        }

                    }
                });
            } else {
                $("#myModal").find(".modal-body").html("{{ trans("profile/front_lang.debe_introducir_codigo") }}");
                $('#myModal').modal("show");
            }


        }
        @endif



        var onloadCallback = function() {
            grecaptcha.render('recaptcha', {
                'sitekey' : '{!!  env("RECAPTCHA_HTML_KEY", '')  !!}'
            });
        };

    </script>

    {!! JsValidator::formRequest('Clavel\Elearning\Requests\UsersRequest')->selector('#formData') !!}

@stop
