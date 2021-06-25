@extends('notificationbroker::front.email.broker.default')

@section('title')
    @parent {{ @$payload['to']  }}
@stop



@section('content')



    @include('notificationbroker::front.email.broker.articleStart')


    {!! @$payload['content'] !!}

    @include('notificationbroker::front.email.broker.articleEnd')




@endsection
