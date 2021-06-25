<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>
        @section('title')
            {{ config('app.name', '') }} ::
        @show
    </title>

    <!-- App CSS -->
    <link href="{{ asset("/assets/front/css/front.min.css") }}" rel="stylesheet" type="text/css" />

    @yield('head_page')

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>


<!-- Page Content -->
    @yield('content')
<!-- /.container -->


<!-- App -->
<script src="{{ asset("/assets/js/front.js") }}" type="text/javascript"></script>



@yield('foot_page')

</body>

</html>
