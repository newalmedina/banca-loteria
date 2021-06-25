@extends('front.layouts.printer')

@section('title')
    @parent {{ $page_title }}
@stop

@section("head_page")

    <style>
        * {
            font-family: Sans-Serif;
        }
    </style>

@stop

@section('content')


    <table border="0" cellspacing="0" cellpadding="0" width="100%" aria-hidden="true">
        <tr>
            <td valign="top">
                <h3 class="title-web" style="border-bottom: solid 1px @if($contenido->modulo->fondo!='') {{ $contenido->modulo->fondo }} @else #C0C0c0 @endif;">{{ $contenido->modulo->asignatura->titulo }}</h3>
            </td>
            <td style="text-align: right;" valign="top">
                <img src="{{ base_path() . "/public/assets/img/logo.svg" }}" class="img-responsive" alt="Logo">
            </td>
        </tr>
    </table>

    <h5 class="section-title" style="border-bottom:none; margin: 0px;">
        <div style="padding: 15px 35px; background-color: @if($contenido->modulo->fondo!='') {{ $contenido->modulo->fondo }} @else #C0C0c0 @endif; float: left; width: 100%;">{{ $contenido->modulo->nombre }} - {{ $page_title }}</div>
        <br clear="all">
    </h5>

    <div class="texto" style="margin-top: 30px">
        {!! $contenido->contenido !!}
    </div>

@stop
