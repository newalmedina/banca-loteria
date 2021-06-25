@if(auth()->user()->can('admin-newsletter-gral'))
    <li class="treeview @if (Request::is('admin/newsletter*') || Request::is('admin/templates*')) active open @endif">
        <a href="#"><i class="fa fa-paper-plane" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.newsletters') }}</span> <i class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
        <ul class="treeview-menu">

            @if(auth()->user()->can('admin-templates'))
                <li @if (Request::is('admin/templates*')) class="active" @endif>
                    <a href="{{ url('admin/templates') }}">
                        <i class="fa fa-file-code-o" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.template') }}</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->can('admin-newsletter'))
                <li @if (Request::is('admin/newsletter*') && !(Request::is('admin/newsletter-lists*') || Request::is('admin/newsletter-subscribers*') || Request::is('admin/newsletter-campaigns*'))) class="active" @endif>
                    <a href="{{ route('newsletter.index') }}">
                        <i class="fa fa-paper-plane-o" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.design') }}</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->can('admin-newsletter-lists-list'))
                <li @if (Request::is('admin/newsletter-lists*')) class="active" @endif>
                    <a href="{{ route('newsletter-lists.index') }}">
                        <i class="fa fa-list-ul" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.list') }}</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->can('admin-newsletter-subscribers-list'))
                <li @if (Request::is('admin/newsletter-subscribers*')) class="active" @endif>
                    <a href="{{ route('newsletter-subscribers.index') }}">
                        <i class="fa fa-users" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.subscribers') }}</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->can('admin-newsletter-campaigns-list'))
                <li @if (Request::is('admin/newsletter-campaigns*')) class="active" @endif>
                    <a href="{{ route('newsletter-campaigns.index') }}">
                        <i class="fa fa-bullhorn" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.Campaigns') }}</span>
                    </a>
                </li>
            @endif

        </ul>
    </li>
@endif
