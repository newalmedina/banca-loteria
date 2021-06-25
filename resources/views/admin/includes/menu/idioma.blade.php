
@if(Auth::user()->can('admin-idiomas') && config('general.multilanguage', false))
    <li @if (Request::is('admin/idiomas*')) class="active" @endif>
        <a href="{{ url('/admin/idiomas') }}"><i class="fa fa-language" aria-hidden="true"></i>
            <span>{{ trans('Idiomas::idiomas/admin_lang.idioma') }}</span></a>
    </li>
@endif

