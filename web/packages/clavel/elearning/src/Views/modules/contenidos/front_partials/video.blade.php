<style>
    DIV.image_block {
        border: solid 1px #C0C0C0;
        margin-bottom: 25px;
    }

    DIV.comment_img {
        padding: 20px 20px;
        background-color: #f1f1f1;
        text-align: left;
        border-top: solid 1px #C0C0C0;
        -moz-box-shadow: inset 0 0 10px #C0C0C0;
        -webkit-box-shadow: inset 0 0 10px #C0C0C0;
        box-shadow: inset 0 0 10px #C0C0C0;
    }

    DIV.image_content {
        cursor: pointer !important;
        border: solid 1px #C0C0C0 !important;
    }

    IMG#image_img {
        cursor: zoom-in;
    }

    .modal-dialog {
        max-width: 1024px;
    }

    @media (min-width: 992px) {
        .modal-dialog {
            max-width: 1024px;
        }
    }
</style>

@if(!empty($contenido->nombre))
    <h3>{{ $contenido->nombre }}</h3>
@else
    <div class="alert alert-warning mb-none">
        <?php $traducidos = $contenido->getTraduccionesReales(); ?>
        {{ trans_choice("general/front_lang.no_lang", $traducidos->count()) }}:
        <strong>{{ $contenido->getTraduccionesReales()->implode("name", ", ") }}</strong>
    </div>
@endif
<?php
$array = explode("/", $contenido->media_url);
$video = \Clavel\Basic\Models\Media::find(intval(end($array)));
?>
@if(!empty($video))
    <div class="modal fade" id="zoom_image" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title"
                        id="largeModalLabel">{{ trans("elearning::contenidos/front_lang.zoom_imagen") }}</h4>
                </div>
                <div class="modal-body">
                    <img id="image_zoom" src="" class="img-responsive" alt="" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{ trans("general/front_lang.cerrar") }}</button>
                </div>
            </div>
        </div>
    </div>
    <div style="display: flex">
        <video id="my-video" style="position: static; width: 100%; height: 100%;" controls>
            <source src="{{ url("media/getAnnex/".$video->id) }}" type="{{ $video->mime }}">
        </video>
    </div>
@else
    <p class="text-warning">{{ trans("elearning::contenidos/front_lang.sin_contenidos_galeria") }}</p>
@endif

<br clear="all">
<br clear="all">

@section('foot_page')
    @parent
    <script>
        var vid = document.getElementById("my-video");
        let startTrack = undefined;
        let userStop = Number("{{ empty($contenido->trackVideo) ? "0" : $contenido->trackVideo->user_stop }}");
        vid.currentTime = userStop;

        vid.addEventListener("play", function () {
            startTrack = setInterval(trackVideo, 2000);
        });

        ['pause', 'ended'].forEach(function(e) {
            vid.addEventListener(e, function () {
                clearInterval(startTrack);
            });
        });

        function trackVideo() {
            $.post("{{url("contenido/track-video")}}", {
                "_token": "{{ csrf_token() }}",
                "contenido_id": "{{ $contenido->id }}",
                "modulo_id": "{{ $contenido->modulo->id }}",
                "asignatura_id": "{{ $contenido->modulo->asignatura->id }}",
                "user_stop": vid.currentTime,
                "total_video_seconds": vid.duration
            });
        }
    </script>
@endsection
