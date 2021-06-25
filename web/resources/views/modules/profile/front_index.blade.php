@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
@stop

@section('content')

    <div class="container">
        <h3 class="section-title font-alt mt0">{{ trans('profile/front_lang.perfil_usuario') }}</h3>
        <p class="form-control-static">{{ trans('profile/front_lang.perfil_usuario_desc') }}</p>
        <br clear="all">

        @include('front.includes.errors')
        @include('front.includes.success')

        <div class='row'>

            <div class="col-md-2">
                <div id="fileOutput">
                    @if($user->userProfile->photo!='')
                        <img src='{{ url('profile/getphoto/'.$user->userProfile->photo) }}' id='image_ouptup' width='100%' alt="">
                    @else
                        <i class="fa fa-camera" aria-hidden="true"></i> {{ trans("profile/front_lang.sin_foto") }}
                    @endif
                </div>
                <div id="remove" class="text-danger" style="@if($user->userProfile->photo=='') display: none; @endif cursor: pointer; text-align: center;"><i class="fa fa-times" aria-hidden="true"></i> {{ trans("profile/front_lang.quitar_foto") }}</div>
            </div>
            <div class='col-md-10'>

                {!! Form::model($user, ['role' => 'form', 'id' => 'formData', 'method' => 'POST', 'files'=>true]) !!}
                {!! Form::hidden('delete_photo', 0, array('id' => 'delete_photo')) !!}
                <div class="row">
                    <div class="col-lg-6 form-group">
                        {!! Form::label('userProfile[first_name]', trans('profile/front_lang._NOMBRE_USUARIO')) !!} <span class="text-danger">*</span>
                        {!! Form::text('userProfile[first_name]', null, array('placeholder' => trans('users/lang._INSERTAR_NOMBRE_USUARIO'), 'class' => 'form-control', 'id' => 'first_name')) !!}
                    </div>
                    <div class="col-lg-6 form-group">
                        {!! Form::label('userProfile[last_name]', trans('profile/front_lang._APELLIDOS_USUARIO')) !!} <span class="text-danger">*</span>
                        {!! Form::text('userProfile[last_name]', null, array('placeholder' => trans('users/lang._INSERTAR_APELLIDOS_USUARIO'), 'class' => 'form-control', 'id' => 'last_name')) !!}
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 form-group">
                        {!! Form::label('email', Lang::get('profile/front_lang._EMAIL_USUARIO')) !!} <span class="text-danger">*</span>
                        {!! Form::text('email', null, array('placeholder' =>  Lang::get('profile/front_lang._INSERTAR_EMAIL_USUARIO'), 'class' => 'form-control')) !!}
                    </div>
                    {{--
                    <div class="col-lg-6 form-group">
                        {!! Form::label('userProfile[gender]', Lang::get('profile/front_lang._genero_sexusal')) !!} <span class="text-danger">*</span>
                        <div class="radio-list">
                            <div class="rdio rdio-primary radio-inline">
                                <input id="male" name="userProfile[gender]" type="radio" name="radio" value="male" checked="checked" required />
                                {!! trans('profile/front_lang.hombre') !!}
                            </div>
                            <div class="rdio rdio-primary radio-inline">
                                <input id="female" name="userProfile[gender]" type="radio" name="radio" value="female" @if ($user->userProfile->gender == 'female') checked="checked" @endif />
                                {!! trans('profile/front_lang.mujer') !!}
                            </div>
                        </div>
                    </div>
                    --}}
                </div>

                <div class="row">
                    <div class="col-lg-6 form-group">
                        {!! Form::label('userProfile[user_lang]', Lang::get('profile/front_lang._USER_LANG')) !!} <span class="text-danger">*</span>
                        <select name="userProfile[user_lang]" class="form-control">
                            @foreach(\App\Models\Idioma::where("active","=","1")->get() as $key=>$value)
                                <option value="{{ $value->code }}" @if($value->code==$user->userProfile->user_lang) selected @endif>{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 form-group">
                        {!! Form::label('profile_image', Lang::get('profile/front_lang._USER_PHOTO')) !!}
                        <div class="input-group">
                            <input type="text" class="form-control" id="nombrefichero" readonly>
                            <span class="input-group-btn">
                                <div class="btn btn-primary btn-file">
                                    {{ trans('profile/front_lang.search_logo') }}
                                    {!! Form::file('profile_image[]',array('id'=>'profile_image', 'multiple'=>false)) !!}
                                </div>
                            </span>
                        </div>

                    </div>
                </div>

                <br clear="all">
                <h4 class="section-title font-alt mt0">{{ trans('profile/front_lang.accesos_web') }}</h4>

                <div class="row">
                    <div class="col-lg-6 form-group">
                        {!! Form::label('username', trans('profile/front_lang.usuario')) !!} <span class="text-danger">*</span>
                        {!! Form::text('username', null, array('placeholder' => trans('profile/front_lang._INSERTAR_USUSARIO_USUARIO'), 'class' => 'form-control', 'required' => 'required')) !!}
                        <div id="login_info">
                            <h4 style="color: #ec3f41; font-weight: bold;">{{ trans('profile/front_lang._NOTCORRECTUSERLOGIN') }}</h4>
                        </div>
                    </div>
                </div>

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
                <br clear="all">

                <div class="box-footer">
                    <button type="submit" class="btn btn-info">{{ trans('profile/front_lang.guardar') }}</button>
                </div>
                {!! Form::close() !!}
            </div><!-- /.col -->
        </div><!-- /.row -->

    </div>
    <!-- /.container -->
    <br><br><br><br><br><br><br>

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        var pwd1 = true;
        var login = true;

        $(document).ready(function() {
            $("#username").change(function() {

                $.ajax({
                    url: "{{url('/profile/exists/login')}}",
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

            $("#profile_image").change(function(){
                getFileName();
                readURL(this);
            });

            $("#remove").click(function() {
                $('#nombrefichero').val('');
                $('#profile_image').val("");
                $('#fileOutput').html('<i class="fa fa-camera" aria-hidden="true"></i> {{ trans("profile/front_lang.sin_foto") }}');
                $("#remove").css("display","none");
                $("#delete_photo").val('1');
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

        function getFileName() {
            $('#nombrefichero').val($('#profile_image')[0].files[0].name);
            $("#delete_photo").val('1');
        }

        function readURL(input) {


            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#fileOutput').html("<img src='' id='image_ouptup' width='100%' alt=''>");
                    $("#remove").css("display","block");
                    $('#image_ouptup').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    {!! JsValidator::formRequest('App\Http\Requests\FrontProfileRequest')->selector('#formData') !!}
@stop
