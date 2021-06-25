@extends('front.email.default')

@section('content')

    <head>

        <meta charset="utf-8"/>
        <title>
            @section('title')
                {{config('app.name','')}}::
            @show
        </title>

        <style>
            body {
                background:#f1f1f1;
            }

            .portlet {
                background-color: #ffffff;
                border-style: none solid solid;
                border-width: 0 1px 1px;
                border-color: #D1B888;
            }

            .portlet-title {
                background-color: #D1B888;
                border: 0 none;
                color: #ffffff;
                font-weight: 400;
                padding: 10px 15px;
                font-family: Arial, sans-serif;
                font-size: 14px;
            }

            .portlet-body {
                background-color: #fff;
                padding: 10px;
                font-family: Arial, sans-serif;
                font-size: 14px;
                text-align: center;
            }
        </style>
    </head>

    <body>

    <table border='0' cellpadding='0' cellspacing='0' class='portlet' width='940' align='center' aria-hidden="true">
        <tr height='1'>
            <td width='100%' class="portlet-title"><strong>{{ config('app.name', '') }}</strong></td>
        </tr>
        <tr>
            <td class='portlet-body'>
                <body>

                <table border='0' cellpadding='0' cellspacing='0' width='940' align='center' aria-hidden="true">
                    <tr>
                        <strong>{{ trans("elearning::foro/front_lang.edit_foro") }}</strong><br><br>

                        Los siguientes datos son:<br><br>
                        Nombre: {{$user}}<br>
                        Email: {{$email}}<br><br>

                        Con el siguiente mensaje:<br>
                        {!! $msn !!}<br><br>

                    </tr>
                </table>
                </body>
            </td>
        </tr>
    </table>

    </body>

@endsection
