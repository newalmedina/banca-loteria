@extends('front.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
<li class="active">{{ $page_title }}</li>
@stop


@section('content')
    <div class="container">

        <div class="starter-template">
            <h1>@lang('file-transfer::front_lang.cannot-upload')</h1>
            <p class="lead">@lang('file-transfer::front_lang.cannot-upload-blocked-ip')</p>
        </div>

    </div><!-- /.container -->



@endsection

@section("foot_page")

@stop
