@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('breadcrumb')
    <li>{{ $page_title }}</li>
@stop

@section("head_page")

@stop

@section('content')

    <div class="container pt-lg pb-xlg">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default box-shadow-custom">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <h4><i class="fa fa-check-circle text-success fa-3x" aria-hidden="true"></i></h4>
                        <h4><i class="fa fa-check text-success" aria-hidden="true"></i> {{ trans("users/lang.confirmacion_correcta") }}</h4>
                        <br clear="all">
                        <p>{{ trans("users/lang.confirmacion_correcta_01") }}</p>
                        <p class="mt-lg">
                            <a class="p-sm btn btn-info" href="/">{{ trans("users/lang.volver_a_la_home")}}</a>
                        </p>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
