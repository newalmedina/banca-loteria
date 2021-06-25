@extends('admin.layouts.simple')

@section('title')
    @parent "Log in"
@stop

@section('head_page')
    <link rel="stylesheet" href="{{ asset("/assets/admin/vendor/iCheck/css/square/blue.css") }} ">
@stop

@section ('content')

<div id="app" class="login-box">
    <div class="login-logo">
        <a href="{{ route('home') }}">
            <div>{{ config("app.name") }}</div>
        </a>
    </div>

    <div class="login-box-body">
        <p class="login-box-msg">{{ trans("auth/lang.sign_in") }}</p>

        <form method="post" method="POST" action="{{ route('admin.login') }}">
            {{ csrf_field() }}
            <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                <input type="text" class="form-control" placeholder="{{ trans("auth/lang.username") }}" name="username" value="{{ old('username') }}" required autofocus >
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                @if ($errors->has('username'))
                    <span class="help-block">
                        <strong>{{ $errors->first('username') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" type="password" class="form-control" placeholder="{{ trans("auth/lang.password") }}" name="password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ trans("auth/lang.recordarme") }}
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('auth/lang.login_access') }}</button>
                </div>
            </div>
        </form>

        <hr />

        @if (Route::has('admin.password.request'))
            <p class="margin-botton-md">
                <a href="{{ route('admin.password.request') }}">{{ trans('auth/lang.recordar_contrasena') }}</a>
            </p>

        @endif
        @if (Route::has('admin.register'))
            <p class="margin-botton-md">
                <a href="{{ route('admin.register') }}">{{ __('auth/lang.registrarse_en_la_web') }}</a>
            </p>
        @endif



    </div>
    <div class="login-footer">
        <a href="#" @click="changeColor"><strong>{{ trans("general/admin_lang.version") }}</strong> @{{ message }}</a>
        <p v-show="activeLove" class="love">Made with <i class="fa fa-heart" :class="classColor" aria-hidden="true"></i> by Aduxia</p>
    </div>

</div>


@stop

@section('foot_page')
    <!-- iCheck -->
    <script src="{{ asset("/assets/admin/vendor/iCheck/js/icheck.min.js") }}"></script>
    <script src="{{ asset("/assets/admin/vendor/vue/vue.min.js") }}"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });

        var app = new Vue({
            el: '#app',
            data: {
                message: '{{ config("general.app_version") }}',
                classColor: '',
                activeLove: false
            },
            methods:{
                changeColor: function () {

                    var v = this;

                    v.activeLove = !v.activeLove;

                    setTimeout(function recursiveColor () {
                        if(v.classColor == '') {
                            v.classColor = 'alternate';
                        } else {
                            v.classColor = '';
                        }
                        if(v.activeLove) {
                            setTimeout(recursiveColor, 1000);
                        }

                    }, 1000);
                }
            },
            mounted () {
                console.log('Vue mounted')
            }
        })
    </script>
@stop
