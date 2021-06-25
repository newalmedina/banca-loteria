
@if(Auth::user()->can('admin-pais'))
    <li @if (Request::is('admin/pais*')) class="active" @endif>
        <a href="{{ url('/admin/pais') }}"><i class="fa fas fa-globe-africa" aria-hidden="true"></i>
            <span>{{ trans('Pais::pais/admin_lang.pais') }}</span></a>
    </li>
@endif

