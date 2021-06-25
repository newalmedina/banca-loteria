@extends('themes.front.layouts.app')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')


@stop

@section('breadcrumb')

@stop


@section('content')

    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/recognition') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/recognition/rekognition') }}">{{ trans("recognition::general/front_lang.titleRekognition") }}</a></li>
            <li class="breadcrumb-item active">{{ $page_title }}</li>
        </ol>

        <h1>Prueba servicio de AWS Rekognition</h1>

        @if(!empty($dni))
            <h2>DNI detectado: <strong>{{ $dni}}</strong></h2>
        @endif

        <div class="list-group">
            @foreach ($textDetections as $phrase)
                @if($phrase['Type'] === 'WORD')
                <li class="list-group-item">
                {{ $phrase['DetectedText'] }} - {{ $phrase['Type'] }} - {{ $phrase['Id'] }} - {{ $phrase['Confidence'] }}
                </li>
                @endif
            @endforeach
        </div>


    </div><!-- /.container -->


@endsection

@section("foot_page")

@stop

