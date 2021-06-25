@extends('themes.front.layouts.app')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ $page_title }}</li>
@stop


@section('content')


    <div class="container p-b-xl">
        <h1>Prueba servicios</h1>

        <div class="list-group">
            <a href="/recognition/s3" class="list-group-item">AWS S3</a>
            <a href="/recognition/rekognition" class="list-group-item">AWS Rekognition</a>
            <a href="/recognition/textract" class="list-group-item">AWS Textract</a>
            <a href="#" class="list-group-item">Prueba 4</a>
          </div>


    </div><!-- /.container -->
@endsection

@section("foot_page")

@stop

