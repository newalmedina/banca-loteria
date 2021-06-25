@extends('front.email.default')

@section('content')

    Recibida solicitud de registro<br><br>
    <strong>{{ $user->userProfile->fullName }}</strong> (<strong>{{ $user->username }}</strong>) ({{ $user->email }}) <br><br>


@endsection
