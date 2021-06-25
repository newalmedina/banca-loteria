@extends('admin.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
crossorigin=""/>


<style>
    #geocoding-map {
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


    <input id="geocoding_text_field" type="text" placeholder="Your address"/>
    <input id="geocoding_search_button" type="button" value="Search"/>


    <div id="geocoding-error" style="float: right; padding-left: 20px;">
    </div>

    <div id="geocoding-response" style="float: right; padding-left: 20px;">

    </div>

    <div id="instructions-header">

        <div id="instructions" ></div>
    </div>

    <div id="geocoding-map" style="cursor: default; height:550px; width: 800px;"></div>



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

            var ghGeocoding = new GraphHopper.Geocoding({
                key: "{{ $graphhopper_api_key }}",
                limit: 8,
                locale: "es" /* currently fr, en, de and it are explicitely supported */
            });

            var gcMap = createMap('geocoding-map');
            setupGeocodingAPI(gcMap, ghGeocoding);

        });

        function createMap(divId) {
            var osmAttr = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

            var map = L.map(divId);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: osmAttr
            }).addTo(map);

            return map;
        }

        function setupGeocodingAPI(map, ghGeocoding) {
            map.setView([41.32261221364778, 2.103037900893092], 16);

            var iconObject = L.icon({
                iconUrl: '{{ asset("assets/admin/img/") }}/marker-icon.png',
                shadowSize: [50, 64],
                shadowAnchor: [4, 62],
                iconAnchor: [12, 40]
            });
            var geocodingLayer = L.geoJson().addTo(map);
            geocodingLayer.options = {
                style: {color: "#00cc33", "weight": 5, "opacity": 0.6}
            };

            L.NumberedDivIcon = L.Icon.extend({
                options: {
                    iconUrl: '{{ asset("assets/admin/img/") }}/marker-icon.png',
                    iconSize: new L.Point(25, 41),
                    iconAnchor: new L.Point(13, 41),
                    popupAnchor: new L.Point(0, -33),
                    className: 'leaflet-div-icon'
                },
                createIcon: function () {
                    var div = document.createElement('div');
                    var img = this._createImg(this.options['iconUrl']);
                    var numdiv = document.createElement('div');
                    numdiv.setAttribute("class", "number");
                    numdiv.innerHTML = this.options['number'] || '';
                    div.appendChild(img);
                    div.appendChild(numdiv);
                    this._setIconStyles(div, 'icon');
                    return div;
                }
            });


            var clearGeocoding = function () {
                $("#geocoding-results").empty();
                $("#geocoding-error").empty();
                $("#geocoding-response").empty();
                geocodingLayer.clearLayers();
            };




            var mysubmit = function () {
                clearGeocoding();

                ghGeocoding.doRequest({query: textField.val()})
                    .then(function (json) {
                        var listUL = $("<ol>");
                        $("#geocoding-response").append("Locale:" + ghGeocoding.locale + "<br/>").append(listUL);
                        var minLon, minLat, maxLon, maxLat;
                        var counter = 0;
                        for (var hitIdx in json.hits) {
                            counter++;
                            var hit = json.hits[hitIdx];

                            var str = counter + ". " + dataToText(hit);
                            $("<div>" + str + "</div>").appendTo(listUL);
                            new L.Marker(hit.point, {
                                icon: new L.NumberedDivIcon({iconUrl: '{{ asset("assets/admin/img/") }}/marker-icon-green.png', number: '' + counter})
                            }).bindPopup("<div>" + str + "</div>").addTo(geocodingLayer);

                            if (!minLat || minLat > hit.point.lat)
                                minLat = hit.point.lat;
                            if (!minLon || minLon > hit.point.lng)
                                minLon = hit.point.lng;

                            if (!maxLat || maxLat < hit.point.lat)
                                maxLat = hit.point.lat;
                            if (!maxLon || maxLon < hit.point.lng)
                                maxLon = hit.point.lng;
                        }

                        if (minLat) {
                            var tmpB = new L.LatLngBounds(new L.LatLng(minLat, minLon), new L.LatLng(maxLat, maxLon));
                            map.fitBounds(tmpB);
                        }
                    })
                    .catch(function (err) {
                        $("#geocoding-error").text("An error occured: " + err.message);
                    });
            };


            map.on('click', function (e) {
                clearGeocoding();

                ghGeocoding.doRequest({point: e.latlng.lat + "," + e.latlng.lng})
                    .then(function (json) {
                        var counter = 0;
                        for (var hitIdx in json.hits) {
                            counter++;
                            var hit = json.hits[hitIdx];
                            var str = counter + ". " + dataToText(hit);
                            L.marker(hit.point, {icon: iconObject}).addTo(geocodingLayer).bindPopup(str).openPopup();

                            // only show first result for now
                            break;
                        }
                    })
                    .catch(function (err) {
                        $("#geocoding-error").text("An error occured: " + err.message);
                    });
            });

            var textField = $("#geocoding_text_field");
            textField.keypress(function (e) {
                if (e.which === 13) {
                    mysubmit();
                    return false;
                }
            });

            $("#geocoding_search_button").click(mysubmit);

            function dataToText(data) {
                var text = "";
                if (data.name)
                    text += data.name;

                if (data.postcode)
                    text = insComma(text, data.postcode);

                // make sure name won't be duplicated
                if (data.city && text.indexOf(data.city) < 0)
                    text = insComma(text, data.city);

                if (data.country && text.indexOf(data.country) < 0)
                    text = insComma(text, data.country);
                return text;
            }

            function insComma(textA, textB) {
                if (textA.length > 0)
                    return textA + ", " + textB;
                return textB;
            }




        }

    </script>
@stop
