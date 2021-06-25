<div id="ele_{{ $element->id }}" class="elementInfo"
     style="top:{{ $element->mtop }}px; left:{{ $element->mleft }}px; width:{{ $element->width }}px; height: {{ $element->height }}px; font-family:{{ $element->fontfamily }}, sans-serif; font-size:{{ $element->fontsize }}; color:{{ $element->fontcolor }};"
     data-value="{{ $element->id }}">
    <div class="functionsScript">
        <a href="javascript:editelement('{{ $element->id }}');"><i class="fa fa-pencil mediumIcon" aria-hidden="true"></i></a>
        <a href="javascript:deleteelement({{ $element->id }});"><i class="fa fa-times-circle mediumIcon" aria-hidden="true"></i></a>
    </div>
    {{ $element->name }}
</div>
