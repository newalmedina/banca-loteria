<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/userinfo/1.1.0/userinfo.min.js"></script>

<script>

    var latitude, longitude, contry_code, city, page_title, route;

    try {
        function initialize() {

            page_title  = "{{ (isset($page_title)) ? $page_title : "No definido"  }}";
            route       = window.location.pathname;

            if (!google.loader.ClientLocation) {

                UserInfo.getInfo(function(data) {
                    latitude  = data.position.latitude;
                    longitude 	= data.position.longitude;
                    contry_code = data.country.code;
                    city 		= data.city.name;
                    save_info();
                }, function(err) {

                });
            } else {
                latitude 	= google.loader.ClientLocation.latitude;
                longitude 	= google.loader.ClientLocation.longitude;
                contry_code = google.loader.ClientLocation.address.country_code;
                city 		= google.loader.ClientLocation.address.city;

                if(city=='-') city = "Privado";
                save_info();
            }

        }

        function save_info() {
            $.ajax({
                type		: 'POST',
                url			: '{{ url("estadistica") }}',
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                data: {
                    latitude: latitude,
                    longitude: longitude,
                    contry_code: contry_code,
                    city: city,
                    page_title: page_title,
                    route: route
                },
                success		: function(data) { }
            });
        }

        google.load("maps", "3.36", { other_params: 'key={{ env("MAPS_API_KEY") }}', callback:initialize});

    } catch(e) {
    }

</script>