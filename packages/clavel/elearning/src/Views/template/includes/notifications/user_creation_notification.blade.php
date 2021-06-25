<li style="padding-top: 0">
    <a href="#" data-notif-id="{{ $notif->id }}" style="padding: 0">
        <p>
            {{ trans('general/admin_lang.notif_new_user') }}:<br>{{ $notif->data['name'] }}
            <span class="timeline-icon" style="left: -34px; top: 4px;"><i class="fas fa-user" style="color:#CA006E !important"></i></span>
        </p>
    </a>
</li>
