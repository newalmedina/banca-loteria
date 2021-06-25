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
        map.on('click', function (e) {
            if (ghRouting.points.length > 1) {
                ghRouting.clearPoints();
                routingLayer.clearLayers();
            }

            L.marker(e.latlng, {icon: iconObject}).addTo(routingLayer);
            ghRouting.addPoint(new GHInput(e.latlng.lat, e.latlng.lng));
            console.log(e.latlng);
            if (ghRouting.points.length > 1) {
                // ******************
                //  Calculate route!
                // ******************
                ghRouting.doRequest()
                    .then(function (json) {

                        console.log(json);
                        var path = json.paths[0];
                        routingLayer.addData({
                            "type": "Feature",
                            "geometry": path.points
                        });
                        var outHtml = "Distance in meter:" + path.distance;
                        outHtml += "<br/>Times in seconds:" + path.time / 1000;
                        outHtml += "<br/><a href='" + ghRouting.getGraphHopperMapsLink() + "'>GraphHopper Maps</a>";
                        $("#routing-response").html(outHtml);

                        if (path.bbox) {
                            var minLon = path.bbox[0];
                            var minLat = path.bbox[1];
                            var maxLon = path.bbox[2];
                            var maxLat = path.bbox[3];
                            var tmpB = new L.LatLngBounds(new L.LatLng(minLat, minLon), new L.LatLng(maxLat, maxLon));
                            map.fitBounds(tmpB);
                        }

                        instructionsDiv.empty();
                        if (path.instructions) {
                            var allPoints = path.points.coordinates;
                            var listUL = $("<ol>");
                            instructionsDiv.append(listUL);
                            for (var idx in path.instructions) {
                                var instr = path.instructions[idx];

                                // use 'interval' to find the geometry (list of points) until the next instruction
                                var instruction_points = allPoints.slice(instr.interval[0], instr.interval[1]);

                                // use 'sign' to display e.g. equally named images

                                $("<li>" + instr.text + " <small>(" + ghRouting.getTurnText(instr.sign) + ")</small>"
                                    + " for " + instr.distance + "m and " + Math.round(instr.time / 1000) + "sec"
                                    + ", geometry points:" + instruction_points.length + "</li>").appendTo(listUL);
                            }
                        }

                    })
                    .catch(function (err) {
                        var str = "An error occured: " + err.message;
                        $("#routing-response").text(str);
                    });
            }
        });

        var instructionsHeader = $("#instructions-header");
        instructionsHeader.click(function () {
            instructionsDiv.toggle();
        });

        var routingLayer = L.geoJson().addTo(map);
        routingLayer.options = {
            style: {color: "#00cc33", "weight": 5, "opacity": 0.6}
        };
    }

</script>
@stop
