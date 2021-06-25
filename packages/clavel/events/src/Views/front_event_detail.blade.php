@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <style>
        #spinner {
            background: rgba(0,0,0,0.5);
            position: absolute;
            padding: 50px;
            text-align: center;
            display: none;
            z-index: 9999999;
        }
    </style>
@stop

@section('content')
    <div id="spinner" class="overlay"><i class="fa fa-refresh fa-spin" style="font-size: 124px;" aria-hidden="true"></i></div>
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="portfolio-title">
                    <div class="row">
                        <div class="portfolio-nav-all col-md-1">
                            <a href="{{ url("/events") }}" data-tooltip data-original-title="{{ trans("Events::front_lang.volver") }}"><i class="fa fa-th" style="font-size: 32px; margin-top: 15px;" aria-hidden="true"></i></a>
                        </div>
                        <div class="col-md-10">
                            <h2 class="text-center">{{ $event->title }}</h2>
                        </div>
                        <div class="portfolio-nav col-md-1">
                            @if($event->eventPrevious()>0)
                                <a href="{{ url("/events/detalle/previo/".$event->dateini."/".$event->id) }}" class="portfolio-nav-prev" data-tooltip data-original-title="{{ trans("Events::front_lang.anterior")  }}"><i class="fa fa-chevron-left" style="font-size: 32px; margin-top: 15px;" aria-hidden="true"></i></a>
                            @else
                                <i class="fa fa-chevron-left" style="font-size: 32px; margin-top: 15px;margin-right: 2px;" aria-hidden="true"></i>
                            @endif
                            @if($event->eventNext()>0)
                                <a href="{{ url("/events/detalle/siguiente/".$event->dateini."/".$event->id) }}" class="portfolio-nav-next" data-tooltip data-original-title="{{ trans("Events::front_lang.siguiente")  }}"><i class="fa fa-chevron-right" style="font-size: 32px; margin-top: 15px;" aria-hidden="true"></i></a>
                            @else
                                <i class="fa fa-chevron-right" style="font-size: 32px; margin-top: 15px;margin-left: 2px;" aria-hidden="true"></i>
                            @endif
                        </div>
                    </div>
                </div>
                <hr class="tall">
            </div>
        </div>

        <div class="row">

            @if($event->images()->count()>0)
                <div class="col-md-4">
                    <div class="post-image">
                        <div id="carousel-images-{{ $event->id }}" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner" role="listbox">
                                <?php $nX=1; ?>
                                @foreach($event->images as $imagen)
                                    <div class="item @if($nX==1) active @endif">
                                        <img class="img-responsive" src="{{ $imagen->path }}" alt="">
                                    </div>
                                    <?php $nX++; ?>
                                @endforeach
                            </div>
                            <a class="left carousel-control" href="#carousel-images-{{ $event->id }}" role="button" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                <span class="sr-only"></span>
                            </a>
                            <a class="right carousel-control" href="#carousel-images-{{ $event->id }}" role="button" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                <span class="sr-only"></span>
                            </a>
                        </div>
                    </div>
                </div>

            @endif

            <div class="@if($event->images()->count()>0) col-md-8 @else col-md-12 @endif">
                <div class="portfolio-info">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            @if(auth()->check())
                                <a id="heart_fav" href="javascript:set_favorite();" data-tooltip data-original-title="{{ trans("Events::front_lang.Like") }}" data-value="@if($event->favourites()->hasFavorite()->count()>0) 1 @else 0 @endif">
                                    <i class="fa fa-heart" aria-hidden="true" @if($event->favourites()->hasFavorite()->count()>0) style="color:#e53f51;" @endif></i>
                                    <span id="data-inc">{{ $event->favourites()->count() }}</span>
                                </a>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                            @endif
                            <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>&nbsp;{{ trans("Events::front_lang.date_ini") }}: {{ $event->dateini_formatted }}
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                            <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>&nbsp;{{ trans("Events::front_lang.date_end") }}: {{ $event->dateend_formatted }}

                        </div>
                    </div>
                </div>

                <h4 class="heading-primary">{!! trans("Events::front_lang.descripcion") !!}</h4>
                <p class="mt-xlg">{!! $event->body !!}</p>

                @if($event->enlace!='')
                    <br clear="all">
                    <a href="{{ $event->enlace }}" class="btn btn-primary btn-icon" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i>{{ trans("Events::front_lang.ir_a") }}</a>
                @endif


                @if($event->tags()->count()>0)
                    <p>
                    <p><strong>{{ trans("Events::front_lang.skills") }}:</strong></p>

                    <ul class="list list-inline list-icons">
                        @foreach($event->tags as $tag)
                            <li><i class="fa fa-check-circle" style="color: {{ $tag->color }};" aria-hidden="true"></i> {{ $tag->tag }}&nbsp;&nbsp;</li>
                        @endforeach
                    </ul>
                    </p>
                @endif
                @if($event->localization!='')
                    <p>
                    <p><strong>{{ trans("Events::front_lang.localization") }}:</strong></p>
                    <p>{{ $event->localization }}</p>
                    </p>
                @endif
                @if($event->has_shared=='1')
                    <p>
                    <p><strong>{!! trans("Events::front_lang.share") !!}:</strong></p>
                    <p>
                        <!-- AddThis Button BEGIN -->
                    <div class="addthis_toolbox addthis_default_style  addthis_20x20_style">
                        <a class="addthis_button_twitter"></a>
                        <a class="addthis_button_facebook"></a>
                        <a class="addthis_button_google_plusone_share"></a>
                        <a class="addthis_button_meneame"></a>
                        <a class="addthis_button_tuenti"></a>
                        <a class="addthis_button_pocket"></a>
                        <a class="addthis_button_tumblr"></a>
                        <a class="addthis_button_linkedin"></a>
                        <a class="addthis_button_whatsapp"></a>
                        <a class="addthis_button_pinterest_share"></a>
                        <a class="addthis_button_more"></a>
                    </div>
                    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5669460febf78224" async="async"></script>
                    <!-- AddThis Button END -->

                    </p>
                    </p>
                @endif


            </div>
        </div>

        @if(count($event->eventsRelated())>0)

            <div class="row">
                <div class="col-md-12">

                    <hr class="tall">

                    <h4 class="mb-md text-uppercase">{!! trans("Events::front_lang.relacionados") !!}</h4>

                    <div class="row">


                        @foreach($event->eventsRelated() as $event_relacionado)
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="panel panel-primary">

                                    <div class="panel-body" style="padding: 0px;">
                                        <a href="{{ url("events/detalle/".$event_relacionado->url_amigable) }}">
                                            <span class="thumb-info">
                                                <span class="thumb-info-wrapper">
                                                    @if($event_relacionado->images()->count()>0)
                                                        <img src="{{ $event_relacionado->images[0]->path }}" class="img-responsive" alt="">
                                                    @else
                                                        <img src="{{ asset("assets/img/boxed-bg.jpg") }}" class="img-responsive" alt="">
                                                    @endif
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="panel-footer">
                                        <h3 class="panel-title">{{ $event_relacionado->title }}</h3>
                                        <span class="thumb-info-title">
                                                        <span class="thumb-info-type">
                                                            @foreach($event_relacionado->tags as $key=>$tag)
                                                                @if($key!=0),&nbsp;@endif
                                                                {{ $tag->tag }}
                                                            @endforeach
                                                        </span>
                                                    </span><br>
                                        <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>&nbsp;{{ trans("Events::front_lang.date_ini") }}: {{ $event_relacionado->dateini_formatted }}
                                        <br>
                                        <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>&nbsp;{{ trans("Events::front_lang.date_end") }}: {{ $event_relacionado->dateend_formatted }}
                                    </div>
                                </div>
                            </div>
                        @endforeach


                    </div>
                </div>
            </div>

        @endif

    </div>

@endsection

@section("foot_page")
    <script>

        function set_favorite(){

            var url = "{{ url('events/favorito') }}";
            var inc = parseInt($("#data-inc").html());
            var att = parseInt($("#heart_fav").attr("data-value"));

            $.ajax({
                url     : url,
                type    : 'POST',
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                data: {
                    set_fav: att,
                    id: "{{ $event->id  }}"
                },
                success : function(data) {
                    $("#spinner").fadeOut(500);

                    if(att=='1') {
                        $("#heart_fav").children("i").css("color", "#b1b1b1");
                        $("#heart_fav").attr("data-value", '0');
                        $("#data-inc").html(inc-1);
                    } else {
                        $("#heart_fav").children("i").css("color", "#e53f51");
                        $("#heart_fav").attr("data-value", '1');
                        $("#data-inc").html(inc+1);
                    }

                    return false;
                }
            });


        }
    </script>
@stop
