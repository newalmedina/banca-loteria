@extends('themes.front.layouts.app')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
<style>
    .modal-img {
        width:100%;
    }
</style>

@stop

@section('breadcrumb')

@stop


@section('content')

    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/recognition') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/recognition/s3') }}">{{ trans("recognition::general/front_lang.titleS3") }}</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/recognition/s3/'.$bucket) }}">{{ trans("recognition::general/front_lang.titleS3Objects") }}</a></li>
            <li class="breadcrumb-item active">{{ $page_title }}</li>
        </ol>

        <h1>Identificación DNI</h1>
        <h2>Bucket: {{ $bucket }}</h2>
        <h2>Fichero: {{ $file }}</h2>

        @include('recognition::recognition.s3.front_image', [ 'presignedUrl' => $presignedUrl ])

        <h3>Confianza de ser un DNI: {{ $labels['confidence'] }}</h3>
        <h3>Etiquetas de confianza: {{ $labels['confidenceCount'] }}</h3>


        @if(!empty($textos1['dni']))
            <h2>DNI detectado método 1: <strong>{{ $textos1['dni'] }}</strong></h2>
        @endif
        @if(!empty($textos2['dni']))
            <h2>DNI detectado método 2: <strong>{{ $textos2['dni'] }}</strong></h2>
        @endif

        @if(!empty($dni))
            <h2>DNI detectado: <strong>{{ $dni}}</strong></h2>
        @endif

        <!-- Table -->
        <table class="table" aria-hidden="true">
            <thead>
                <tr>
                    <th scope="col">Etiqueta</th>
                    <th scope="col">Confianza</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($labels['textDetections']  as $object)
                <tr>
                    <td
                        @if( $object['Name'] === "Driving License" ||
                            $object['Name'] === "License" ||
                            $object['Name'] === "Passport" ||
                            $object['Name'] === "Id Cards"
                            )
                        style="font-weight: bold"
                        @endif
                    >{{ $object['Name']  }}</td>
                    <td>{{ $object['Confidence'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Textos 1</h3>
        <!-- Table -->
        <table class="table" aria-hidden="true">
            <thead>
                <tr>
                    <th scope="col">Texto</th>
                    <th scope="col">Confianza</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($textos1['textos'] as $phrase)
                <tr>
                    <td>{{ $phrase['DetectedText']  }}</td>
                    <td>{{ $phrase['Confidence'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Textos 2</h3>
        <!-- Table -->
        <table class="table" aria-hidden="true">
            <thead>
                <tr>
                    <th scope="col">Texto</th>
                    <th scope="col">Confianza</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($textos2['textos'] as $phrase)
                <tr>
                    <td>{{ $phrase['Text']  }}</td>
                    <td>{{ $phrase['Confidence'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>







    </div><!-- /.container -->


@endsection

@section("foot_page")

@stop

