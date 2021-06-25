@extends('admin.email.default')

@section('title')
@parent {{ @$payload['to'] }}
@stop

@section('content')

@include('admin.email.articleStart')


    <h1>{{ trans("auth/lang.info_01_01") }}</h1>

    <p>{{ trans("auth/lang.info_02") }} </p>
    <br>
    @include('admin.email.button')
    <br>

    <p>{{ trans("auth/lang.info_03") }} </p>
    <br>
    <p>{{ trans("auth/lang.info_05") }} <br>
    Emocional.reg</p>


@include('admin.email.articleEnd')


@endsection
