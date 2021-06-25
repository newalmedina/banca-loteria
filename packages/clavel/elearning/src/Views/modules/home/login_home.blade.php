<div class="container login_container">
    <div class="row landing_row">

        @if(!empty(Auth::user()->id))
            <div class="col-md-5 cols-xs-12 txt-buton">
                <a class="btn btn-primary txt-buton-1" href="{{ $url }}">
                <strong>
                    {!! trans("elearning::general/front_lang.acceder") !!}
                </strong>
                </a>
            </div>

        @else
            <div class="col-md-5 cols-xs-12">
                <div class="tabs">
                    <div class="tab-content">
                        <div id="sign_in" class="tab-pane active">
                            <div class="newsletter p-xlg mb-none" style="border: none;">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="svg_img_wrapper pb-sm">
                                            <img src="{{ asset("assets/front/img/inicio/icon-lock.svg") }}" alt="login_icon">
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <h4 class="mt-xs mb-xs">{{trans('auth/lang.bienvenida') }}</h4>
                                    </div>
                                </div>
                                @include('front.includes.errors')
                                {!! Form::open($form_data, ['role' => 'form', 'class' => 'login-form text-black pt-sm']) !!}
                                <div class="row p-sm">
                                    <label class="login_box_label" for="username">{{ trans("auth/lang.username") }}</label>
                                    {!! Form::text('username', null, [
                                        'placeholder' => trans('auth/lang.username'),
                                        'class' => 'form-control'
                                    ]) !!}
                                </div>
                                <div class="row p-sm">
                                    <label class="login_box_label" for="password">{{ trans("auth/lang.password") }}</label>
                                    {!! Form::password('password', [
                                    'placeholder' => trans('auth/lang.password'),
                                    'class' => 'form-control'
                                ]) !!}
                                </div>
                                <div class="checkbox-custom checkbox-default pull-left">
                                    {!! Form::checkbox('remember',1,false) !!}
                                    <label for="remember">{{ trans("auth/lang.recordarme") }}</label>
                                </div>
                                <br clear="all">
                                <div class="login_submit_row mt-xlg">
                                    <a class="recover_pass" href="{{ url('/password/reset') }}">{{ trans('auth/lang.recordar_contrasena') }}</a>
                                    <button id="btn_submit" type="submit" class="btn btn-primary text-uppercase background-color-secondary pt-md pr-lg pb-md pl-lg text-light">{{ trans('auth/lang.login_access') }}</button>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <a id="go_to_registro" href="{{ url('usuarios/registro') }}">
                        {{ trans("auth/lang.texto_registro") }}
                    </a>
                </div>
            </div>
        @endif

        <div class="e-landing_title col-md-7 cols-xs-12">
            <img class="pull-right" src="{{ asset("assets/front/img/inicio/titulo.svg") }}" alt="{{ env('APP_NAME') }}">
        </div>

    </div>
</div>
