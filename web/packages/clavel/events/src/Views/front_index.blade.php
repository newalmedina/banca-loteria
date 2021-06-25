@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link rel="stylesheet" href="{{ asset("assets/admin/vendor/zabuto_calendar/zabuto_calendar.min.css") }}">

    <style>
        ul.history li p {
            margin: 0px;
        }

        div.zabuto_calendar .table tr td div.day {
            padding-bottom: 20px;
            padding-top: 20px;
        }

        div#popularPosts {
            overflow: hidden;
            overflow-y: auto;
            max-height: 360px;
        }

        span.badge_info {
            width: 100%;
            text-align: left;
            font-size: 10px;
            line-height: 10px;
            font-weight: normal;
            font-style: normal;
            cursor: pointer;
            white-space: normal !important;
            -ms-word-break: break-all;
            word-break: break-all;
            word-break: break-word;
            -webkit-hyphens: auto;
            -moz-hyphens: auto;
            -ms-hyphens: auto;
            hyphens: auto;
            -moz-box-shadow: 1px 1px 3px 0px #666666;
            -webkit-box-shadow: 1px 1px 3px 0px #666666;
            -o-box-shadow: 1px 1px 3px 0px #666666;
            box-shadow: 1px 1px 3px 0px #666666;
            filter: progid:DXImageTransform.Microsoft.Shadow(color=#666666, Direction=134, Strength=3);
            background-color: #777777;
            border-radius: 10px;
            padding: 3px 7px;
        }
    </style>
@stop

@section('content')
    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <h1 class="mt-4 mb-3">Events Home One
            <small>Subheading</small>
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="index.html">Home</a>
            </li>
            <li class="breadcrumb-item active">Event Home 1</li>
        </ol>


        <div class="row">
            <div class="@if(auth()->user()!=null) col-md-9 @else col-md-12 @endif">
                <div id="my-calendar"></div>
            </div>

            @if(auth()->user()!=null)
                <div class="col-md-3">
                    <div class="tabs">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#popularPosts"><i
                                            class="fa fa-heart" aria-hidden="true"></i> {{ trans("Events::front_lang.favoritos")}}</a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="popularPosts" class="tab-pane active">
                                @if($events_fav!=null)

                                    @foreach($events_fav as $event)
                                        @if($event->permission == 0 || ($event->permission==1 && auth()->user()!=null && auth()->user()->can($event->permission_name)))
                                            <br clear="all">
                                            <div class="row">
                                                <div class="col-xs-4 post-image">
                                                    <div class="img-thumbnail">
                                                        <a href="{{ url("/events/event/".$event->event->url_seo) }}">
                                                            <i style="padding:10px; font-size:30px;"
                                                               class="fa fa-calendar" aria-hidden="true" hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="col-xs-8 post-info">
                                                    <a href="{{ url("/events/event/".$event->event->url_seo) }}">{{ $event->event->title }}</a>
                                                    <div class="post-meta">
                                                        {{ $event->event->dateini_lista_formatted }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    {{ trans("Events::front_lang.no_events") }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>


        <div class="row">
            <div class="col-md-12">
                <hr class="tall">
            </div>
        </div>

        <div id="listadeevents"></div>

    </div>
    <!-- /.container -->
@endsection

@section("foot_page")
    <script src="{{ asset("assets/admin/vendor/zabuto_calendar/zabuto_calendar.min.js") }}"></script>

    <script type="application/javascript">
        var eventprint = new Array();
        var mes = '{{ (int)$date_info->format("m")  }}';
        var ano = '{{ $date_info->format("Y")  }}';

        $(document).ready(function () {

            $("#my-calendar").zabuto_calendar({
                action: function () {
                    var date = $("#" + this.id).data("date");
                    return myDateFunction(date, true);
                },
                action_nav: function () {
                    var to = $("#" + this.id).data("to");
                    mes = to.month;
                    ano = to.year;
                },
                language: "{{ app()->getLocale() }}",
                cell_border: true,
                today: true,
                weekstartson: 1,
                year: ano,
                month: mes,
                callbacks: {
                    on_nav_clicked: function () {
                        reloadCal();
                    }
                }
            });

            myDateFunction('{{ $date_info->format("Y-m-d")  }}', false);
            reloadCal();
        });

        function myDateFunction(date, moveto) {
            $("#listadeevents").load("{{ url("events/list/") }}/" + date, function () {
                comprobateNumberOfEvents();
                if (moveto) $('html,body').animate({scrollTop: $(this).offset().top}, 'slow');
            });
        }

        function reloadCal() {
            var url = "{{ url("events/month") }}";

            $.ajax({
                url: url,
                type: 'POST',
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                data: {
                    mes: mes,
                    ano: ano
                },
                success: function (data) {
                    eventprint = [];

                    for (var key in data) {
                        value = data[key];
                        if (eventprint[value.fecha] != undefined) {
                            eventprint[value.fecha].push({label: value.titulo, color: value.color});
                        } else {
                            eventprint[value.fecha] = [{label: value.titulo, color: value.color}];
                        }
                    }

                    $(".day").each(function () {
                        var str = this.id;
                        var res = str.substring(this.id.length - 14, this.id.length - 4);
                        if (eventprint[res] != undefined) {
                            for (var i = 0, len = eventprint[res].length; i < len; i++) {
                                $(this).append('<div class="events" id="my-calendar-' + res + '-events" style="margin-top:3px;"><i class="fa fa-circle visible-xs text-primary"></i><span class="badge badge_info hidden-xs" id="' + res + '-1" style="background-color:' + eventprint[res][i].color + ';">' + eventprint[res][i].label + '</span></div>');
                            }
                        }
                    });

                    return false;
                }
            });

        }

        function comprobateNumberOfEvents() {
            var i = 0;

            $(".history").children(".li_btn_pill").each(function () {
                i++;
            });

            if (i > 0) {
                $("#number_events").html(i);
            } else {
                $("#tagInfo").html('<span class="alternative-font">0 {{ trans("Events::front_lang.encontrados") }}</span>');
            }

        }

    </script>
@stop
