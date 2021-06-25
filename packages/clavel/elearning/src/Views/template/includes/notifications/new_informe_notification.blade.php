<li style="padding-top: 0">
    <a href="#" data-notif-id="{{ $notif->id }}" style="padding: 0">
        <p>
            {{ trans('Consultas::consultas/front_lang.informe') }}:<br>{{ $notif->data['title'] }}
            <span class="timeline-icon" style="left: -34px; top: 4px;"><i class="far fa-file-alt" style="color:#0040ca !important"></i></span>
            <span class="timeline-date">{{ $notif->data['date'] }}</span>
        </p>
    </a>
</li>

