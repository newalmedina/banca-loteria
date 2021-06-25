@extends('admin.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')
<style>
    #routing-map {
        height: 480px;
        cursor: default;
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
crossorigin=""/>

@stop

@section('breadcrumb')
<li class="active">{{ $page_title }}</li>
@stop

@section('content')
<div id="routing">

    <div id="routing-response" style="float: right; padding-left: 20px;">

    </div>

    <div id="instructions-header">

        <div id="instructions" ></div>
    </div>

    <div id="routing-map"></div>


</div>

@endsection

@section("foot_page")
<script src="{{ asset("/assets/admin/vendor/graphhopper/dist/graphhopper-client.js") }}" type="text/javascript"></script>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
    integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
    crossorigin=""></script>



    <script>

        var iconObject = L.icon({
            iconUrl: '{{ asset("assets/admin/img/") }}/truck.png',
            iconSize:     [32, 32], // size of the icon
            iconAnchor: [16, 16]
        });

        $(document).ready(function (e) {
            jQuery.support.cors = true;

            var profile = "car";

            var ghRouting = new GraphHopper.Routing({
                key: "{{ $graphhopper_api_key }}",
                vehicle: profile,
                elevation: false
            });

            var routingMap = createMap('routing-map');
            setupRoutingAPI(routingMap, ghRouting);

        });

        function createMap(divId) {
            var osmAttr = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

            var map = L.map(divId);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: osmAttr
            }).addTo(map);

            return map;
        }

        function setupRoutingAPI(map, ghRouting) {
            map.setView([41.32261221364778, 2.103037900893092], 16);

            var instructionsDiv = $("#instructions");

            var instructionsHeader = $("#instructions-header");
            instructionsHeader.click(function () {
                instructionsDiv.toggle();
            });

            var routingLayer = L.geoJson().addTo(map);
            var myStyle = {
                    "color": "#ff7800",
                    "weight": 5,
                    "opacity": 0.65
                };

            routingLayer.options = {
                style: myStyle
            };

            ghRouting.clearPoints();
            routingLayer.clearLayers();


            @if(!empty($rutas))
                L.marker(
                    [
                        41.322524283701334,
                        2.1028089523315434
                    ]
                    , {icon: iconObject}).addTo(routingLayer);
                L.marker(
                    [
                        41.32555379770166,
                        2.1045255661010747
                    ]
                    , {icon: iconObject}).addTo(routingLayer);

                var myLines = [
                    {
                            "type": "Feature",
                            "geometry": {
                                "type": "{{ $rutas['points']['type'] }}",
                                "coordinates": [
                                    @foreach($rutas['points']['coordinates'] as $coordinate)
                                        [{{$coordinate[0]}}, {{$coordinate[1]}}],
                                    @endforeach
                                ]

                            }
                        }
                    ];

                routingLayer.addData(myLines);


                var outHtml = "Distance in meter:" + {{ $rutas['distance']}};
                outHtml += "<br/>Times in seconds:" + {{ $rutas['time']}} / 1000;
                $("#routing-response").html(outHtml);

                @if(!empty($rutas['bbox']))
                    var minLon = {{ $rutas['bbox'][0] }};
                    var minLat = {{ $rutas['bbox'][1] }};
                    var maxLon = {{ $rutas['bbox'][2] }};
                    var maxLat = {{ $rutas['bbox'][3] }};
                    var tmpB = new L.LatLngBounds(new L.LatLng(minLat, minLon), new L.LatLng(maxLat, maxLon));
                    map.fitBounds(tmpB);
                @endif

                instructionsDiv.empty();

                @if(!empty($rutas['instructions']))

                @endif


            @endif

        }

    </script>
@stop
