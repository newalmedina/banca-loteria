
@if(Auth::user()->can('admin-pruebas'))
    <li @if (Request::is('admin/pruebas*')) class="active" @endif>
        <a href="{{ url('/admin/pruebas') }}">
            <span>{{ trans('pruebas::pruebas/admin_lang.prueba') }}</span></a>
    </li>
@endif

