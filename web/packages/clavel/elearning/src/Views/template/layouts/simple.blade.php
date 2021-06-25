<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

    <!-- Basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>
        @section('title')
            {{ config("elearning.basicos.TITULO") }} ::
        @show
    </title>

    <meta name="author" content="Aduxia Consulting, S.L.">
    @section('metas')
        <meta name="title" content="{!! env('PROJECT_NAME') !!}">
        <meta name="description" content="{!! env('PROJECT_NAME') !!}">
        @show

                <!-- Favicon -->
        <link rel="shortcut icon" href="{{ asset("assets/front/img/") }}/favicon.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" href="{{ asset("assets/front/img/") }}/apple-touch-icon.png">

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
        <link rel="stylesheet" href="{{ asset("assets/front/css/") }}/theme.css">
        <link rel="stylesheet" href="{{ asset("assets/front/css/") }}/theme-elements.css">
        <link rel="stylesheet" href="{{ asset("assets/front/css/") }}/theme-blog.css">
        <link rel="stylesheet" href="{{ asset("assets/front/css/") }}/theme-shop.css">

        <!-- Current Page CSS -->
        @yield('head_page')

        <!-- Skin CSS -->
        <link rel="stylesheet" href="{{ asset("assets/front/css/") }}/skin-real-estate.css">

        <!-- Theme Custom CSS -->
        <link rel="stylesheet" href="{{ asset("assets/front/") }}/css/app.css">

        <!-- Head Libs -->
        <script src="{{ asset("assets/front/vendor/") }}/modernizr/modernizr.min.js"></script>
    <![endif]-->

</head>

<body class="login-page">

    @yield('content')



    @yield('foot_page')

</body>
</html>
