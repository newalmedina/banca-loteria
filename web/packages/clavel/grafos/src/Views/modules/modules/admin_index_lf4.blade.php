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
        #mapid { height: 580px; }
    </style>
@stop

@section('breadcrumb')
<li class="active">{{ $page_title }}</li>
@stop

@section('content')
<div id="mapid"></div>
@endsection

@section("foot_page")
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
crossorigin=""></script>

<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script src="https://unpkg.com/lrm-graphhopper@1.3.0/src/L.Routing.GraphHopper.js"></script>


<script>

var mymap = L.map('mapid').setView([41.32261221364778, 2.103037900893092], 14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(mymap);



var control = L.Routing.control({
  waypoints: [
      @foreach($rutas as $waipont)
        L.latLng({{$waipont[0]}},{{$waipont[1]}},'{{ $loop->iteration }}'),
      @endforeach
  ],
  //router: new L.Routing.GraphHopper('{{ $graphhopper_api_key }}'),
	routeWhileDragging: false,
    show: true,
    draggableWaypoints : false,//to set draggable option to false
    addWaypoints : false //disable adding new waypoints to the existing path
}).addTo(mymap);


</script>
@stop
