<style>
    video::-webkit-media-controls-fullscreen-button {}
    video::-webkit-media-controls-play-button {}
    video::-webkit-media-controls-timeline {}
    video::-webkit-media-controls-current-time-display{}
    video::-webkit-media-controls-time-remaining-display {}
    video::-webkit-media-controls-mute-button {}
    video::-webkit-media-controls-toggle-closed-captions-button {
        display: none;
    }
    video::-webkit-media-controls-volume-slider {}

    .texto IMG, .texto VIDEO, .texto TABLE {
        max-width: 100%;
    }

    .texto IMG, .texto VIDEO {
        height: auto;
    }
</style>
<div>
    @if(!empty($contenido->nombre))
    <h3>{{ $contenido->nombre }}</h3>
    <div class="texto">{!! $contenido->contenido !!}</div>
    @else
        <div class="alert alert-warning mb-none">
            <?php $traducidos = $contenido->getTraduccionesReales(); ?>
            {{ trans_choice("general/front_lang.no_lang", $traducidos->count()) }}:
            <strong>{{ $contenido->getTraduccionesReales()->implode("name", ", ") }}</strong>
        </div>
    @endif
</div>
<br clear="all"><br clear="all">
<script>
    function avisar_{{$contenido->id}} () {
        $.ajax({
            type		: 'GET',
            url			: '{{ url("contenido/track-contenido/".$contenido->id) }}',
            "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            success		: function(data) { }
        });
    }
</script>
