@extends('admin.layouts.simple')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
@stop

@section ('content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('home') }}">
                <div v-html="message">{{ config("app.name") }}</div>
            </a>
        </div>

        <div class="register-box-body">
            <p class="login-box-msg">{{ trans("auth/lang.change_password_info") }}</p>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form  method="POST" action="{{ route('admin.password.request') }}">
                {{ csrf_field() }}

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input id="email"  type="email" class="form-control" placeholder="{{ trans('auth/lang.email') }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input id="password" type="password" class="form-control" placeholder="{{ trans('auth/lang.password') }}" name="password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback">
                    <input id="password-confirm" type="password" class="form-control" placeholder="{{ trans('auth/lang.repetir_password') }}" name="password_confirmation" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                    @if ($errors->has('password_confirmation'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="row">
                    <div class="col-xs-8">
                        <button class="btn btn-primary btn-block btn-flat" type="submit">{{ trans('auth/lang.cambiar_contrasena') }}</button>

                    </div>
                    <!-- /.col -->
                </div>
            </form>

        </div>
        <!-- /.form-box -->
    </div>
    <!-- /.register-box -->

@stop



@section('foot_page')
    <!-- iCheck -->
    <script src="{{ asset("/assets/admin/vendor/iCheck/js/icheck.min.js") }}"></script>

    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
@stop