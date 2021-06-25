

@if(Auth::user()->can('admin-translator'))
    <li @if (Request::is('admin/translator*')) class="active" @endif>
        <a href="{{ url('/admin/translator') }}"><i class="fa fa-language" aria-hidden="true"></i> <span>{{ trans('translator-manager::translator/admin_lang.menu') }}</span></a>
    </li>
@endif
