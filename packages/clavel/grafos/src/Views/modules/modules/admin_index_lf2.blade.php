@extends('admin.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
    integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
    crossorigin=""/>

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

<script src="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.js"></script>
<link type="text/css" rel="stylesheet" href="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.css"/>




<script>


    L.mapquest.key = 'A7L4KqnOACETM6PW3QKxPkDoffxnO5KX';

    var map = L.mapquest.map('mapid', {
        center: [40.48151, -3.36903],
        layers: L.mapquest.tileLayer('map'),
        zoom: 13
    });

    L.mapquest.directions().route({
        locations: [
            //{ latLng: { lat: 38.895211, lng: -77.036495 } },
            //{ street: '935 pennsylvania ave', city: 'washington', state: 'dc' }
           // { latLng: { lat: 40.41649, lng:-3.7023} },
            { latLng: { lat: 40.48151, lng:-3.36903} },
            { latLng: { lat: 40.48304, lng:-3.36503} },
            { latLng: { lat: 40.4844, lng:-3.36059} },
            { latLng: { lat: 40.4866, lng:-3.34973} },
            { latLng: { lat: 40.48637, lng:-3.34922} },
            { latLng: { lat: 40.49053, lng:-3.33901} },
            { latLng: { lat: 40.49635, lng:-3.35045} },
            { latLng: { lat: 40.49053, lng:-3.33901} },
            { latLng: { lat: 40.49685, lng:-3.34857} },
            { latLng: { lat: 40.49758, lng:-3.34712} },
            { latLng: { lat: 40.49817, lng:-3.34722} },
            { latLng: { lat: 40.50097, lng:-3.34837} },
            { latLng: { lat: 40.49027, lng:-3.36145} },
            { latLng: { lat: 40.48656, lng:-3.36378} },
            { latLng: { lat: 40.47906, lng:-3.37625} }
        ]
    });


</script>
@stop
