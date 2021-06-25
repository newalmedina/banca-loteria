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

    <!-- Page Content -->
    <div class="container pt-lg pb-xlg">
        @include('front.includes.errors')
        @include('front.includes.success')
        @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif
        <form id='formData' class="form-horizontal d-flex justify-content-center align-items-center pt-xlg"
            role="form" method="POST" action="{{ route('password.request') }}">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="light-rounded-box row p-lg">
                <div class="col-md-12">
                    <div class="row d-flex align-items-center pb-lg">
                        <div class="svg_img_wrapper">
                            <img src="{{ asset("assets/front/img/key.svg") }}" alt="key_icon">
                        </div>
                        <h4>{{ trans("general/front_lang.restablecer") }}</h4>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <label class="mb-none" for="email">{{trans("general/front_lang.email") }}</label>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group pl-md pr-md">
                                <div class="form-group pl-md pr-md mb-2 mr-sm-2 mb-sm-0">
                                    <input type="text" name="email" class="form-control" id="email"
                                           placeholder="{{ trans("general/front_lang.email") }}"  value="{{ old('email') }}" autofocus>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <label class="mb-none" for="password">{{ trans("general/front_lang.password") }}</label>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group pl-md pr-md">
                                <div class="form-group pl-md pr-md mb-2 mr-sm-2 mb-sm-0">
                                    <input type="password" name="password" class="form-control" id="password"
                                           placeholder="{{ trans("general/front_lang.password") }}" >
                                        <div id="pswd_info">
                                            <h4>{{ trans('auth/lang._KEY_POSIBILITIES_INFO') }}</h4>
                                            <ul>
                                                <li id="letter" class="invalid">
                                                    {{ trans('auth/lang._KEY_POSIBILITIES_001') }}
                                                    <strong>{{ trans('auth/lang._KEY_POSIBILITIES_003') }}</strong>
                                                </li>
                                                <li id="capital" class="invalid">
                                                    {{ trans('auth/lang._KEY_POSIBILITIES_001') }}
                                                    <strong>{{ trans('auth/lang._KEY_POSIBILITIES_004') }}</strong>
                                                </li>
                                                <li id="number" class="invalid">
                                                    {{ trans('auth/lang._KEY_POSIBILITIES_001') }}
                                                    <strong>{{ trans('auth/lang._KEY_POSIBILITIES_005') }}</strong>
                                                </li>
                                                <li id="length" class="invalid">
                                                    {{ trans('auth/lang._KEY_POSIBILITIES_002') }}
                                                    <strong>{{ trans('auth/lang._KEY_POSIBILITIES_006') }}</strong>
                                                </li>
                                            </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="mb-none" for="password">{{ trans("general/front_lang.rep_password") }}</label>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group pl-md pr-md">
                                <div class="form-group pl-md pr-md mb-2 mr-sm-2 mb-sm-0">
                                    <input type="password" name="password_confirmation" class="form-control"
                                           id="password-confirm" placeholder="{{ trans("general/front_lang.rep_password") }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                            <button  id="btn_submit" type="submit" class="btn btn-primary text-uppercase background-color-secondary pt-md pr-lg pb-md pl-lg text-light pull-right">
                                <i class="fa fa-refresh"></i>
                                {{ trans("general/front_lang.restablecer") }}
                            </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section("foot_page")
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>


<script>
    var pwd1 = true;

    $(document).ready(function() {

        var pwd = $("#formData").find('input[name="password"]');

        if (pwd.val() != '') pwd1 = checkform(pwd.val());

        pwd.keyup(function () {

            pwd1 = checkform($(this).val());

        }).blur(function () {

            $('#pswd_info').fadeOut(500);

        });

        $("#formData").submit(function (event) {
            if(! $(this).valid()) return false;

            var pwd = $("#formData").find('input[name="password"]');

            if (!pwd1 && (pwd.val() != '')) {
                return checkform(pwd);
            }

            return true;
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


</script>

{!! JsValidator::formRequest('App\Http\Requests\FrontResetPasswordRequest')->selector('#formData') !!}

@stop
