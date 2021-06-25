@extends('admin.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
    integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
    crossorigin=""/>

    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

    <style>
        #mapid { height: 380px; }
    </style>
@stop

@section('breadcrumb')
<li class="active">{{ $page_title }}</li>
@stop

@section('content')
<div id="mapid" style="display: block;  height: 100vh;"></div>
@endsection

@section("foot_page")
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
crossorigin=""></script>

<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script src="https://unpkg.com/lrm-graphhopper@1.3.0/src/L.Routing.GraphHopper.js"></script>


<script>

var locations = [
      ['Location 1', 41.32261221364778, 2.103037900893092, 4],
      ['Location 2', 41.32567395163192, 2.104668683974147, 5],
      ['Location 3', 41.326076801183284, 2.099926538435817, 3],
      ['Location 4', 41.32538389841272, 2.0959139537495375, 2],
      ['Location 5', 41.32209653835895, 2.094948358504176, 1]

    ];

//var mymap = L.map('mapid').setView([41.32261221364778, 2.103037900893092], 14);
var mymap = L.map('mapid').setView([40.48093,		-3.368649], 14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(mymap);



// L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={!! $mapbox_api_key !!}', {
//     attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
//     maxZoom: 18,
//     id: 'mapbox/streets-v11',
//     tileSize: 512,
//     zoomOffset: -1,
//     accessToken: '{!! $mapbox_api_key !!}'
// }).addTo(mymap);

// L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={!! $mapbox_api_key !!}', {
//     attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
//     maxZoom: 18,
//     id: 'mapbox/satellite-v9',
//     tileSize: 512,
//     zoomOffset: -1,
//     accessToken: '{!! $mapbox_api_key !!}'
// }).addTo(mymap);



// Añadimos los markers
var marker, i, popup;

var popup = L.popup();


var truckIcon = L.icon({
    iconUrl: '{{ asset("assets/admin/img/") }}/truck.png',

    iconSize:     [32, 32], // size of the icon
    iconAnchor:   [16, 16], // point of the icon which will correspond to marker's location
});

var vanIcon = L.icon({
    iconUrl: '{{ asset("assets/admin/img/") }}/van.png',

    iconSize:     [32, 32], // size of the icon
    iconAnchor:   [16, 16], // point of the icon which will correspond to marker's location
});

/*
for (i = 0; i < locations.length; i++) {
    marker = L.marker(
        [locations[i][1], locations[i][2]],
        {
            title:locations[i][0],
            alt:locations[i][3],
            icon: Math.round(Math.random())?truckIcon:vanIcon
        }
    );
    marker.titulo = locations[i][0];
    marker.distancia = locations[i][3];
    marker.addTo(mymap);

    marker.on('click', function(e) {
            console.log(e.latlng);
            console.log(e);
            popup
                .setLatLng(e.latlng)
                .setContent("Has clickado en el mapa  " + e.target.options.title + " - " + e.target.titulo + " - " + e.latlng.toString())
                .openOn(mymap);
        });


}
*/

var circle = L.circle([41.3227, 2.11], {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5,
    radius: 500
}).addTo(mymap);

var polygon = L.polygon([
    [41.331064573708815,2.0875740051269536],
    [41.32571493811822,2.0988178253173833],
    [41.31611027340459,2.0954704284667973],
    [41.31978470828957,2.0770168304443364],
    [41.33125792580617,2.0775318145751958]
]).addTo(mymap);

// var popup = L.popup()
//     .setLatLng([41.32261221364778, 2.103037900893092])
//     .setContent("I am a standalone popup.")
//     .openOn(mymap);

function onMapClick(e) {
    console.log(e.latlng);
    popup
        .setLatLng(e.latlng)
        .setContent("You clicked the map at " + e.latlng.toString())
        .openOn(mymap);
}

mymap.on('click', onMapClick);



var control = L.Routing.control({
  waypoints: [
    //L.latLng(41.33280472192591,2.1003627777099614),
    //L.latLng(41.3193334730579,2.0993328094482426)
      L.latLng(40.48093,		-3.368649   ,1),
      L.latLng(40.480997,	-3.367606       ,2),
      L.latLng(40.481013,	-3.366894       ,3),
      L.latLng(40.480722,	-3.366795       ,4),
      L.latLng(40.48004,		-3.366393   ,5),
      L.latLng(40.479921,	-3.366295       ,6),
      L.latLng(40.479574,	-3.367428       ,7),
      L.latLng(40.479449,	-3.367741       ,8),
      L.latLng(40.479291,	-3.368262       ,9),
      L.latLng(40.479144,	-3.369045       ,10),
      L.latLng(40.478826,	-3.370438       ,11),
      L.latLng(40.478865,	-3.37055        ,12),
      L.latLng(40.478682,	-3.371322       ,13),
     /* L.latLng(40.478781,	-3.371949),
      L.latLng(40.478829,	-3.372016),
      L.latLng(40.478756,	-3.372126),
      L.latLng(40.478757,	-3.372222),
      L.latLng(40.478726,	-3.372272),
      L.latLng(40.478804,	-3.372437),
      L.latLng(40.479021,	-3.372687),
      L.latLng(40.479115,	-3.372765),
      L.latLng(40.479259,	-3.372848),
      L.latLng(40.479542,	-3.372966),
      L.latLng(40.479572,	-3.372909),
      L.latLng(40.479607,	-3.372903),
      L.latLng(40.479617,	-3.372833),
      L.latLng(40.479754,	-3.372563),
      L.latLng(40.480211,	-3.371817),
      L.latLng(40.480802,	-3.372105),
      L.latLng(40.480848,	-3.371922),
      L.latLng(40.481077,	-3.371308),
      L.latLng(40.48183,		-3.369775),
      L.latLng(40.481885,	-3.369775),
      L.latLng(40.48202,		-3.369832),
      L.latLng(40.482104,	-3.36983),
      L.latLng(40.48217,		-3.369779),
      L.latLng(40.482222,	-3.369686),
      L.latLng(40.483798,	-3.365531),
      L.latLng(40.484191,	-3.364503),
      L.latLng(40.484806,	-3.364936),
      L.latLng(40.485104,	-3.364251),
      L.latLng(40.485078,	-3.36421),
      L.latLng(40.485083,	-3.364157),
      L.latLng(40.485115,	-3.364126),
      L.latLng(40.485155,	-3.364135),
      L.latLng(40.485914,	-3.36226),
      L.latLng(40.485855,	-3.362186),
      L.latLng(40.485794,	-3.362014),
      L.latLng(40.485656,	-3.36182),
      L.latLng(40.485189,	-3.361359),
      L.latLng(40.485157,	-3.361388),
      L.latLng(40.485123,	-3.361365),
      L.latLng(40.485055,	-3.361446),
      L.latLng(40.484823,	-3.361232),
      L.latLng(40.484207,	-3.360799),
      L.latLng(40.4844,		-3.360589),
      L.latLng(40.484587,	-3.360403),
      L.latLng(40.484643,	-3.360296),
      L.latLng(40.484975,	-3.361031),
      L.latLng(40.485152,	-3.361288),
      L.latLng(40.485183,	-3.361304),
      L.latLng(40.485189,	-3.361359),
      L.latLng(40.485656,	-3.36182),
      L.latLng(40.485923,	-3.361961),
      L.latLng(40.486005,	-3.361961),
      L.latLng(40.486204,	-3.36147),
      L.latLng(40.486513,	-3.360775),
      L.latLng(40.487237,	-3.359016),
      L.latLng(40.487235,	-3.358991),
      L.latLng(40.487554,	-3.358198),
      L.latLng(40.488117,	-3.356934),
      L.latLng(40.487549,	-3.356367),
      L.latLng(40.487046,	-3.355899),
      L.latLng(40.48648,		-3.355402),
      L.latLng(40.486417,	-3.355369),
      L.latLng(40.486565,	-3.354582),
      L.latLng(40.486882,	-3.353348),
      L.latLng(40.487275,	-3.351033),
      L.latLng(40.487246,	-3.350793),
      L.latLng(40.487101,	-3.350658),
      L.latLng(40.486961,	-3.350579),
      L.latLng(40.486938,	-3.3506, ),
      L.latLng(40.486537,	-3.350245),
      L.latLng(40.486666,	-3.34999),
      L.latLng(40.486537,	-3.350245),
      L.latLng(40.485809,	-3.349603),
      L.latLng(40.485783,	-3.349554),
      L.latLng(40.485791,	-3.349486),
      L.latLng(40.48628,		-3.348595),
      L.latLng(40.486521,	-3.34881),
      L.latLng(40.48628,		-3.348595),
      L.latLng(40.486808,	-3.347616),
      L.latLng(40.486247,	-3.347114),
      L.latLng(40.4861,		-3.347088),
      L.latLng(40.48542,		-3.348396),
      L.latLng(40.484456,	-3.347511),
      L.latLng(40.484871,	-3.346732),
      L.latLng(40.485293,	-3.34588),
      L.latLng(40.487156,	-3.342377),
      L.latLng(40.487193,	-3.342408),
      L.latLng(40.488076,	-3.340738),
      L.latLng(40.488894,	-3.339127),
      L.latLng(40.489963,	-3.34008),
      L.latLng(40.490532,	-3.339012)*/
  ],
  //router: new L.Routing.GraphHopper('{{ $graphhopper_api_key }}'),
	routeWhileDragging: false,
    show: true,
    draggableWaypoints : false,//to set draggable option to false
    addWaypoints : false //disable adding new waypoints to the existing path
}).addTo(mymap);


</script>
@stop
