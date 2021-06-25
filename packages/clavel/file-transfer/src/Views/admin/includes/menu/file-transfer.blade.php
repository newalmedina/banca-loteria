@if(Auth::user()->can('admin-file-transfer'))
    <li @if (Request::is('admin/file-transfer*'))) class="active" @endif>
        <a href="{{ url('/admin/file-transfer') }}"><i class="fa fa-file-code-o" aria-hidden="true"></i> {{ trans('file-transfer::file-transfer/admin_lang.title') }}</a>
    </li>
@endif
