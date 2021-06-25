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
    <div class="container pb-xlg">
        <div class="row">
            <div class="col-lg-12">
                @include('front.includes.errors')
                @include('front.includes.success')
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
            </div>
        </div>
        <form id='formData' class="form-horizontal d-flex justify-content-center align-items-center pt-xlg" role="form" method="POST" action="{{ route('password.email') }}">
            {{ csrf_field() }}
            <div class="light-rounded-box row p-lg">
                <div class="col-lg-12">
                    <div class="row d-flex align-items-center pb-lg">
                        <div class="svg_img_wrapper">
                            <img src="{{ asset("assets/front/img/key.svg") }}" alt="key_icon">
                        </div>
                        <h4>{{ trans("general/front_lang.recuperar_pass") }}</h4>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p>{{ trans("auth/lang.recover_info_01") }}</p>
                            <p>{{ trans("auth/lang.recover_info_02") }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group pl-md pr-md">
                                <input type="text" name="email" class="form-control" id="email"
                                       placeholder="{{ trans("users/lang.email_usuario") }}"  value="{{ old('email') }}" autofocus>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p>{{ trans("auth/lang.recover_info_03") }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <button id="btn_submit" type="submit" class="btn background-color-secondary pt-sm pr-md pb-sm pl-md text-light pull-right"><i class="fa fa-paper-plane"></i>&nbsp;&nbsp;{{ trans("general/front_lang.enviar") }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection


@section("foot_page")
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

{!! JsValidator::formRequest('App\Http\Requests\FrontPassLostRequest')->selector('#formData') !!}

@stop
