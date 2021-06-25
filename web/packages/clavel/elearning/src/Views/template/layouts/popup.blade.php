<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>
        @section('title')
            {{ config("elearning.basicos.TITULO") }} ::
        @show
    </title>

    @yield('head_page')

</head>

<body>

    @yield('content')
    @yield('foot_page')

    <!-- Estadísitcas de la web, si usa otro sistema, quitar-->
    @include('front.includes.foot')

</body>
</html>
