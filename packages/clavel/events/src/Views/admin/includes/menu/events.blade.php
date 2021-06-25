@if(Auth::user()->can('admin-events'))
    <li class="treeview @if (Request::is('admin/events*')) active open @endif">
        <a href="#"><i class="fa fa-calendar" hidden="true" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.calendar') }}</span> <i class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
        <ul class="treeview-menu">
            @if(Auth::user()->can('admin-events-list'))
                <li @if (Request::is('admin/events*') && (!Request::is('admin/events/tags*'))) class="active" @endif>
                    <a href="{{ url('/admin/events') }}"><i class="fa fa-sticky-note" aria-hidden="true"></i> {{ trans('general/admin_lang.gestion_events') }}</a>
                </li>
            @endif

            @if(Auth::user()->can('admin-events-tags'))
                <li @if (Request::is('admin/events/tags*')) class="active" @endif>
                    <a href="{{ url('/admin/events/tags') }}"><i class="fa fa-tags" aria-hidden="true"></i> {{ trans('general/admin_lang.tags_news') }}</a>
                </li>
            @endif
        </ul>
    </li>
@endif
