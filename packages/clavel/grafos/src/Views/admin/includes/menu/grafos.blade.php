@if(Auth::user()->can('admin-grafos'))
    <li @if (Request::is('admin/grafos*')) class="active" @endif>
        <a href="{{ url('/admin/grafos') }}"><i class="fa fa-connectdevelop" aria-hidden="true"></i>
            <span>{{ trans('grafos::modules/admin_lang.title') }}</span></a>
    </li>
@endif

