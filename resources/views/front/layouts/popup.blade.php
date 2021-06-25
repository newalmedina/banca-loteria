<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>
        @section('title')
            {{ config('app.name', '') }} ::
        @show
    </title>

    @yield('head_page')

</head>

<body>

@yield('content')
@yield('foot_page')



</body>
</html>