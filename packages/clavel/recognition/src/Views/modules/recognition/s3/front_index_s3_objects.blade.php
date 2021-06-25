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

    <div class="modal modal-preview fade in" id="bs-modal-preview">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Imagen remota</h4>
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                </div>
                <div id="content-preview" class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="container">

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/recognition') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/recognition/s3') }}">{{ trans("recognition::general/front_lang.titleS3") }}</a></li>
            <li class="breadcrumb-item active">{{ $page_title }}</li>
        </ol>

        <h1>Prueba servicio de AWS S3</h1>

        <h2>Bucket: {{ $bucket }}</h2>

        <div class="panel panel-default">

            <div class="panel-heading">Ficheros</div>

            <!-- Table -->
            <table class="table" aria-hidden="true">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tamaño</th>
                        <th scope="col">Ultima modificacion</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($objects as $object)
                    <tr>
                        <td scope="row">{{ $object['Key'] }}</td>
                        <td>{{ empty($object['Size'])?'':$object['Size'] }}</td>
                        <td>{{ $object['LastModified'] }}</td>
                        <td>
                            <a class="btn btn-primary" href="{{ empty($object['Key'])?'#':url('recognition/s3/download/'.$bucket.'/'.base64_encode($object['Key']))  }}">
                                Descargar
                            </a>
                            <a  class="btn btn-warning" href="{{ empty($object['Key'])?'#':url('recognition/s3/process/'.$bucket.'/'.base64_encode($object['Key']))  }}">
                                Procesar
                            </a>

                            <a class="btn btn-success" href="javascript:showPreview('{{ empty($object['Key'])?'#':url('recognition/s3/view/'.$bucket.'/'.base64_encode($object['Key']))  }}');"
                                data-toggle="popover">
                                Ver
                            </a>

                            <a  class="btn btn-danger" href="{{ empty($object['Key'])?'#':url('recognition/s3/delete/'.$bucket.'/'.base64_encode($object['Key']))  }}">
                                Borrar
                            </a>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <a href="{{ url('recognition/s3/upload/'.$bucket)}}" class="btn btn-secondary navbar-btn float-right mt-5">Subir ficheros</a>


    </div><!-- /.container -->
@endsection

@section("foot_page")

<script>
    function showPreview(url) {

        if(url!='') {
            $("#content-preview").html('<div id="spinner2" class="overlay" style="text-align: center"><i class="fa fa-refresh fa-spin" style="font-size: 64px;"></i></div>');
            $('#bs-modal-preview').modal({
                keyboard: false,
                backdrop: 'static',
                show: true
            });
            $("#content-preview").load(url);
        }

    }

</script>
@stop

