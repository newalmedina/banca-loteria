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

        <h1>Prueba servicio de AWS Rekognition</h1>

        <h2>Bucket: {{ $bucket }}</h2>

        <!-- Table -->
        <table class="table" aria-hidden="true">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($objects as $object)
                <tr>
                    <td scope="row">
                        <a href="{{ empty($object['Key'])?'#':url('recognition/rekognition/process/'.$bucket.'/'.base64_encode($object['Key']))  }}"
                        >
                                {{ $object['Key'] }} - {{ empty($object['Size'])?'':$object['Size'] }} - {{ $object['LastModified'] }}
                        </a>
                    </td>

                    <td>
                        <a  class="btn btn-primary"  href="{{ empty($object['Key'])?'#':url('recognition/rekognition/process-label/'.$bucket.'/'.base64_encode($object['Key']))  }}"
                            >
                               Etiquetas
                        </a>


                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>


    </div><!-- /.container -->


@endsection

@section("foot_page")

@stop

