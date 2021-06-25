@extends('admin.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')
<style>
    /* Set the size of the div element that contains the map */
    #map {
        height: 400px;
        /* The height is 400 pixels */
        width: 100%;
        /* The width is the width of the web page */
    }
</style>
@stop

@section('breadcrumb')
<li class="active">{{ $page_title }}</li>
@stop

@section('content')
<h3>My Google Maps Demo</h3>
<!--The div element for the map -->
<div id="map"></div>

@endsection

@section("foot_page")
<!--Load the API from the specified URL
    * The async attribute allows the browser to render the page while the API loads
    * The key parameter will contain your own API key (which is not needed for this tutorial)
    * The callback parameter executes the initMap() function
    -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{$maps_api_key}}&callback=initMap"></script>
<script>
    var locations = [
      ['Location 1', 41.32261221364778, 2.103037900893092, 4],
      ['Location 2', 41.32567395163192, 2.104668683974147, 5],
      ['Location 3', 41.326076801183284, 2.099926538435817, 3],
      ['Location 4', 41.32538389841272, 2.0959139537495375, 2],
      ['Location 5', 41.32209653835895, 2.094948358504176, 1]
    ];

    // Initialize and add the map
    var map, infoWindow;
    function initMap() {
        // The location of Uluru
        var myLatlng = {lat: 41.32261221364778, lng: 2.103037900893092};
        // The map, centered at Uluru
        map = new google.maps.Map(
            document.getElementById('map'), {
                zoom: 14,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
                }
            );


        // Create the initial InfoWindow.
        // infoWindow = new google.maps.InfoWindow(
        //             {content: 'Click the map to get Lat/Lng!', position: myLatlng});
        // infoWindow.open(map);
        //infoWindow = new google.maps.InfoWindow();

        // Configure the click listener.
        map.addListener('click', function(mapsMouseEvent) {
          // Close the current InfoWindow.
          infoWindow.close();

          // Create a new InfoWindow.
          infoWindow = new google.maps.InfoWindow({position: mapsMouseEvent.latLng});
          infoWindow.setContent(mapsMouseEvent.latLng.toString());
          infoWindow.open(map);
          console.log(mapsMouseEvent.latLng.toString());
        });

        // Añadimos los markers
        var marker, i;

        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map
            });

            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infoWindow.setContent(locations[i][0]);
                    infoWindow.open(map, marker);
                }
            })(marker, i));
        }
    }


</script>
@stop
