@extends('front.layouts.popup')

<div class="row">
    <div class="col-md-12">
        <h3 class="heading-primary mt-xl">{!! trans("Events::front_lang.listaevents") !!} <strong>{{ $date_info->format("d/m/Y") }}</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @if(count($events)>0)
            <div id="tagInfo">
                <table border="0" aria-hidden="true">
                    <tr>
                        <td class="visible-lg visible-md " style="padding-right: 10px;"><span id="number_events">{{count($events)}}</span> {{ trans("Events::front_lang.encontrados") }}:</td>
                        <td>
                            <ul class="nav nav-pills sort-source" data-sort-id="portfolio" data-option-key="filter">
                                <li id="todos" class="active btn_pill"><a href="javascript:showInfo('todos')">{{ trans("Events::front_lang.mostrartodos") }}</a></li>
                                @foreach($tags as $tag)
                                    <li id="{{ $tag->tag }}" class="btn_pill"><a href="javascript:showInfo('{{ $tag->tag }}');">{{ $tag->tag }}</a></li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                </table>
                <hr>
            </div>

            <div class="history">
                @foreach($events as $event)
                    @if($event->permission == 0 || ($event->permission==1 && Auth::user()!=null && Auth::user()->can($event->permission_name)))
                        <div class="row @foreach($event->tags as $tag) {{ $tag->tag }} @endforeach li_btn_pill" data-attr="visible">
                            <div class="thumb col-md-3">
                                @if($event->images()->count()>0)
                                    <img src="{{ $event->images[0]->path }}" class="img-responsive" alt="">
                                @else
                                    <img src="http://placehold.it/250x250" class="img-responsive" alt="">
                                @endif
                            </div>
                            <div class="featured-box col-md-9">
                                <div class="box-content">
                                    <h4 class="heading-primary" style="margin: 0px;"><strong><a href="{{ url("/events/event/".$event->url_seo) }}">{{ $event->title }}</a> </strong></h4>
                                    <p><small>{{ $event->localization }}</small></p>
                                    <p>
                                        {!! $event->lead_event !!}
                                    </p>

                                    <p><i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>&nbsp;{{ trans("Events::front_lang.date_ini") }}: {{ $event->DateStartFormatted }}&nbsp;</p>
                                    <p><i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>&nbsp;{{ trans("Events::front_lang.date_end") }}: {{ $event->DateEndFormatted }}</p>
                                    <p><i class="fa fa-user" aria-hidden="true"></i>&nbsp;{{ trans("Events::front_lang.creator") }} {{ $event->creator->userProfile->FullName }}</p>

                                    <p>
                                    @if($event->tags()->count()>0)
                                        <ul class="list list-inline list-icons" style="margin: 0px;">
                                            @foreach($event->tags as $tag)
                                                <li style="margin: 13px 0px;"><i class="fa fa-check-circle" style="color: {{ $tag->color }};" aria-hidden="true"></i> {{ $tag->tag }}&nbsp;&nbsp;</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <p>
                                        <a class="btn btn-xs btn-primary" href="{{ url("/events/event/".$event->url_seo) }}">{{ trans("Events::front_lang.readmore") }}</a>
                                        <br clear="all">
                                </div>
                            </div>
                        </div>
                        <hr>
                    @endif
                @endforeach
                <!-- Pagination -->
                {!! $events->links('front.includes.pagination') !!}
            </div>
        @else
            <div id="tagInfo" style="margin-top: 5px;">
                <span class="alternative-font">{{count($events)}} {{ trans("Events::front_lang.encontrados") }}</span>
            </div>
        @endif

    </div>

</div>

@section('foot_page')

    <script>
        function showInfo(tag) {
            $(".btn_pill").removeClass("active");
            $("#" + tag).addClass("active");
            if(tag!='todos') {
                $(".li_btn_pill").each(function() {

                    if(!$(this).hasClass(tag) && $(this).attr("data-attr")=='visible') {
                        $(this).fadeOut(500);
                        $(this).attr("data-attr",'hidden');
                    } else if($(this).hasClass(tag) && $(this).attr("data-attr")!='visible') {
                        $(this).fadeIn(500);
                        $(this).attr("data-attr",'visible');
                    }
                });
            } else {
                $(".li_btn_pill").fadeIn(500);
            }

        }
    </script>
@stop
