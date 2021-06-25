@include('front.includes.suplantacion')
<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ url ('/') }}">{{ config('app.name', '') }}</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="{{ url ('/') }}">Home</a></li>
                <li><a href="{{ url('/contactus') }}">Contacto</a></li>
                <?php
                /*
                {!! CustomMenu::render('navbar') !!}

                */
                ?>
            </ul>
            @include('front.includes.notifications')
            <ul class="nav navbar-nav navbar-right">
                @if (config('general.multilanguage', false))
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-language" aria-hidden="true"></i> {{ trans("general/front_lang.selecciona_idioma") }}<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        @foreach (App\Models\Idioma::active()->get() as $idioma)
                            <li>
                                <a href="/changelanguage/{{$idioma->code}}">
                                    @if($idioma->code==App::getLocale())
                                        <i class="fa fa-dot-circle-o text-green" aria-hidden="true"></i>
                                    @else
                                        <i class="fa fa-circle-o text-gray" aria-hidden="true"></i>
                                    @endif
                                    {{$idioma->translate(App::getLocale())->name}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>


                @endif

                @if(Auth::check())
                    @if(Auth::user()->can('admin'))
                        <li><a href="{{ url('/admin') }}">Administración</a></li>
                    @endif
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">{{ Auth::user()->userProfile->fullname }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('profile') }}">Perfil</a></li>
                            <li class="divider"></li>
                            <li>
                                <a href="{{ route('logout')  }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li><a href="{{ url('/login') }}">Login</a></li>
                    <li><a href="{{ url('/register') }}">Registrarse</a></li>



                @endif
            </ul>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </div><!--/.nav-collapse -->
    </div>
</nav>







