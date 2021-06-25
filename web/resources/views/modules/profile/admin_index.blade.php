@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
<style>
    .btn-toolbar .btn{
        margin-right: 40px;
    }
    .btn-toolbar .btn:last-child{
        margin-right: 0;
    }

    #fileOutput {
        min-height: 150px;
        border: dashed 2px #C0C0C0;
        background-color: #F4F4F4;
        line-height: 150px;
        text-align: center;
    }
</style>
@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class='row'>

        <div class='col-md-2 text-center'>
            <div id="fileOutput">
                @if(!empty($user->userProfile->photo))
                    <img  id='image_ouptup' src="{{ url('admin/profile/getphoto/'.$user->userProfile->photo) }}" class="img-circle" style="width: 80%; margin: auto;" alt="User Image"/>
                @else
                    <i class="fa fa-camera" aria-hidden="true"></i> {{ trans("profile/admin_lang.sin_foto") }}
                @endif
            </div>
            <p>
            <h3>{{ $user->userProfile->first_name }} {{ $user->userProfile->last_name }}</h3>
            {{ trans('profile/admin_lang.miembro_desde') }} {{ $user->CreatedAtFormatted }}
            </p>
        </div><!-- /.col -->

        <div class='col-md-10'>
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs pull-right">
                    <li class="active">
                        <a data-toggle="tab" href="#tab_2-2">{{ trans('profile/admin_lang.avatar') }}</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#tab_3-3">{{ trans('profile/admin_lang.social_title') }}</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#tab_1-1">{{ trans('profile/admin_lang.info_perfil') }}</a>
                    </li>
                    <li class="pull-left header">{{ trans('profile/admin_lang.perfil_usuario') }}</li>
                </ul>
                <div class="tab-content">

                    <div id="tab_1-1" class="tab-pane">
                        {!! Form::model($user, ['role' => 'form', 'id' => 'formData', 'method' => 'POST']) !!}

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                {!! Form::label('userProfile[first_name]', trans('profile/admin_lang._NOMBRE_USUARIO')) !!}
                                {!! Form::text('userProfile[first_name]', null, array('placeholder' => trans('users/lang._INSERTAR_NOMBRE_USUARIO'), 'class' => 'form-control', 'id' => 'first_name')) !!}
                            </div>
                            <div class="col-lg-6 form-group">
                                {!! Form::label('userProfile[last_name]', trans('profile/admin_lang._APELLIDOS_USUARIO')) !!}
                                {!! Form::text('userProfile[last_name]', null, array('placeholder' => trans('users/lang._INSERTAR_APELLIDOS_USUARIO'), 'class' => 'form-control', 'id' => 'last_name')) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                {!! Form::label('email', Lang::get('profile/admin_lang._EMAIL_USUARIO')) !!}
                                {!! Form::text('email', null, array('placeholder' =>  Lang::get('profile/admin_lang._INSERTAR_EMAIL_USUARIO'), 'class' => 'form-control')) !!}
                            </div>
                            <div class="col-lg-6 form-group">
                                {!! Form::label('userProfile[gender]', Lang::get('profile/admin_lang._genero_sexusal')) !!}
                                <div class="radio-list">
                                    <div class="rdio rdio-primary radio-inline">
                                        <input id="male" name="userProfile[gender]" type="radio" name="radio"
                                               value="male" checked="checked" required/>
                                        {!! trans('profile/admin_lang.hombre') !!}
                                    </div>
                                    <div class="rdio rdio-primary radio-inline">
                                        <input id="female" name="userProfile[gender]" type="radio" name="radio"
                                               value="female"
                                               @if ($user->userProfile->gender == 'female') checked="checked" @endif />
                                        {!! trans('profile/admin_lang.mujer') !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-6 form-group">
                                {!! Form::label('userProfile[user_lang]', Lang::get('profile/front_lang._USER_LANG')) !!}
                                <span class="text-danger">*</span>
                                <select name="userProfile[user_lang]" class="form-control">
                                    @foreach(\App\Models\Idioma::where("active","=","1")->get() as $key=>$value)
                                        <option value="{{ $value->code }}"
                                                @if($value->code==$user->userProfile->user_lang) selected @endif>{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <br clear="all">
                        <p class="lead">{{ trans('profile/admin_lang.accesos_web') }}</p>

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                {!! Form::label('username', trans('profile/admin_lang.usuario')) !!}
                                {!! Form::text('username', null, array('placeholder' => trans('profile/admin_lang._INSERTAR_USUSARIO_USUARIO'), 'class' => 'form-control', 'required' => 'required')) !!}
                                <div id="login_info">
                                    <h4 style="color: #ec3f41; font-weight: bold;">{{ trans('profile/admin_lang._NOTCORRECTUSERLOGIN') }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                {!! Form::label('password', trans('profile/admin_lang._CONTASENYA_USUARIO')) !!}
                                {!! Form::text('password', '', array('class' => 'form-control', 'id' => 'password', 'autocomplete'=>'off')) !!}
                                <div id="pswd_info">
                                    <h4>{{ trans('profile/admin_lang._KEY_POSIBILITIES_INFO') }}</h4>
                                    <ul>
                                        <li id="letter"
                                            class="invalid">{{ trans('profile/admin_lang._KEY_POSIBILITIES_001') }}
                                            <strong>{{ trans('profile/admin_lang._KEY_POSIBILITIES_003') }}</strong>
                                        </li>
                                        <li id="capital"
                                            class="invalid">{{ trans('profile/admin_lang._KEY_POSIBILITIES_001') }}
                                            <strong>{{ trans('profile/admin_lang._KEY_POSIBILITIES_004') }}</strong>
                                        </li>
                                        <li id="number"
                                            class="invalid">{{ trans('profile/admin_lang._KEY_POSIBILITIES_001') }}
                                            <strong>{{ trans('profile/admin_lang._KEY_POSIBILITIES_005') }}</strong>
                                        </li>
                                        <li id="length"
                                            class="invalid">{{ trans('profile/admin_lang._KEY_POSIBILITIES_002') }}
                                            <strong>{{ trans('profile/admin_lang._KEY_POSIBILITIES_006') }}</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-6 form-group margin">
                                {!! Form::label('password_confirmation', trans('profile/admin_lang._REPETIR_CONTASENYA_USUARIO')) !!}
                                {!! Form::text('password_confirmation', '', array('class' => 'form-control', 'autocomplete'=>'off')) !!}
                            </div>
                        </div>

                        <br clear="all">

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">{{ trans('profile/admin_lang.guardar') }}</button>
                        </div>

                        {!! Form::close() !!}

                    </div><!-- /.tab-pane -->

                    <div id="tab_2-2" class="tab-pane active">
                        {!! Form::open(array('role' => 'form','id'=>'formAvatar', 'method'=>'POST', 'files'=>true)) !!}
                        {!! Form::hidden('delete_photo', 0, array('id' => 'delete_photo')) !!}

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                {!! Form::label('profile_image', Lang::get('profile/admin_lang._USER_PHOTO')) !!}
                                <div class="input-group">
                                    <input type="text" class="form-control" id="nombrefichero" readonly>
                                    <span class="input-group-btn">
                                        <div class="btn btn-primary btn-file">
                                            {{ trans('profile/front_lang.search_logo') }}
                                            {!! Form::file('profile_image[]',array('id'=>'profile_image', 'multiple'=>true)) !!}
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 form-group">
                                <div class="btn-toolbar">
                                    <button onclick="updloadAvatar();"  class="btn btn-primary">{{ trans('profile/admin_lang.guardar') }}</button>
                                    <a id="remove" href="#" class="btn btn-danger" style="@if($user->userProfile->photo=='') display: none; @endif cursor: pointer; text-align: center;">
                                        {{ trans("profile/front_lang.quitar_foto") }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}

                    </div><!-- /.tab-pane -->
                    <div id="tab_3-3" class="tab-pane">
                        {!! Form::model($user,  array('route' => array('profile.social.update', $user->id), 'method' => 'PATCH',
                                                    'id' => 'formData', 'class' => 'form-horizontal') ) !!}


                        {!! Form::hidden('id', $user->id, array('id' => 'id')) !!}

                        <div class="row">
                            <div class="col-lg-12 form-group">
                                {!! Form::label('userProfile[facebook]', Lang::get('profile/admin_lang.facebook'), array('class' => 'col-md-2 control-label')) !!}
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-facebook" aria-hidden="true"></i>
                                        </span>
                                        {!! Form::text('userProfile[facebook]', null, array('placeholder' =>  Lang::get('profile/admin_lang.facebook'), 'class' => 'form-control', 'id' => 'facebook')) !!}
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-12 form-group">
                                {!! Form::label('userProfile[twitter]', Lang::get('profile/admin_lang.twitter'), array('class' => 'col-md-2 control-label')) !!}
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-twitter" aria-hidden="true"></i>
                                        </span>
                                        {!! Form::text('userProfile[twitter]', null, array('placeholder' =>  Lang::get('profile/admin_lang.twitter'), 'class' => 'form-control', 'id' => 'twitter')) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 form-group">
                                {!! Form::label('userProfile[linkedin]', Lang::get('profile/admin_lang.linkedin'), array('class' => 'col-md-2 control-label')) !!}
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-linkedin" aria-hidden="true"></i>
                                        </span>
                                        {!! Form::text('userProfile[linkedin]', null, array('placeholder' =>  Lang::get('profile/admin_lang.linkedin'), 'class' => 'form-control', 'id' => 'linkedin')) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 form-group">
                                {!! Form::label('userProfile[youtube]', Lang::get('profile/admin_lang.youtube'), array('class' => 'col-md-2 control-label')) !!}
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-youtube"></i>
                                        </span>
                                        {!! Form::text('userProfile[youtube]', null, array('placeholder' =>  Lang::get('profile/admin_lang.youtube'), 'class' => 'form-control', 'id' => 'youtube')) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 form-group">
                                {!! Form::label('userProfile[bio]', trans('profile/admin_lang.bio'), array('class' => 'col-md-2 control-label')) !!}
                                <div class="col-md-10">
                                    {!! Form::textarea('userProfile[bio]', null, array('placeholder' =>  Lang::get('profile/admin_lang.bio'), 'class' => 'form-control textarea', 'id' => 'bio')) !!}
                                </div>
                            </div>

                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">{{ trans('profile/admin_lang.guardar') }}</button>
                        </div>

                        {!! Form::close() !!}
                    </div>

                </div><!-- /.tab-content -->
            </div>
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        var pwd1 = true;
        var login = true;

        $(document).ready(function () {
            $("#username").change(function () {

                $.ajax({
                    url: "{{url('/admin/profile/exists/login')}}",
                    type: "POST",
                    headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                    data: {
                        user_id: '{{ $user->id }}',
                        login: $("#username").val()
                    },
                    success: function (data) {
                        if (data == 'NOK') {
                            login = false;
                            $('#login_info').fadeIn(500);
                        } else {
                            login = true;
                            $('#login_info').fadeOut(500);
                        }
                    }
                });

            });

            if ($("#password").val() != '') pwd1 = checkform($("#password").val());

            $('#password').keyup(function () {

                pwd1 = checkform($(this).val());

            }).blur(function () {

                $('#pswd_info').fadeOut(500);

            });

            $("#formData").submit(function (event) {

                if (!pwd1 && ($("#password").val() != '')) {
                    return checkform($("#password").val());
                }

                if (!login) {
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
            var pswdlength = false;
            var pswdletter = false;
            var pswduppercase = false;
            var pswdnumber = false;

            if (pswd.length >= 7) {
                $('#length').removeClass('invalid').addClass('valid');
                pswdlength = true;
            } else {
                $('#length').removeClass('valid').addClass('invalid');
            }

            if (pswd.match(/[A-z]/)) {
                $('#letter').removeClass('invalid').addClass('valid');
                pswdletter = true;
            } else {
                $('#letter').removeClass('valid').addClass('invalid');
            }

            if (pswd.match(/[A-Z]/)) {
                $('#capital').removeClass('invalid').addClass('valid');
                pswduppercase = true;
            } else {
                $('#capital').removeClass('valid').addClass('invalid');
            }

            if (pswd.match(/\d/)) {
                $('#number').removeClass('invalid').addClass('valid');
                pswdnumber = true;
            } else {
                $('#number').removeClass('valid').addClass('invalid');
            }

            if (pswdlength && pswdletter && pswduppercase && pswdnumber) {
                $('#pswd_info').fadeOut(500);
                return true;
            } else {
                $('#pswd_info').fadeIn(500);
                return false;
            }
        }

        function updloadAvatar() {
            $("#formAvatar").attr("action", "{!! url("admin/profile/photo") !!}");
            $("#formAvatar").submit();
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
