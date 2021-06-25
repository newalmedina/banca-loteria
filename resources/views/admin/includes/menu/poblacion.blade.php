
@if(Auth::user()->can('admin-poblacions'))
    <li @if (Request::is('admin/poblacions*')) class="active" @endif>
        <a href="{{ url('/admin/poblacions') }}"><i class="fa far fa-flag" aria-hidden="true"></i>
            <span>{{ trans('Poblacions::poblacions/admin_lang.poblacion') }}</span></a>
    </li>
@endif

