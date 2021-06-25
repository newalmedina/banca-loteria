@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@endsection

@section('content')

    <!-- Page Content -->
    <div class="container pt-lg pb-xlg">
        <div class="row">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
        </div>
        <form class="form-horizontal d-flex justify-content-center align-items-center pt-xlg" role="form" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
            <div class="light-rounded-box row p-lg">
                <div class="col-lg-12">
                    <div class="row d-flex align-items-center pb-lg">
                        <div class="svg_img_wrapper">
                            <img src="{{ asset("assets/front/img/key.svg") }}" alt="key_icon">
                        </div>
                        <h4>{{ trans("general/front_lang.login") }}</h4>
                    </div>
                    <div class="row">
                        @if (!$errors->isEmpty())
                            <div class="alert alert-danger">
                            <span class="text-danger align-middle">
                                <i class="fa fa-close"></i>
                                @foreach ($errors->all() as $error)
                                    {{ $error }}
                                @endforeach
                            </span>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="mb-none" for="username">{{ trans("users/lang.usuario") }}</label>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group pl-md pr-md {{ $errors->has('username') ? ' has-danger' : '' }}">
                                <input type="text" name="username" class="form-control" id="username"
                                       placeholder="{{ trans("users/lang.nombre_usuario") }}"  value="{{ old('username') }}" required autofocus>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="mb-none" for="password">{{ trans("users/lang._CONTASENYA_USUARIO") }}</label>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group pl-md pr-md {{ $errors->has('password') ? ' has-danger' : '' }}">
                                <input type="password" name="password" class="form-control" id="password"
                                       placeholder="{{ trans("users/lang._CONTASENYA_USUARIO") }}"  value="{{ old('password') }}" required autofocus>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="checkbox-custom checkbox-default pull-left">
                                {!! Form::checkbox('remember',1,false) !!}
                                <label for="remember">{{ trans("auth/lang.recordarme") }}</label>
                            </div>
                        </div>
                    </div>


                    <div class="login_submit_row mt-xlg">
                        <a class="recover_pass" href="{{ url('/password/reset') }}">{{ trans('auth/lang.recordar_contrasena') }}</a>
                        <button id="btn_submit" type="submit" class="btn background-color-secondary pt-sm pr-md pb-sm pl-md text-light pull-right">{{ trans("auth/lang.login_access") }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section("foot_page")

@stop
