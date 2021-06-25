<header class="main-header">
    <!-- Logo -->
    <a href="{{ route('admin') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><img src="{{ asset('/assets/admin/img/clavel_24.png') }}" alt="{{ config("app.name") }}"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><img src="{{ asset('/assets/admin/img/clavel_32.png') }}" alt="{{ config("app.name") }}">&nbsp;{{ config("app.name") }}</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" id="sidebarToggle" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @if (!Auth::guest())
                    @include('admin.includes.notifications')

                    @if (config('general.multilanguage', false))
                        <li class="dropdown tasks-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-language" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header"><h5><i class="fa fa-language" aria-hidden="true"></i> {{ trans("general/admin_lang.selecciona_idioma") }}</h5></li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        @foreach (App\Models\Idioma::active()->get() as $idioma)
                                            <li>
                                                <a href="/changelanguage/{{$idioma->code}}">
                                                    @if($idioma->code==App::getLocale())
                                                        <i class="fa fa-dot-circle-o text-green" aria-hidden="true"></i>
                                                    @else
                                                        <i class="fa fa-circle-o text-gray" aria-hidden="true"></i>
                                                    @endif
                                                    {{ $idioma->locale_name }}
                                                </a>
                                            </li>
                                        @endforeach
                                        <!-- end task item -->
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            @if(!empty(Auth::user()->userProfile->photo))
                                <img src="{{ url('admin/profile/getphoto/'.Auth::user()->userProfile->photo) }}" class="user-image" alt="User Image">
                            @else
                                <img src="{{ asset("/assets/admin/img/user.png") }}" class="user-image" alt="User Image" />
                            @endif
                            <span class="hidden-xs">{{ Auth::user()->userProfile->fullname }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                @if(!empty(Auth::user()->userProfile->photo))
                                    <img src="{{ url('admin/profile/getphoto/'.Auth::user()->userProfile->photo) }}" class="img-circle" alt="User Image">
                                @else
                                    <img src="{{ asset("/assets/admin/img/user.png") }}" class="img-circle" alt="User Image" />
                                @endif
                                <p>
                                    {{ Auth::user()->userProfile->fullname }}
                                    <small>{{ trans('general/admin_lang.miembro_desde') }} {{ Auth::user()->created_at_formatted }}</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{ url('admin/profile') }}" class="btn btn-default btn-flat">{{ trans('general/admin_lang.perfil') }}</a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{ route('admin.logout') }}" class="btn btn-default btn-flat"
                                       onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">{{ trans('general/admin_lang.desconectar') }}</a>
                                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>

                                </div>
                            </li>
                        </ul>
                    </li>

                    <!-- Control Sidebar Toggle Button -->
                    <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears" aria-hidden="true"></i></a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</header>
