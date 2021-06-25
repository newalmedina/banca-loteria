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
            <li class="breadcrumb-item active">{{ $page_title }}</li>
        </ol>

        <h1>Prueba servicio de AWS S3</h1>



        <div class="list-group">
            @foreach ($buckets as $bucket)
            <a href="{{ url('recognition/s3/'.$bucket['Name'])}}" class="list-group-item">{{ $bucket['Name'] }} - {{ $bucket['CreationDate'] }}</a>
            @endforeach
        </div>


    </div><!-- /.container -->
@endsection

@section("foot_page")

@stop

