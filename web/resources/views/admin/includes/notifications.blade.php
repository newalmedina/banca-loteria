{{-- Notificaciones --}}
@if(!auth()->guest())
    @php
        $notifications = auth()->user()->unreadNotifications()->where(function($q) {
              $q->where('visibility', 'admin')
              ->orWhere('visibility', '=', '');
          })->get();
    @endphp
    @if($notifications->count()>0 )
        <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-bell-o" aria-hidden="true"></i>
                <span class="label label-warning">{{ $notifications->count() }}</span>
            </a>
            <ul class="dropdown-menu">
                <li class="header"> {{ trans('general/admin_lang.notif_count', ['contador' => $notifications->count()]) }}</li>
                <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                        @foreach ($notifications as $notif)
                            @if(View::exists('admin.includes.notifications.'.snake_case(class_basename($notif->type))))
                                @include('admin.includes.notifications.'.snake_case(class_basename($notif->type)))
                            @endif
                        @endforeach
                    </ul>
                </li>
                <li class="footer"><a id="mark-all" href="#">{{ trans(('general/admin_lang.notif_mark_all')) }}</a></li>
            </ul>
        </li>
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
