@extends('front.email.default')

@section('title')
    @parent {{ @$payload['to'] }}
@stop

@section('content')

    @include('front.email.articleStart')

        <p>Hola, <strong>{{ $user->userProfile->fullName }}</strong></p>

        <p>{{ trans("posts::front_lang.email_001") }}</p>

        <p> <a href="{{ url("posts/post/".$post->url_seo) }}">{{ $post->title }}</a></p>
        <br>
        <p>{{ trans("auth/lang.info_05") }} <br>
        Emocional.reg</p>

    @include('front.email.articleEnd')

@endsection
