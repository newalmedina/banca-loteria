@extends('admin.email.plain')

@section('title')
@parent Notificación de error en la aplicación
@stop

@section('content')


    <h1>Error en la App {{env('APP_NAME')}}</h1>

    <table border="1" width="100%">
        <tr><th >Date:</th><td>{{ date('M d, Y H:iA') }}</td></tr>
        <tr><th >Route:</th><td>{{ $data['route'] }}</td></tr>
        <tr><th >Action:</th><td>{{ $data['action'] }}</td></tr>
        <tr><th >Path:</th><td>{{ $data['path'] }}</td></tr>

        <tr><th >User:</th><td>{{ $data['user'] }}</td></tr>
        <tr><th >Method:</th><td>{{ $data['method'] }}</td></tr>
        <tr><th >URI:</th><td>{{ $data['uri'] }}</td></tr>
        <tr><th >IP:</th><td>{{ $data['ip'] }}</td></tr>
        <tr><th >Referer:</th><td>{{ $data['referer'] }}</td></tr>
        <tr><th >Is secure:</th><td>{{ $data['isSecure'] }}</td></tr>
        <tr><th >Is ajax:</th><td>{{ $data['isAjax'] }}</td></tr>
        <tr><th >User agent:</th><td>{{ $data['userAgent'] }}</td></tr>
        <tr><th >Content:</th><td>{{ $data['content'] }}</td></tr>

        <tr><th >File:</th><td>{{ $data['file'] }}</td></tr>
        <tr><th >Code:</th><td>{{ $data['code'] }}</td></tr>
        <tr><th >Line:</th><td>{{ $data['line'] }}</td></tr>
        <tr><th >Message:</th><td>{{ $data['message'] }}</td></tr>
        <tr><th >Trace:</th><td>{!! $data['trace'] !!}</td></tr>


    </table>




@endsection
