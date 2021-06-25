@extends('admin.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')
<style>
    #routing-map {
        height: 400px;
        /* The height is 400 pixels */
        width: 100%;
        cursor: default;
    }

    #map {
        height: 400px;
        /* The height is 400 pixels */
        width: 100%;
        /* The width is the width of the web page */
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
    integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
    crossorigin="" />

@stop

@section('breadcrumb')
<li class="active">{{ $page_title }}</li>
@stop

@section('content')
<div id="routing">
    <div id="routing-map"></div>
</div>

<div id="map"></div>
@endsection

@section("foot_page")
<script src="{{ asset("/assets/admin/vendor/graphhopper/dist/graphhopper-client.js") }}" type="text/javascript">
</script>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
    integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
    crossorigin=""></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{$maps_api_key}}&callback=initMap"></script>

<script>
    var routingMap, routingLayer;
    var mapGoogle;

    var latInit = 41.32261221364778;
    var longInit = 2.103037900893092;
    var zoomInit = 14;


    $(document).ready(function (e) {
        jQuery.support.cors = true;

        routingMap = createMap('routing-map');

        setupRoutingAPI(routingMap);

    });

    function createMap(divId) {
        var osmAttr = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

        var map = L.map(divId);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: osmAttr
        }).addTo(map);

        return map;
    }

    function initMap() {
        // The location of Uluru
        var myLatlng = {lat: latInit, lng: longInit};
        // The map, centered at Uluru
        mapGoogle = new google.maps.Map(
            document.getElementById('map'), {
                zoom: zoomInit,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
                }
            );

            mapGoogle.addListener('click', function(mapsMouseEvent) {

                var marker = new google.maps.Marker({
                    position: mapsMouseEvent.latLng,
                    map: mapGoogle
                });

                L.marker([mapsMouseEvent.latLng.lat(), mapsMouseEvent.latLng.lng()]).addTo(routingLayer);


                console.log(mapsMouseEvent.latLng.toString());
            });


    }

    function setupRoutingAPI(map) {
        map.setView([latInit, longInit], zoomInit);


        routingLayer = L.geoJson().addTo(map);
        routingLayer.options = {
            style: {color: "#00cc33", "weight": 5, "opacity": 0.6}
        };

        map.on('click', function (e) {
            L.marker(e.latlng).addTo(routingLayer);


            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(e.latlng.lat, e.latlng.lng),
                map: mapGoogle
            });

            console.log(e.latlng);
        });

    }

</script>
@stop
