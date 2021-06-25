@if(Auth::user()->can('admin-elearning'))
    <li class="treeview @if (Request::is('admin/cursos*') ||
                        Request::is('admin/certificados*') ||
                        Request::is('admin/asignaturas*')||
                        Request::is('admin/grupos*')||
                        Request::is('admin/profesor*')||
                        Request::is('admin/alumnos*')) active open @endif">
        <a href="#"><i class="fa fa-mortar-board" aria-hidden="true" hidden="true"></i>
            <span>{{ trans('elearning::general/admin_lang.elearning') }}</span> <i
                    class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
        <ul class="treeview-menu">

            @if(Auth::user()->can('admin-cursos'))
                <li @if (Request::is('admin/cursos*')) class="active" @endif>
                    <a href="{{ url('/admin/cursos') }}"><i class="fa fa-book" aria-hidden="true"></i>
                        <span>{{ trans('elearning::general/admin_lang.cursos') }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-asignaturas'))
                <li @if (Request::is('admin/asignaturas*')) class="active" @endif>
                    <a href="{{ url('/admin/asignaturas') }}"><i class="fa fa-leanpub" aria-hidden="true"></i>
                        <span>{{ trans('elearning::general/admin_lang.asignaturas') }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-grupos'))
                <li @if (Request::is('admin/grupos*')) class="active" @endif>
                    <a href="{{ url('/admin/grupos') }}"><i class="fa fa-object-ungroup" aria-hidden="true" hidden="true"></i>
                        <span>{{ trans('elearning::general/admin_lang.grupos') }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-certificados'))
                <li @if (Request::is('admin/certificados*')) class="active" @endif>
                    <a href="{{ url('/admin/certificados') }}"><i class="fa fa-certificate" hidden="true" aria-hidden="true"></i>
                        <span>{{ trans('elearning::general/admin_lang.certificados') }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('admin-profesor'))
                <li class="treeview @if (Request::is('admin/profesor*')  ||
                        Request::is('admin/alumnos*')) active open @endif" >
                    <a href="#">
                        <i class="fa fa-laptop" aria-hidden="true"></i>
                        <span>{{ trans('elearning::general/admin_lang.zona_docente') }}</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right" aria-hidden="true"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li @if (Request::is('admin/profesor*')) class="active" @endif>
                            <a href="{{ url('/admin/profesor') }}"><i class="fa fa-book" aria-hidden="true"></i>
                                <span>{{ trans('elearning::general/admin_lang.profesor') }}</span></a>
                        </li>
                        <li @if (Request::is('admin/alumnos*')) class="active" @endif>
                            <a href="{{ url('/admin/alumnos') }}"><i class="fa fa-address-book-o" aria-hidden="true"></i>
                                <span>{{ trans('elearning::general/admin_lang.alumnado') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endif

            @if(Auth::user()->can('admin-codigos'))
                <li @if (Request::is('admin/codigos*')) class="active" @endif>
                    <a href="{{ url('/admin/codigos') }}"><i
                                class="fa fa-cc" aria-hidden="true" hidden="true"></i> {{ trans('elearning::general/admin_lang.codigos') }}</a>
                </li>
            @endif

        </ul>
    </li>
@endif
