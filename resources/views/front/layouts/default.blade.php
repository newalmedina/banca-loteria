<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    @section('metas')
        <meta name="title" content="{!! config('app.name', '') !!}">
        <meta name="description" content="{!! config('app.name', '') !!}">
    @show

    <title>
        @section('title')
            {{ config('app.name', '') }} ::
        @show
    </title>

    <!-- App CSS -->
    <link href="{{ asset("/assets/front/vendor/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/assets/front/vendor/fontawesome/css/font-awesome.css") }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset("/assets/front/css/app.css") }}" rel="stylesheet" type="text/css" />

    @yield('head_page')

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>


<!-- Header -->
@include('front.includes.header')

<!-- Sidebar -->
@include('front.includes.sidebar')


<!-- Page Content -->
    @yield('content')
<!-- /.container -->


<!-- Footer -->
@include('front.includes.footer')

@include('front.includes.control-sidebar')


<!-- App -->
<script src="{{ asset("/assets/front/vendor/jquery/js/jquery.js") }}" type="text/javascript"></script>
<script src="{{ asset("/assets/front/vendor/bootstrap/js/bootstrap.js") }}" type="text/javascript"></script>

<script src="{{ asset("/assets/front/js/app.js") }}" type="text/javascript"></script>


@yield('foot_page')

</body>

</html>
