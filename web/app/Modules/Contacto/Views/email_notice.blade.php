@extends('front.email.default')

@section('title')
    @parent {{ @$payload['to'] }}
@stop

@section('content')

    @include('front.email.articleStart')
    <p>
        <span style='font-size:16px'>¡Administrador!</span><br><br>

        <strong>Hemos recibido una nueva solicitud de contacto</strong><br><br>

        Con los siguientes datos:<br>
        Nombre: {{ $fullname }}<br>
        Email: {{ $email }}<br><br>

        Con el siguiente mensaje:<br>
        {{ $description }}<br><br>

    </p>

    @include('front.email.articleEnd')
@endsection
