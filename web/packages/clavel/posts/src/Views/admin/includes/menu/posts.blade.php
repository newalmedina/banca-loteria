@if(Auth::user()->can('admin-posts'))
    <li class="treeview @if (Request::is('admin/posts*')) active open @endif">
        <a href="#"><i class="fa fa-newspaper-o" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.news') }}</span> <i class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
        <ul class="treeview-menu">
            @if(Auth::user()->can('admin-posts-list'))
                <li @if (Request::is('admin/posts*') && (!Request::is('admin/posts/comments*') && !Request::is('admin/posts/tags*'))) class="active" @endif>
                    <a href="{{ url('/admin/posts') }}"><i class="fa fa-bullhorn" aria-hidden="true"></i> {{ trans('general/admin_lang.getion_news') }}</a>
                </li>
            @endif

            @if(Auth::user()->can('admin-posts-comments'))
                <li @if (Request::is('admin/posts/comments*')) class="active" @endif>
                    <a href="{{ url('/admin/posts/comments') }}"><i class="fa fa-commenting" aria-hidden="true"></i> {{ trans('general/admin_lang.comments') }}</a>
                </li>
            @endif

            @if(Auth::user()->can('admin-posts-tags'))
                <li @if (Request::is('admin/posts/tags*')) class="active" @endif>
                    <a href="{{ url('/admin/posts/tags') }}"><i class="fa fa-tags" aria-hidden="true"></i> {{ trans('general/admin_lang.tags_news') }}</a>
                </li>
            @endif

            @if(Auth::user()->can('admin-posts-stats'))
                <li @if (Request::is('admin/posts/stats*')) class="active" @endif>
                    <a href="{{ url('/admin/posts/stats') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> {{ trans('posts::admin_lang.stats_news') }}</a>
                </li>
            @endif
        </ul>
    </li>

@endif
