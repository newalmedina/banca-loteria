@extends('admin.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
crossorigin=""/>


<style>
    #isochrone-map {
        height: 480px;
        cursor: default;
    }

    .leaflet-div-icon {
        background: transparent;
        border: none;
    }

    .leaflet-marker-icon .number{
        position: relative;
        top: -37px;
        font-size: 12px;
        width: 25px;
        text-align: center;
    }
</style>
@stop

@section('breadcrumb')
<li class="active">{{ $page_title }}</li>
@stop

@section('content')



<div id="optimization">

    <div id="isochrone-error" style="float: right; padding-left: 20px;">
    </div>

    <div id="isochrone-response" style="float: right; padding-left: 20px;">

    </div>

    <div id="instructions-header">

        <div id="instructions" ></div>
    </div>

    <div id="isochrone-map" style="cursor: default; height:550px; width: 800px;"></div>



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

            var ghIsochrone = new GraphHopper.Isochrone({
                key: "{{ $graphhopper_api_key }}",
                vehicle: profile,
                time_limit: 0,

                locale: "es" /* currently fr, en, de and it are explicitely supported */
            });


            var isochroneMap = createMap('isochrone-map');
            setupIsochrone(isochroneMap, ghIsochrone);

        });

        function createMap(divId) {
            var osmAttr = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

            var map = L.map(divId);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: osmAttr
            }).addTo(map);

            return map;
        }

        function setupIsochrone(map, ghIsochrone) {
            map.setView([41.32261221364778, 2.103037900893092], 16);

            var isochroneLayer;
            var inprogress = false;

            map.on('click', function (e) {
                var pointStr = e.latlng.lat + "," + e.latlng.lng;

                if (!inprogress) {
                    inprogress = true;
                    $('#isochrone-response').text("Calculating ...");
                    ghIsochrone.doRequest({point: pointStr, buckets: 2})
                        .then(function (json) {
                            if (isochroneLayer)
                                isochroneLayer.clearLayers();

                            isochroneLayer = L.geoJson(json.polygons, {
                                style: function (feature) {
                                    var num = feature.properties.bucket;
                                    var color = (num % 2 === 0) ? "#00cc33" : "blue";
                                    return {color: color, "weight": num + 2, "opacity": 0.6};
                                }
                            });

                            map.addLayer(isochroneLayer);

                            $('#isochrone-response').text("Calculation done");
                            inprogress = false;
                        })
                        .catch(function (err) {
                            inprogress = false;
                            $('#isochrone-response').text("An error occured: " + err.message);
                        })
                    ;
                } else {
                    $('#isochrone-response').text("Please wait. Calculation in progress ...");
                }
            });



        }

    </script>
@stop
