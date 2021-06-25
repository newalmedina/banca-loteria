@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <style>
        .wrap {
            border: solid 1px #C0C0C0;
            width: 220px;
            margin: 20px 0;
            height: 150px;
            padding: 0;
            overflow: hidden;
        }

        .iframe {
            margin-left: -112px;
            width: 1280px;
            height: 786px;
            border: 0; -ms-transform: scale(0.35);
            -moz-transform: scale(0.35);
            -o-transform: scale(0.35);
            -webkit-transform: scale(0.35);
            transform: scale(0.35);

            -ms-transform-origin: 0 0;
            -moz-transform-origin: 0 0;
            -o-transform-origin: 0 0;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
        }
    </style>
@stop

@section('content')

    <div class="container">

        <div class="page-header">
            <h3>{{ $page_title }}</h3>
        </div>

        @if(count($listado)>0)

            @foreach($listado as $campaing)

                <div class="col-sm-12 new new-big">
                    <div
                            class="@if (file_exists(storage_path("newsletter/processed/$campaing->newsletter_id.html"))) col-lg-8 @else col-lg-12 @endif"
                            onclick="window.location='{{ url("/anteriores/".$campaing->newsletter_id) }}';"
                            style="cursor:pointer;">
                        <h2>
                            {!! $campaing->name !!}
                            <small>
                                @if($campaing->newsletter->subject!='')
                                    {!! $campaing->newsletter->subject !!}
                                @else
                                    {!! $campaing->newsletter->name !!}
                                @endif
                            </small>
                        </h2>
                        <p class="date">{{ $campaing->sent_at__date_formatted }}</p>
                    </div>
                    @if (file_exists(storage_path("newsletter/processed/$campaing->newsletter_id.html")))
                        <div class="col-lg-4">
                            <div class="wrap">
                                <iframe src="{{ url("newsletter/preview/".$campaing->newsletter_id) }}" class="iframe" title=""></iframe>
                            </div>
                        </div>
                    @endif
                </div>
                <br clear="all">
                <br clear="all">

            @endforeach

            <div class="pull-right">
                {!! $listado->render() !!}
            </div>

        @else
            <p class="text-warning">{{ trans("Newsletter::front_lang.no_campana_enviada") }}</p>
        @endif
        <br clear="all">
        <br clear="all">
    </div>
@endsection

@section("foot_page")

@stop
