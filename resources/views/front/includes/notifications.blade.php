{{-- Notificaciones --}}
@if(!auth()->guest())
    @php
        $notifications = auth()->user()->unreadNotifications;
    @endphp
    @if($notifications->count()>0 )
        <div id="notify-container" class="dropdown">
            <a href="#" onclick="return false;" role="button" data-toggle="dropdown" id="dropdownMenu1" data-target="#" style="float: left" aria-expanded="true">
                <i id="notify-bell"  class="fa fa-bell-o" aria-hidden="true">
                </i>
            </a>
            <span class="badge badge-danger">{{ $notifications->count() }}</span>
            <ul class="dropdown-menu dropdown-menu-left pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li role="presentation">
                    <a href="#" class="dropdown-menu-header">{{ trans('general/admin_lang.notif_count', ['contador' => $notifications->count()]) }}</a>
                </li>
                <ul class="timeline timeline-icons timeline-sm" style="margin:10px;width:210px">
                    @foreach ($notifications as $notif)
                        @if(View::exists('admin.includes.notifications.'.snake_case(class_basename($notif->type))))
                            @include('admin.includes.notifications.'.snake_case(class_basename($notif->type)))
                        @endif
                    @endforeach

                </ul>
                <li role="presentation">
                    <a id="mark-all" href="#" class="dropdown-menu-header">{{ trans(('general/admin_lang.notif_mark_all')) }}</a>
                </li>
            </ul>
        </div>

        <script>
            var notifications = document.querySelectorAll('[data-notif-id]');
            var markNotification = function() {
                document.getElementById('notify_id').value = this.getAttribute("data-notif-id");
                document.getElementById('notification-form').action = '{{ url('admin/notification/mark') }}';
                document.getElementById('notification-form').submit();
            };

            var markAllNotifications = function() {
                document.getElementById('notification-form').action = '{{ url('admin/notification/mark_all') }}';
                document.getElementById('notification-form').submit();
            };

            for (var i = 0; i < notifications.length; i++) {
                notifications[i].addEventListener('click', markNotification, false);
            }
            document.getElementById('mark-all').addEventListener('click', markAllNotifications, false);


        </script>
        <form id="notification-form" action="" method="POST" style="display: none;">
            {{ csrf_field() }}
            <input type="hidden" id="notify_id" name="notify_id" value="">
        </form>
    @endif
@endif

