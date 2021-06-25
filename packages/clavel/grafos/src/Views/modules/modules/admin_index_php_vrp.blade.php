@extends('admin.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
crossorigin=""/>


<style>
    #vrp-map {
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



    <div id="vrp-error" style="float: right; padding-left: 20px;">
    </div>

    <div id="vrp-response" style="float: right; padding-left: 20px;">

    </div>

    <div id="instructions-header">

        <div id="instructions" ></div>
    </div>

    <div id="vrp-map" style="cursor: default; height:550px; width: 800px;"></div>

    <div id="button-list" class="right">
        vehicles:
        <input id="optimize_vehicles" style="max-width: 60px" type="number" min="1" max="4" value="2"/>
        <button id="optimize_button">Optimize</button>
        <button id="vrp_clear_button">Clear</button>
    </div>

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

            var ghOptimization = new GraphHopper.Optimization({
                key: "{{ $graphhopper_api_key }}",
                vehicle: profile,
                elevation: false
            });

            var vrpMap = createMap('vrp-map');
            setupRouteOptimizationAPI(vrpMap, ghOptimization, ghRouting);


        });

        function createMap(divId) {
            var osmAttr = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

            var map = L.map(divId);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: osmAttr
            }).addTo(map);

            return map;
        }

        function setupRouteOptimizationAPI(map, ghOptimization, ghRouting) {
            map.setView([41.32261221364778, 2.103037900893092], 16);

            L.NumberedDivIcon = L.Icon.extend({
                options: {
                    iconUrl: '{{ asset("assets/admin/img/") }}/marker-icon.png',
                    number: '',
                    shadowUrl: null,
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
                },
                // you could change this to add a shadow like in the normal marker if you really wanted
                createShadow: function () {
                    return null;
                }
            });

            var addPointToMap = function (lat, lng, index) {
                index = parseInt(index);
                if (index === 0) {
                    new L.Marker([lat, lng], {
                        icon: new L.NumberedDivIcon({iconUrl: '{{ asset("assets/admin/img/") }}/marker-icon-green.png', number: '1'}),
                        bounceOnAdd: true,
                        bounceOnAddOptions: {duration: 800, height: 200}
                    }).addTo(routingLayer);
                } else {
                    new L.Marker([lat, lng], {
                        icon: new L.NumberedDivIcon({number: '' + (index + 1)}),
                        bounceOnAdd: true,
                        bounceOnAddOptions: {duration: 800, height: 200},
                    }).addTo(routingLayer);
                }
            };

            map.on('click', function (e) {
                addPointToMap(e.latlng.lat, e.latlng.lng, ghOptimization.points.length);
                ghOptimization.addPoint(new GHInput(e.latlng.lat, e.latlng.lng));
            });

            var routingLayer = L.geoJson().addTo(map);
            routingLayer.options.style = function (feature) {
                return feature.properties && feature.properties.style;
            };

            var clearMap = function () {
                ghOptimization.clear();
                routingLayer.clearLayers();
                ghRouting.clearPoints();
                $("#vrp-response").empty();
                $("#vrp-error").empty();
            };

            var optimizeRoute = function () {
                if (ghOptimization.points.length < 3) {
                    $("#vrp-response").text("At least 3 points required but was: " + ghOptimization.points.length);
                    return;
                }
                $("#vrp-response").text("Calculating ...");
                ghOptimization.doVRPRequest($("#optimize_vehicles").val())
                    .then(optimizeResponse)
                    .catch(optimizeError);
            };

            var optimizeResponse = function (json) {
                var sol = json.solution;
                if (!sol)
                    return;

                $("#vrp-response").text("Solution found for " + sol.routes.length + " vehicle(s)! "
                    + "Distance: " + Math.floor(sol.distance / 1000) + "km "
                    + ", time: " + Math.floor(sol.time / 60) + "min "
                    + ", costs: " + sol.costs);

                var no_unassigned = sol.unassigned.services.length + sol.unassigned.shipments.length;
                if (no_unassigned > 0)
                    $("#vrp-error").append("<br/>unassigned jobs: " + no_unassigned);

                routingLayer.clearLayers();
                for (var routeIndex = 0; routeIndex < sol.routes.length; routeIndex++) {
                    var route = sol.routes[routeIndex];

                    // fetch real routes from graphhopper
                    ghRouting.clearPoints();
                    var firstAdd;
                    for (var actIndex = 0; actIndex < route.activities.length; actIndex++) {
                        var add = route.activities[actIndex].address;
                        ghRouting.addPoint(new GHInput(add.lat, add.lon));

                        if (!eqAddress(firstAdd, add))
                            addPointToMap(add.lat, add.lon, actIndex);

                        if (actIndex === 0)
                            firstAdd = add;
                    }

                    var ghCallback = createGHCallback(getRouteStyle(routeIndex));

                    ghRouting.doRequest({instructions: false})
                        .then(ghCallback)
                        .catch(function (err) {
                            var str = "An error for the routing occurred: " + err.message;
                            $("#vrp-error").text(str);
                        });
                }
            };

            var optimizeError = function (err) {
                $("#vrp-response").text(" ");

                if (err.message.indexOf("Too many locations") >= 0) {
                    $("#vrp-error").empty();
                    $("#vrp-error").append(createSignupSteps());
                } else {
                    $("#vrp-error").text("An error occured: " + err.message);
                }
                console.error(err);
            };

            var eqAddress = function (add1, add2) {
                return add1 && add2
                    && Math.floor(add1.lat * 1000000) === Math.floor(add2.lat * 1000000)
                    && Math.floor(add1.lon * 1000000) === Math.floor(add2.lon * 1000000);
            };

            var createSignupSteps = function () {
                return "<div style='color:black'>To test this example <br/>"
                    + "1. <a href='https://graphhopper.com/#directions-api'>sign up for free</a>,<br/>"
                    + "2. log in and request a free standard package then <br/>"
                    + "3. copy the API key to the text field in the upper right corner<div>";
            };

            var createGHCallback = function (routeStyle) {
                return function (json) {
                    for (var pathIndex = 0; pathIndex < json.paths.length; pathIndex++) {
                        var path = json.paths[pathIndex];
                        routingLayer.addData({
                            "type": "Feature",
                            "geometry": path.points,
                            "properties": {
                                style: routeStyle
                            }
                        });
                    }
                };
            };

            var getRouteStyle = function (routeIndex) {
                var routeStyle;
                if (routeIndex === 3) {
                    routeStyle = {color: "cyan"};
                } else if (routeIndex === 2) {
                    routeStyle = {color: "black"};
                } else if (routeIndex === 1) {
                    routeStyle = {color: "green"};
                } else {
                    routeStyle = {color: "blue"};
                }

                routeStyle.weight = 5;
                routeStyle.opacity = 1;
                return routeStyle;
            };


            $("#vrp_clear_button").click(clearMap);

            $("#optimize_button").click(optimizeRoute);

            return;

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
                            $("#vrp-response").html(outHtml);

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
                            $("#vrp-response").text(str);
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
