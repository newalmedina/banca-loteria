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

        <h1>Prueba servicio de AWS Rekognition Labels</h1>


        <div class="list-group">
            @foreach ($textDetections as $phrase)

                <li class="list-group-item"
                @if( $phrase['Name'] === "Driving License" ||
                    $phrase['Name'] === "License" ||
                    $phrase['Name'] === "Passport" ||
                    $phrase['Name'] === "Id Cards"
                    )
                    style="font-weight: bold"
                @endif
                >
                {{ $phrase['Name'] }} - {{ $phrase['Confidence'] }}
                </li>

            @endforeach
        </div>


    </div><!-- /.container -->


@endsection

@section("foot_page")

@stop

