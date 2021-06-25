<li class="treeview @if (Request::is('admin/plantillas*') ||
                         Request::is('admin/notifications-group*') ||
                         Request::is('admin/notifications*') ||
                         Request::is('admin/blacklists*') ||
                         Request::is('admin/bouncedemails*') ||
                         Request::is('admin/bouncetypes*')

                         ) active open @endif">
    <a href="#"><i class="fa fa-comments-o" aria-hidden="true"></i> <span>{{ trans('notificationbroker::notifications/admin_lang.notifications') }}</span> <i class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
    <ul class="treeview-menu">


        @if(Auth::user()->can('admin-plantillas'))
            <li @if (Request::is('admin/plantillas*')) class="active" @endif>
                <a href="{{ url('/admin/plantillas') }}"><i class="fa fa-object-group" aria-hidden="true"></i> {{ trans('notificationbroker::plantillas/admin_lang.plantillas') }}</a>
            </li>
        @endif


        @if(Auth::user()->can('admin-notifications-broker-group'))
            <li @if (Request::is('admin/notifications-group*')) class="active" @endif>
                <a href="{{ url('/admin/notifications-group') }}"><i class="fa fa-inbox" aria-hidden="true"></i> {{ trans('notificationbroker::notifications-group/admin_lang.notifications-group') }}</a>
            </li>
        @endif

        @if(Auth::user()->can('admin-notifications-broker'))

            <li @if (Request::is('admin/notifications*') && !Request::is('admin/notifications-group*')) class="active" @endif>
                <a href="{{ url('/admin/notifications') }}"><i class="fa fa-list" aria-hidden="true"></i> {{ trans('notificationbroker::notifications/admin_lang.notifications') }}</a>
            </li>
        @endif

        @if(Auth::user()->can('admin-blacklists'))
            <li @if (Request::is('admin/blacklists*')) class="active" @endif>
                <a href="{{ url('/admin/blacklists') }}"><i class="fa fa-certificate" hidden="true" aria-hidden="true"></i>
                    <span>{{ trans('notificationbroker::blacklists/admin_lang.blacklists') }}</span></a>
            </li>
        @endif


        @if(Auth::user()->can('admin-bouncedemails'))
            <li @if (Request::is('admin/bouncedemails*')) class="active" @endif>
                <a href="{{ url('/admin/bouncedemails') }}"><i class="fa fa-envelope-open" aria-hidden="true"></i>
                    <span>{{ trans('notificationbroker::bouncedemails/admin_lang.bouncedemails') }}</span></a>
            </li>
        @endif

        @if(Auth::user()->can('admin-bouncetypes'))
            <li @if (Request::is('admin/bouncetypes*')) class="active" @endif>
                <a href="{{ url('/admin/bouncetypes') }}"><i class="fa fa-bug" aria-hidden="true"></i>
                    <span>{{ trans('notificationbroker::bouncetypes/admin_lang.bouncetypes') }}</span></a>
            </li>
        @endif

    </ul>
</li>




