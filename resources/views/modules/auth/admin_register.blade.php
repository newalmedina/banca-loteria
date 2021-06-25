@extends('admin.layouts.simple')

@section('title')
    @parent "Log in"
@stop

@section('head_page')
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset("/assets/admin/vendor/iCheck/css/square/blue.css") }} ">

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    @stop

@section ('content')
    <div class="register-box">
        <div class="register-logo">
            <a href="../../index2.html"><strong>Admin</strong>LTE</a>
        </div>

        <div class="register-box-body">
            <p class="login-box-msg">Register a new membership</p>

            <form  method="POST" action="{{ url('/admin/register') }}">
                {{ csrf_field() }}

                <div class="form-group has-feedback{{ $errors->has('name') ? ' has-error' : '' }}">
                    <input id="username" type="text" class="form-control" placeholder="User name" name="username" value="{{ old('username') }}" required autofocus>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    @if ($errors->has('username'))
                        <span class="help-block">
                            <strong>{{ $errors->first('username') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input id="email" type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" required>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if ($errors->has('email'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input id="password" type="password" class="form-control" placeholder="Password" name="password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                    @if ($errors->has('password'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="form-group has-feedback">
                    <input id="password-confirm" type="password" class="form-control" placeholder="Retype password" name="password_confirmation" required>
                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox"> I agree to the <a href="#">terms</a>
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <hr />
            <a href="{{ route('admin.login') }}" class="text-center">I already have a membership</a>
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
