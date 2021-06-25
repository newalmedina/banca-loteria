@extends('admin.layouts.simple')

@section('title')
    @parent {{ $page_title }}
@stop

@section ('content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('home') }}">
                <div v-html="message">{{ config("app.name") }}</div>
            </a>
        </div>

        <div class="login-box-body">
            <p class="login-box-msg">{{ trans('auth/lang.introduzca_email') }}</p>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form  method="POST" action="{{ route('admin.password.email') }}">
                {{ csrf_field() }}

                <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input id="email"  type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" required>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <button class="btn btn-primary btn-block btn-flat" type="submit">{{ trans("auth/lang.enivar_contrasena") }}</button>

                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <a class="btn btn-default btn-block btn-flat" href="{{ url('admin/login') }}">{{ trans("auth/lang.volver_login") }}</a>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

        </div>
        <!-- /.form-box -->
    </div>
    <!-- /.register-box -->

@stop