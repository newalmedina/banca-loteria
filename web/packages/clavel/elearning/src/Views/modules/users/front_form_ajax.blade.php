@extends('front.layouts.minimal')

@section('content')

    @if(config("elearning.autentificacion.TIPO_REGISTRO")=='1')
        <p>{{ trans("auth/lang.signup_06") }}</p>
    @endif

    @if(config("elearning.autentificacion.TIPO_REGISTRO")=='2')

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

        <p>{{ trans("auth/lang.signup_05") }}</p>

        <div id="codeIntroductor" style="padding-bottom: 30px;">
            <div class="form-group text-left">
                {!! Form::label('codigo', trans('profile/front_lang.codigo')) !!} <span class="text-danger">*</span>
                {!! Form::text('codigo', null, array('placeholder' =>  trans('profile/front_lang.codigo'), 'class' => 'form-control', 'id'=>'codigo')) !!}
            </div>

            <div class="box-footer">

                <button id="btnSendCode" onclick="validateCode();" class="btn btn-default  text-uppercase background-color-secondary pt-sm pr-xlg pb-sm pl-xlg mt-xlg text-light has-spinner"><span class="spinner"><img src="{{ asset("assets/front/img/ajax_loader_vector.gif") }}" width="16" alt=""> </span> {{ trans('profile/front_lang.enviar') }}</button>

            </div>
        </div>

    @endif

    <div id="register_form" class='row' @if(config("elearning.autentificacion.TIPO_REGISTRO")=='2') style="display:none;" @endif>

        @include('front.includes.errors')

        <div class='col-md-12'>

            {!! Form::model($user, ['role' => 'form', 'id' => 'formData', 'method' => 'POST']) !!}
                {!! Form::hidden('codigo_id', null, array('id'=>'codigo_id')) !!}

                <div class="row">
                    <div class="col-lg-6 form-group text-left">
                        {!! Form::label('user_profile[first_name]', trans('profile/front_lang._NOMBRE_USUARIO')) !!} <span class="text-danger">*</span>
                        {!! Form::text('user_profile[first_name]', null, array('placeholder' => trans('users/lang._INSERTAR_NOMBRE_USUARIO'), 'class' => 'form-control', 'id' => 'first_name')) !!}
                    </div>

                    <div class="col-lg-6 form-group text-left">
                        {!! Form::label('user_profile[last_name]', trans('profile/front_lang._APELLIDOS_USUARIO')) !!} <span class="text-danger">*</span>
                        {!! Form::text('user_profile[last_name]', null, array('placeholder' => trans('users/lang._INSERTAR_APELLIDOS_USUARIO'), 'class' => 'form-control', 'id' => 'last_name')) !!}
                    </div>
                </div>

                <div class="form-group text-left">
                    {!! Form::label('email', trans('profile/front_lang._EMAIL_USUARIO')) !!} <span class="text-danger">*</span>
                    {!! Form::text('email', null, array('placeholder' =>  trans('profile/front_lang._INSERTAR_EMAIL_USUARIO'), 'class' => 'form-control')) !!}
                </div>

                <div class="row">
                    <div class="col-lg-6 form-group text-left">
                        {!! Form::label('password', trans('profile/front_lang._CONTASENYA_USUARIO')) !!}
                        {!! Form::text('password', '', array('class' => 'form-control', 'id' => 'password_reg', 'autocomplete'=>'off')) !!}
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
                    <div class="col-lg-6 form-group text-left">
                        {!! Form::label('password_confirmation', trans('profile/front_lang._REPETIR_CONTASENYA_USUARIO')) !!}
                        {!! Form::text('password_confirmation', '', array('class' => 'form-control', 'autocomplete'=>'off')) !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox icheck text-left">
                        <label>
                            {!! Form::checkbox('user_profile[confirmed]',1,false) !!} {{ trans("profile/front_lang.confirmed") }}
                        </label>
                    </div>
                </div>

                <div class="box-footer">
                    <button id="btn_submit" type="submit" class="btn btn-default text-uppercase background-color-secondary pt-sm pr-xlg pb-sm pl-xlg mt-xlg text-light">{{ trans('profile/front_lang.guardar') }}</button>
                </div>

            {!! Form::close() !!}

        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('foot_page')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        var pwd1 = true;
        var login = true;

        $(document).ready(function() {

            if($("#password_reg").val()!='') pwd1  = checkform($("#password_reg").val());

            $('#password_reg').keyup(function() {

                pwd1 = checkform($(this).val());

            }).blur(function() {

                $('#pswd_info').fadeOut(500);

            });

            $("#formData").submit(function( event ) {

                if(!pwd1 && ($("#password_reg").val()!='')) {
                    return checkform($("#password_reg").val());
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
                                $("#codigo_id").val(data);
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
    </script>

    {!! JsValidator::formRequest('Clavel\Elearning\Requests\UsersRequest')->selector('#formData') !!}
@stop
