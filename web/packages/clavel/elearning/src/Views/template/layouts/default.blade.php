<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>

        <!-- Basic -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>
            @section('title')
                {{ config('app.name', '') }} ::
            @show
        </title>

        <meta name="author" content="Aduxia Consulting, S.L.">
        @section('metas')
            <meta name="title" content="{!! env('PROJECT_NAME') !!}">
            <meta name="description" content="{!! env('PROJECT_NAME') !!}">
        @show

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{ asset("assets/front/img/") }}/favicon.png" type="image/x-icon" />
        <link rel="apple-touch-icon" href="{{ asset("assets/front/img/") }}/favicon.png">

        <!-- Mobile Metas -->
        <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <!-- Web Fonts  -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800%7CShadows+Into+Light" rel="stylesheet" type="text/css">

        <!-- Vendor CSS -->
        <link href="{{ asset("assets/front/vendor/") }}/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="{{ asset("/assets/front/vendor/") }}/fontawesome/css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset("assets/front/vendor/") }}/animate/animate.min.css">
        <link rel="stylesheet" href="{{ asset("assets/front/vendor/") }}/simple-line-icons/css/simple-line-icons.min.css">
        <link rel="stylesheet" href="{{ asset("assets/front/vendor/") }}/owl.carousel/assets/owl.carousel.min.css">
        <link rel="stylesheet" href="{{ asset("assets/front/vendor/") }}/owl.carousel/assets/owl.theme.default.min.css">
        <link rel="stylesheet" href="{{ asset("assets/front/vendor/") }}/magnific-popup/magnific-popup.min.css">

        <!-- Theme CSS -->
        <link rel="stylesheet" href="{{ asset("assets/front/css/") }}/theme.css?dal">
        <link rel="stylesheet" href="{{ asset("assets/front/css/") }}/theme-elements.css">
        <link rel="stylesheet" href="{{ asset("assets/front/css/") }}/theme-blog.css">
        <link rel="stylesheet" href="{{ asset("assets/front/css/") }}/theme-shop.css">

        <!-- Skin CSS -->
        <link rel="stylesheet" href="{{ asset("assets/front/css/") }}/skin-real-estate.css">

        <!-- Theme Custom CSS -->
		<link rel="stylesheet" href="{{ asset("assets/front/") }}/css/app.css">

        <!-- Current Page CSS -->
        @yield('head_page')

        <!-- Head Libs -->
        <script src="{{ asset("assets/front/vendor/") }}/modernizr/modernizr.min.js"></script>

    </head>

    <body class="loading-overlay-showing" data-loading-overlay>

        <div class="loading-overlay">
            <div class="bounce-loader">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>

        <div class="body">

            <header id="header" class="header-narrow"
                    data-plugin-options="{'stickyEnabled': true, 'stickyEnableOnBoxed': true,
                    'stickyEnableOnMobile': true, 'stickyStartAt': 37,
                    'stickySetTop': '-20px', 'stickyChangeLogo': false, 'stickySetTopMenu': 20}">
                <div class="header-body background-color-primary pt-none pb-none">
                    @include("front.includes.header")

                    @include("front.includes.sidebar")

                </div>
            </header>


            <div role="main" class="main">
                @if(isset($page_title))
                    <section class="cabecera-elearning">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 titulo-elearning">
                                    <span class="titulo-elearning-1">{!! trans("elearning::general/front_lang.elearning_title") !!} </span>
                                    <span class="titulo-elearning-2">{!! trans("elearning::general/front_lang.elearning_theme") !!} </span>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="title-header sm">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <h2>{{ $page_title }}</h2>
                                    @section('certificado_cabecera')
                                    @show
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="breadcrumb">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="breadcrumb">
                                        <li><a href="{{ url("/") }}">{{ trans("elearning::home/front_lang.inicio") }}</a></li>
                                        @section('breadcrumb')
                                        @show
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                @yield('content')
            </div>

            @include("front.includes.footer")

        </div>

        <!-- Vendor -->
        <script src="{{ asset("assets/front/vendor/") }}/jquery/jquery.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/jquery.appear/jquery.appear.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/jquery.easing/jquery.easing.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/jquery-cookie/jquery-cookie.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/bootstrap/js/bootstrap.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/common/common.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/jquery.validation/jquery.validation.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/jquery.easy-pie-chart/jquery.easy-pie-chart.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/jquery.gmap/jquery.gmap.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/jquery.lazyload/jquery.lazyload.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/isotope/jquery.isotope.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/owl.carousel/owl.carousel.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/magnific-popup/jquery.magnific-popup.min.js"></script>
        <script src="{{ asset("assets/front/vendor/") }}/vide/vide.min.js"></script>

        <!-- Theme Base, Components and Settings -->
        <script src="{{ asset("assets/front/js/") }}/theme.js"></script>

        @yield('foot_page')

        <!-- Demo -->
        <script src="{{ asset("assets/front/js/") }}/aduxia-elearning.js"></script>

        <!-- Theme Custom -->
        <script src="{{ asset("assets/front/js/") }}/custom.js"></script>

        <!-- Theme Initialization Files -->
        <script src="{{ asset("assets/front/js/") }}/theme.init.js"></script>

        <script src="{{ asset("assets/front/") }}/js/app.js"></script>

        <!-- Estadísitcas de la web, si usa otro sistema, quitar-->
        @include('front.includes.foot')

    </body>
</html>
