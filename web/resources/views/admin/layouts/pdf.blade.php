<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>
        @section('title')
            {{ env("PROJECT_NAME") }} ::
        @show
    </title>
    @yield('head_page')
</head>

<body>

@yield('content')


</body>
</html>