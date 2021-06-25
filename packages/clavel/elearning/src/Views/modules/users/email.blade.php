@extends('front.email.default')

@section('title')
    @parent {{ @$payload['to'] }}
@stop

@section('content')

    @include('front.email.articleStart')


    {{ trans("users/lang.email_001") }} <strong>{{ $user->userProfile->first_name }}</strong>:<br><br>
    {{ trans("users/lang.email_002") }}<br><br>
    <a href="{{ url("usuarios/confirmar/".md5($user->id)) }}">{{ url("usuarios/confirmar/".md5($user->id)) }}</a>



    @include('front.email.articleEnd')

@endsection

