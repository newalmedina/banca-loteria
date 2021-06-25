@if(Auth::user()->can('admin-crud-generator'))
    <li class="treeview @if (Request::is('admin/crud-generator*')) active open @endif">
        <a href="#"><i class="fa fa-cubes" aria-hidden="true"></i> <span>{{ trans('crud-generator::general/admin_lang.title') }}</span> <i class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
        <ul class="treeview-menu">
            @if(Auth::user()->can('admin-modulos-crud'))
                <li @if (Request::is('admin/crud-generator*'))) class="active" @endif>
                    <a href="{{ url('/admin/crud-generator') }}"><i class="fa fa-cube" aria-hidden="true"></i> {{ trans('crud-generator::modules/admin_lang.title') }}</a>
                </li>
            @endif


        </ul>
    </li>

@endif
