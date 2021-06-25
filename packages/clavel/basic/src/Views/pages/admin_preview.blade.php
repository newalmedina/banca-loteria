@extends('admin.layouts.popup')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <style>
        {!! $css !!}

        .preview_info_ribbon {
            width: 300px;
            text-align: center;
            background-color: rgba(143,149,176,0.9);
            color: #FFF;
            -webkit-transform: rotate(-45deg);
            -moz-transform: rotate(-45deg);
            -o-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            transform: rotate(-45deg);
            margin-top: 40px;
            margin-left: -90px;
            padding: 5px;
            z-index: 99999999999;
        }

        .container {
            overflow: hidden;
            z-index: 99999999999;
            height: 200px;
            margin-top: -15px;
            margin-left: -15px;
            margin-bottom: -200px;
        }

        .modal-body {
            min-height: 250px;
        }
    </style>

@stop

@section('content')

    <div class="container">
        <div class="preview_info_ribbon">
            <h4>{{ trans('basic::pages/admin_lang.preview') }}</h4>
            {{ trans("basic::pages/admin_lang.not_saved") }}
        </div>
    </div>

    <h3>{{ $page_title }}</h3>

    {!! $body !!}

@endsection

@section('foot_page')
    @if(!empty($javascript))
        <script>
            {!! $javascript !!}
        </script>
    @endif
@stop