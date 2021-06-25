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
            <li class="breadcrumb-item"><a href="{{ url('/recognition/textract') }}">{{ trans("recognition::general/front_lang.titleTextract") }}</a></li>
            <li class="breadcrumb-item active">{{ $page_title }}</li>
        </ol>

        <h1>Prueba servicio de AWS Textract</h1>

        @if(!empty($dni))
            <h2>DNI detectado: <strong>{{ $dni}}</strong></h2>
        @endif

        <div class="list-group">
            @foreach ($textDetections as $phrase)
                @if($phrase['BlockType'] == 'WORD')
                <li class="list-group-item">
                {{ $phrase['Text'] }} - {{ $phrase['BlockType'] }} - {{ $phrase['Id'] }} - {{ $phrase['Confidence'] }}
                </li>
                @endif
            @endforeach
        </div>

    </div><!-- /.container -->


@endsection

@section("foot_page")

@stop

