@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section("head_page")

@stop

@section('content')
    @if(config("elearning.autentificacion.EMAIL_CONFIRMACION"))
        {!! Form::hidden('email', $user->email, ['id' => 'email_confirm']) !!}
    @endif

    <div class="container pt-lg pb-xlg">
        <div class="row">
            <div class="col-md-9 col-md-offset-2">
                <div class="panel panel-default box-shadow-custom">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <h4><i class="fa fa-check-circle text-success fa-3x" aria-hidden="true"></i></h4>
                        <h4><i class="fa fa-check text-success" aria-hidden="true"></i> {{ trans("users/lang.alta_correcta") }}</h4>
                        @if(config("elearning.autentificacion.EMAIL_CONFIRMACION"))

                            <br clear="all">
                            <p class="text-left">{{ trans("users/lang.alta_correcta_11") }} {{ trans("users/lang.alta_correcta_12") }}</p>
                            <br clear="all">
                            <div class="text-left">
                                {{ trans("users/lang.alta_correcta_13") }}<br><br>
                                <div class="text-center">
                                    <button id="send_mail_confirm" type="button" class="btn btn-primary p-sm">{{ trans("users/lang.send_mail") }}</button>
                                </div>
                            </div>
                            <p class="mt-lg">
                                <a class="p-sm btn btn-info" href="/">{{ trans("users/lang.volver_a_la_home")}}</a>
                            </p>

                            <div id="success_confirmacion" class="alert alert-success" role="alert" style="display: none;">
                                <button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button>
                                <strong>{{ date('d/m/Y H:i:s') }}</strong>
                                {{ __("users/lang.alta_correcta_11") }}
                            </div>
                            <div id="error_confirmacion" class="alert alert-danger" role="alert" style="display: none;">
                                <button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button>
                                <strong>{{ date('d/m/Y H:i:s') }}</strong>
                                <div id="error_text"></div>
                            </div>
                            <br clear="all">
                            <br clear="all">
                            <br clear="all">

                            <div class="row">
                                <div class="col-md-12">
                                    <h5>{{ trans("users/lang.lopd_1") }}</h5>
                                    <p class="text-left">{{ trans("users/lang.lopd_2") }}</p>
                                    <p class="text-left">{{ trans("users/lang.lopd_3") }}</p>
                                    <p class="text-left">{{ trans("users/lang.lopd_4") }}</p>
                                </div>
                            </div>
                        @else
                            <p>{{ trans("users/lang.alta_correcta_01") }} <strong class="text-primary">"{{ env("PROJECT_NAME") }}"</strong> {{ trans("users/lang.alta_correcta_02") }}</p>
                            <a class="btn btn-primary" href="{{ url("/login") }}">{{ trans("users/lang.iniciar") }}</a>
                        @endif
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection

@section("foot_page")
    @if(config("elearning.autentificacion.EMAIL_CONFIRMACION"))
        <script>
            $(document).ready(function() {
                $("#send_mail_confirm").click(function() {
                    $("#success_confirmacion").css("display","none");
                    if($("#email_confirm").val()!='') {
                        $("#error_confirmacion").css("display","none");

                        $.ajax({
                            url: "{{url('/usuarios/send_confirmar_mail')}}",
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN':"{{ csrf_token() }}"
                            },
                            data: {
                                email: $("#email_confirm").val()
                            },
                            success: function ( data ) {
                                if(data!='OK') {
                                    $("#error_text").html(data);
                                    $("#error_confirmacion").css("display","block");
                                } else {
                                    $("#success_confirmacion").css("display","block");
                                }
                            }
                        });

                    } else {
                        $("#error_text").html("{!! trans('auth/lang.confirmacion_email_fill') !!}");
                        $("#error_confirmacion").css("display","block");
                    }
                });
            });
        </script>
    @endif
@stop
