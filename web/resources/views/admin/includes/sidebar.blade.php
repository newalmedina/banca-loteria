<aside class="main-sidebar">

    <section class="sidebar">

        @if (!Auth::guest())
            <div class="user-panel">

                <div class="pull-left image">
                    @if(!empty(Auth::user()->userProfile->photo))
                        <img src="{{ url('admin/profile/getphoto/'.Auth::user()->userProfile->photo) }}"
                             class="img-circle" alt="User Image">
                    @else
                        <img src="{{ asset("/assets/admin/img/user.png") }}" class="img-circle" alt="User Image"/>
                    @endif
                </div>

                <div class="pull-left info" style="position: static;">
                    <p><a href="{{ url('admin/profile') }}">{{ Auth::user()->userProfile->fullname }}</a></p>
                    <a href="{{ url('admin/profile') }}">
                        @if(!empty(Auth::user()->online()))
                            <i class="fa fa-circle text-success" aria-hidden="true"></i>
                        @else
                            <i class="fa fa-circle text-danger" aria-hidden="true"></i>
                        @endif
                        Online
                    </a>
                </div>
            </div>
        @endif

        <ul class="sidebar-menu" data-widget="tree">

            <li class="header">{{ trans("general/admin_lang.MENU") }}</li>

            <li @if (Request::is('admin')) class="active" @endif>
                <a href="{{ url('/admin') }}"><i class="fa fa-dashboard" aria-hidden="true"></i>
                    <span>{{ trans('general/admin_lang.dashboard') }}</span></a>
            </li>

            @if(Auth::user()->can('admin-roles') || Auth::user()->can('admin-users'))
                <li class="treeview @if (Request::is('admin/users*') || Request::is('admin/roles*') || Request::is('admin/acceso*')) active open @endif">
                    <a href="#"><i class="fa fa-users" aria-hidden="true"></i> <span>{{ trans('general/admin_lang.users') }}</span> <i
                            class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
                    <ul class="treeview-menu">
                        @if(Auth::user()->can('admin-users'))
                            <li @if (Request::is('admin/users*')) class="active" @endif>
                                <a href="{{ url('/admin/users') }}"><i
                                        class="fa fa-user" aria-hidden="true"></i> {{ trans('general/admin_lang.users') }}</a>
                            </li>
                        @endif

                        @if(Auth::user()->can('admin-roles'))
                            <li @if (Request::is('admin/roles*')) class="active" @endif>
                                <a href="{{ url('/admin/roles') }}"><i
                                        class="fa fa-key" aria-hidden="true"></i> {{ trans('general/admin_lang.roles') }}</a>
                            </li>
                        @endif
                        @if(Auth::user()->can('admin-control-acceso'))
                            <li @if (Request::is('admin/acceso*')) class="active" @endif>
                                <a href="{{ url('/admin/acceso') }}"><i
                                            class="fa fa-universal-access" aria-hidden="true"></i> {{ trans('general/admin_lang.control_acceso') }}</a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif


            <?php
            // Cargamos el resto de puntos de menu
            $files = new \Illuminate\Filesystem\Filesystem;

            $menuPath = base_path('resources/views/admin/includes/menu/');

            if ($files->isDirectory($menuPath)) {
                foreach ($files->allFiles($menuPath) as $file) {
                    ?>
                    @include('admin.includes.menu.'.basename($file, ".blade.php"))
                    <?php
                }
            }
            ?>

            @if (!config("general.only_backoffice", false))
                <li>
                    <a href="{{ url('/') }}"><i class="fa fa-globe"></i>
                        <span>{{ trans('general/admin_lang.frontend') }}</span></a>
                </li>
            @endif

            <li>
                <a href="{{ route('admin.logout')  }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out" aria-hidden="true"></i> <span>{{ trans("general/admin_lang.desconectar") }}</span>
                </a>
            </li>

        </ul>

    </section>

</aside>
