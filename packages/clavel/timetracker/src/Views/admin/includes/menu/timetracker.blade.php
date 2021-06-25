@if(Auth::user()->can('admin-timetracker'))
    <li class="treeview @if (Request::is('admin/customers*') ||
                        Request::is('admin/projects*') ||
                        Request::is('admin/activities*') ||
                        Request::is('admin/timesheet*') ||
                        Request::is('admin/mytimes*') ||
                        Request::is('admin/timetracker-dashboard*') ||
                        Request::is('admin/timetracker-config*')
                    ) active open @endif">
        <a href="#"><i class="fa  fa-clock-o" aria-hidden="true"></i>
            <span>{{ trans("timetracker::general/admin_lang.title") }}</span> <i
                    class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
        <ul class="treeview-menu">

            @if(Auth::user()->can('admin-timetracker-dashboard'))
                <li @if (Request::is('admin/timetracker-dashboard*')) class="active" @endif>
                    <a href="{{ url('/admin/timetracker-dashboard') }}"><i class="fa fa-tachometer" aria-hidden="true"></i>
                        <span>{{ trans("timetracker::dashboard/admin_lang.title") }}</span></a>
                </li>
            @endif
            @if(Auth::user()->can('admin-mytimes'))
                <li @if (Request::is('admin/mytimes*')) class="active" @endif>
                    <a href="{{ url('/admin/mytimes') }}"><i class="fa fa-puzzle-piece" aria-hidden="true"></i>
                        <span>{{ trans("timetracker::mytimes/admin_lang.title") }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-timesheet'))
                <li @if (Request::is('admin/timesheet*')) class="active" @endif>
                    <a href="{{ url('/admin/timesheet') }}"><i class="fa  fa-clock-o" aria-hidden="true"></i>
                        <span>{{ trans("timetracker::timesheet/admin_lang.title") }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-customers'))
                <li @if (Request::is('admin/customers*')) class="active" @endif>
                    <a href="{{ url('/admin/customers') }}"><i class="fa fa-users" aria-hidden="true"></i>
                        <span>{{ trans("timetracker::customers/admin_lang.title") }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-projects'))
                <li @if (Request::is('admin/projects*')) class="active" @endif>
                    <a href="{{ url('/admin/projects') }}"><i class="fa fa-cubes" aria-hidden="true"></i></i>
                        <span>{{ trans("timetracker::projects/admin_lang.title") }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-activities'))
                <li @if (Request::is('admin/activities*')) class="active" @endif>
                    <a href="{{ url('/admin/activities') }}"><i class="fa fa-list" aria-hidden="true"></i></i>
                        <span>{{ trans("timetracker::activities/admin_lang.title") }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-timetracker-config'))
                <li @if (Request::is('admin/timetracker-config*')) class="active" @endif>
                    <a href="{{ url('/admin/timetracker-config') }}"><i class="fa fa-cogs" aria-hidden="true"></i>
                        <span>{{ trans("timetracker::config/admin_lang.title") }}</span></a>
                </li>
            @endif
        </ul>
    </li>
@endif
