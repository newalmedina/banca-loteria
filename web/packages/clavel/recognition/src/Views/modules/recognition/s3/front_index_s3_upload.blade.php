@extends('themes.front.layouts.app')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link rel="stylesheet" href="{{ asset("/assets/front/vendor/dropzone/dropzone.min.css") }} ">
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

        <h1>Prueba servicio de AWS S3 Subida fichero</h1>

        <h2>Bucket: {{ $bucket }}</h2>

        <div class="row">
            <div class="col-md-12">
                <form action="{{ url('/recognition/s3/upload') }}"  class="dropzone" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="bucket" value="{{ $bucket }}">
                    <div class="fallback">
                        <input name="file" type="file" multiple />
                    </div>


                </form>
            </div>
        </div>


    </div><!-- /.container -->




@endsection

@section("foot_page")
<script src="{{ asset("/assets/front/vendor/dropzone/dropzone.min.js") }}"></script>


<script type="text/javascript">


</script>
@stop

