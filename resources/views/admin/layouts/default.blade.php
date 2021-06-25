<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @section('title')
            {{ config('app.name', '') }} ::
        @show
    </title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset("assets/admin/img/") }}/favicon.png" type="image/x-icon" />
    <link rel="apple-touch-icon" href="{{ asset("assets/admin/img/") }}/favicon.png">

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Theme style -->
    <link href="{{ asset("/assets/admin/vendor/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/assets/admin/vendor/select2/css/select2.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/assets/admin/vendor/adminlte/css/adminlte.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/assets/admin/vendor/adminlte/css/skins/_all-skins.min.css") }}" rel="stylesheet" type="text/css" />
    {{--
    <link href="{{ asset("/assets/admin/vendor/fontawesome/css/font-awesome.min.css") }}" rel="stylesheet" type="text/css" />
    --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.3/css/fontawesome.min.css" integrity="sha384-wESLQ85D6gbsF459vf1CiZ2+rr+CsxRY0RpiF1tLlQpDnAgg6rwdsUF1+Ics2bni" crossorigin="anonymous">
    <link href="{{ asset("/assets/admin/css/app.css") }}" rel="stylesheet" type="text/css" />

    @yield('head_page')

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="hold-transition sidebar-mini {{ Session::get('skinColor', '') }} {{ Session::get('sidebarState', '') }}">
<div class="wrapper">

    <!-- Header -->
    @include('admin.includes.header')

    <!-- Sidebar -->
    @include('admin.includes.sidebar')


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @yield('pre-content')
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                {!! $page_title_icon ?? null !!}
                {{ $page_title ?? "Page Title" }}
                <small>{!! $page_description ?? null !!}</small>
            </h1>
            <ol class="breadcrumb" >
                <li><a href="{{ route("admin") }}"><i class="fa fa-dashboard" aria-hidden="true"></i> {{ trans("general/admin_lang.home") }}</a></li>
                @section('breadcrumb')
                @show
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Your Page Content Here -->
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    @include('admin.includes.footer')

    <!-- Sidebar -->
    @include('admin.includes.sidebar-right')

</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- App -->
<script src="{{ asset("/assets/admin/vendor/jquery/js/jquery.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("/assets/admin/vendor/jquery/js/jquery-ui.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("/assets/admin/vendor/bootstrap/js/bootstrap.js") }}" type="text/javascript"></script>
<script src="{{ asset("/assets/admin/vendor/select2/js/select2.full.js") }}" type="text/javascript"></script>
<script src="{{ asset("/assets/admin/vendor/adminlte/js/adminlte.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("/assets/admin/js/app.js") }}" type="text/javascript"></script>

<script>
    $(document).ready(function () {
        $('.sidebar-menu').tree();

        $('#sidebarToggle').on('click', function() {
            $.ajax({
                url: "{{ url('admin/dashboard/savestate') }}",
                type: "POST",
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                success : function(data) {
                    return false;
                }
            });
        });

        // Add the change skin listener
        $('[data-skin]').on('click', function (e) {
            if ($(this).hasClass('knob'))
                return
            e.preventDefault()
            saveSkin($(this).data('skin'))
        })
    })

    function saveSkin(skin) {

        $.ajax({
            url: "{{ url('admin/dashboard/changeskin') }}",
            type: "POST",
            "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            data: {
                skin: skin
            },
            success : function(data) {
                return false;
            }
        });

    }
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->

@yield('foot_page')

</body>
</html>
