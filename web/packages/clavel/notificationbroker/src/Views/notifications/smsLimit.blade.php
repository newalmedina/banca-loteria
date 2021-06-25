@extends('notificationbroker::front.email.broker.basic')

@section('title')
    @parent {{ @$payload['to']  }}
@stop



@section('content')



    @include('notificationbroker::front.email.broker.articleStart')


    <h4 class="secondary"><strong>Hola,</strong></h4>

    <p>Se ha superado el límite de seguridad de créditos SMS. Actualmente quedan <strong>{{ @$payload['credits'] }}</strong> SMS's.</p>

    <p>Se deben contratar más. Contacta con comercial@aduxia.com.</p>

    <p>Saludos.</p>



    @include('notificationbroker::front.email.broker.articleEnd')




@endsection
