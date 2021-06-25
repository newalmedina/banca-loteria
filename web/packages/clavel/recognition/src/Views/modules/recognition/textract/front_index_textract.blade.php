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
            <li class="breadcrumb-item"><a href="{{ url('/textract') }}">Home</a></li>
            <li class="breadcrumb-item active">{{ $page_title }}</li>
        </ol>

        <h1>Prueba servicio de AWS Textract</h1>

        <h2>Bucket: {{ $bucket }}</h2>

        <div class="list-group">
            @foreach ($objects as $object)
            <a href="{{ empty($object['Key'])?'#':url('recognition/textract/process/'.$bucket.'/'.base64_encode($object['Key']))  }}"
                class="list-group-item"
                >
                    {{ $object['Key'] }} - {{ empty($object['Size'])?'':$object['Size'] }} - {{ $object['LastModified'] }}
            </a>
            @endforeach
        </div>


    </div><!-- /.container -->


@endsection

@section("foot_page")

@stop

