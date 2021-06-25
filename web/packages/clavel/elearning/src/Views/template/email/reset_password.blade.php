@extends('front.email.default')

@section('title')
    @parent {{ @$payload['to'] }}
@stop

@section('content')

    @include('front.email.articleStart')


    <h1>{{ trans("auth/lang.info_01_01") }}</h1>

    <p>{{ trans("auth/lang.info_02") }} </p>
    <br>
    @include('front.email.button')
    <br>

    <p>{{ trans("auth/lang.info_03") }} </p>
    <br>
    <p>{{ trans("auth/lang.info_05") }} <br><br>
        <strong>{!! trans("general/front_custom_lang.email.signature") !!}</strong>
    </p>


    @include('front.email.articleEnd')


@endsection
