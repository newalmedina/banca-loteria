@if(session()->has("original-user-suplantar"))
    <nav class="navbar navbar-inverse">
        <div class="container">
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav nav-pills">
                    <li class="hidden-xs">
                        <span class="ws-nowrap"><i class="fa fa-user-secret" aria-hidden="true"></i>
                            <a class="text-decoration-none" href="{{ url("admin/users/suplantar/revertir") }}">
                                Revertir al administrador
                            </a>
                        </span>
                    </li>
                </ul>

            </div>
        </div>
    </nav>

@endif
