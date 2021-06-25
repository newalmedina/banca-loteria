@extends('front.email.default')

@section('title')
    @parent {{ @$payload['to'] }}
@stop

@section('content')

    @include('front.email.articleStart')

    <p>
        <span style='font-size:16px'> Apreciado/a, {{ $fullname }}</span><br><br>

        <strong>Hemos recibido su solicitud de contacto</strong><br><br>

        Con el siguiente mensaje:<br><br>
        {{ $description }}<br><br>

        En breve nos pondremos en contacto con usted.<br><br>
        Saludos cordiales<br>
    </p>

    @include('front.email.articleEnd')

@endsection
