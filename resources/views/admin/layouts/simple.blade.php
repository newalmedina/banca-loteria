<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>
        @section('title')
            {{ config('app.name', '') }} ::
        @show
    </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Theme style -->
    <link href="{{ asset("/assets/admin/vendor/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/assets/admin/vendor/adminlte/css/adminlte.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/assets/admin/vendor/adminlte/css/skins/_all-skins.min.css") }}" rel="stylesheet" type="text/css" />
    {{--
    <link href="{{ asset("/assets/admin/vendor/fontawesome/css/font-awesome.min.css") }}" rel="stylesheet" type="text/css" />
    --}}
    <link href="{{ asset("/assets/admin/css/app.css") }}" rel="stylesheet" type="text/css" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

@yield('head_page')

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="hold-transition login-page">

@yield('content')

<!-- REQUIRED JS SCRIPTS -->

<!-- App -->
<script src="{{ asset("/assets/admin/vendor/jquery/js/jquery.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("/assets/admin/vendor/jquery/js/jquery-ui.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("/assets/admin/vendor/bootstrap/js/bootstrap.js") }}" type="text/javascript"></script>
<script src="{{ asset("/assets/admin/vendor/adminlte/js/adminlte.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("/assets/admin/js/app.js") }}" type="text/javascript"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->

@yield('foot_page')

</body>
</html>
