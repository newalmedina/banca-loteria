@section('head_page')
    @if(!is_null($css) && $css!='')
        <style>
            {!! $css !!}
        </style>
    @endif
@show

<div class="panel panel-default">
    @if($title_block!='')
        <div class="panel-heading">
            <h3 class="panel-title">{{ $title_block }}</h3>
        </div>
    @endif
    <div class="panel-body">
        {!! $content_block !!}
    </div>
</div>

@section('foot_page')
    @if(!is_null($js) && $js!='')
        <script>
            {!! $js !!}
        </script>
    @endif
@append
