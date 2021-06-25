<footer id="footer" class="m-none pt-md custom-background-color-1">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div style="margin-bottom:5px;">
                    <img src="{{ asset("assets/front/img/inicio/logos_acreditacion.png") }}" alt="">
                </div>
                <p class="mb-md">
                    {!! trans("elearning::general/front_lang.footer_text") !!}
                    <br><br><br>
                    {{ trans("elearning::general/front_lang.footer_aviso") }}
                </p>
            </div>
            <div class="col-md-4 align-right">
                @if(Request::is('/'))
                    <p class="mb-md">{{ trans("general/front_lang.patrocinado") }}</p>
                    <img src="{{asset("assets/front/img/patrocinador/logo-chiesi.svg") }}">
                @endif
            </div>
        </div>
    </div>
    <div class="footer-copyright custom-background-color-1 pb-none" style="margin-top: 0px;">
        <div class="container">
            <div class="row pt-md pb-md">
                <div class="col-md-6 m-none">
                    <nav id="sub-menu" style="float: left;">
                        <ul>
                            <li><a href="{{ url("/pages/aviso-legal") }}">{{ trans("general/front_lang.aviso_legal") }}</a></li>
                            <li><a href="{{ url("/pages/politica-de-privacidad") }}">{{ trans("general/front_lang.politica") }}</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</footer>

@if(!Session::has("cookies") && config("elearning.basicos.TIENE_COOKIES"))

    <style>
        .politica_de_cookies {
            background-color: #f4f4f4;
            border-top: 1px solid #666;
            bottom: 0;
            font-size: 14px;
            padding: 10px;
            position: fixed;
            width: 100%;
            z-index: 99999;
        }

        .footer-bottom {
            margin-bottom: 80px;
        }
    </style>

    <div class=" swatch-white politica_de_cookies text-left">

        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    {{ trans("general/front_lang.cookies_01") }}
                    <a href="{{ url("pages/politica-de-cookies") }}">{{ trans("general/front_lang.cookies_02") }}</a>.
                </div>
                <div class="col-md-2 col-md-offset-1 text-center">
                    <a class="btn btn-primary" href="javascript:aceptar_cookies();">{{ trans("general/front_lang.cookies_03") }}</a>
                </div>
                <br clear="all">
            </div>
        </div>
    </div>

    <script>
        function aceptar_cookies() {
            jQuery.ajax({
                type    : 'GET',
                url     : '{{ url("aceptar_cookies") }}',
                success : function (data) {
                    jQuery(".politica_de_cookies").slideUp(500);
                    jQuery(".footer-bottom").css("margin-bottom", "0px");
                }
            });
        }
    </script>
@endif
