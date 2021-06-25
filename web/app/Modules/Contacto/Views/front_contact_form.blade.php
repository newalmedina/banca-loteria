@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li><a href="{{ url("/contactus") }}">{{ $page_title }}</a></li>
@stop

@section('content')

    @if(config("general.GMAPS_URL")!='')
        <div class="container bs-docs-container">
            <iframe title="mapa de localización" src="{{ config("general.GMAPS_URL") }}" style="border:0;" allowfullscreen="" width="100%" height="350" frameborder="0"></iframe>
        </div>
    @endif

    <div class="container" style="padding-top: 35px;">
        @include('front.includes.errors')
        @include('front.includes.success')

        <div class="row contatcus">
            <div class="col-md-8">
                <h3>{{ $page_title }}</h3>
                <p>{{ trans("Contacto::front_lang.all_fields_obligatory") }}</p>

                {!! Form::model($user, $form_data, array('role' => 'form')) !!}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-feedback">
                                {!! Form::label('fullname', trans('Contacto::front_lang.fullname'), array('class' => 'control-label', 'readonly' => true)) !!}
                                {!! Form::text('fullname', (isset($user->user_profile->fullname)) ? $user->user_profile->fullname : null, array('placeholder' => trans('Contacto::front_lang._INSERTAR_fullname'), 'class' => 'form-control', 'id' => 'fullname')) !!}

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('email', trans('Contacto::front_lang.email'), array('class' => 'control-label', 'readonly' => true)) !!}
                                {!! Form::text('email', null, array('placeholder' => trans('Contacto::front_lang._INSERTAR_email'), 'class' => 'form-control', 'id' => 'email')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="display: none;">
                        <label for="faxonly">Fax Only
                            <input type="checkbox" name="faxonly" id="faxonly" />
                        </label>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('message', trans('Contacto::front_lang.message'), array('class' => 'control-label', 'readonly' => true)) !!}
                                {!! Form::textarea('message', null, array('class' => 'form-control', 'style' => 'resize:none; height:150px;', 'id' => 'message')) !!}
                            </div>

                            <div id="recaptcha"></div>
                            <div id="message-error-captcha" style="color: #a94442; visibility:hidden;">El campo No soy un robot es obligatorio.</div>
                            <br clear="all">

                            <input type="submit" name="submit" id="submit" value="{{ trans("Contacto::front_lang.submit") }}" class="btn btn-primary">
                        </div>
                    </div>


                {!! Form::close() !!}
            </div>

            <div class="col-md-4">
                <h3>{{ trans("Contacto::front_lang.contacto_info_01") }}</h3>

                <p>{{ trans("Contacto::front_lang.contacto_info_02") }}</p>

                <hr>

               <p><i class="glyphicon glyphicon-earphone img-circle-icon" aria-hidden="true"></i> <strong>{{ trans("Contacto::front_lang.signup_02") }}:</strong> (+xx) xx xxx xx xx</p>
               <p><i class="glyphicon glyphicon-envelope img-circle-icon" aria-hidden="true"></i> <strong>{{ trans("Contacto::front_lang.signup_03") }}:</strong> <a href="mailto:info@aduxia.com">info@aduxia.com</a></p>
            </div>
        </div>
        <br clear="all">
    </div>
    <br clear="all">

@endsection

@section('foot_page')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl={{ config("app.locale") }}&onload=onloadCallback&render=explicit" async defer></script>

    <script>
        $(document).ready(function() {
            $("#contact-form").submit(function( event ) {
                if(! $(this).valid()) return false;

                var g_response = grecaptcha.getResponse();
                if(g_response === ''){
                    var messageErrorCaptcha = document.getElementById('message-error-captcha')
                    messageErrorCaptcha.style.visibility='visible';
                    setTimeout(function(){
                        var messageErrorCaptcha = document.getElementById('message-error-captcha')
                        messageErrorCaptcha.style.visibility='hidden';
                    }, 3000);
                    return false;
                }
                return true;
            });
        });

        var onloadCallback = function() {
            grecaptcha.render('recaptcha', {
                'sitekey' : '{!!  env("RECAPTCHA_HTML_KEY", '')  !!}'
            });
        };
    </script>

    {!! JsValidator::formRequest('App\Modules\Contacto\Requests\ContactRequest')->selector('#contact-form') !!}
@stop
