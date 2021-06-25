@if(Auth::user()->can('admin-struct'))
    <li class="treeview @if (Request::is('admin/pages*') || Request::is('admin/media*') || Request::is('admin/menu*')) active open @endif">
        <a href="#"><i class="fa fa-th-large" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.estructura_web') }}</span> <i class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
        <ul class="treeview-menu">

            @if(Auth::user()->can('admin-menu'))
                <li @if (Request::is('admin/menu*')) class="active" @endif>
                    <a href="{{ url('/admin/menu') }}"><i class="fa fa-bars" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.menu') }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-pages'))
                <li @if (Request::is('admin/pages*')) class="active" @endif>
                    <a href="{{ url('/admin/pages') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.pages') }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-media'))
                <li @if (Request::is('admin/media*')) class="active" @endif>
                    <a href="{{ url('/admin/media') }}"><i class="fa fa-camera" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.media') }}</span></a>
                </li>
            @endif

        </ul>
    </li>
@endif
