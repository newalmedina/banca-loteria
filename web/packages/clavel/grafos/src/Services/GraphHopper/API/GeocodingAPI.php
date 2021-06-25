<?php

namespace Clavel\Grafos\Services\GraphHopper\API;

use Clavel\Grafos\Services\GraphHopper\API\Module;

class GeocodingAPI extends Module
{
    /**
     *
     * https://graphhopper.com/api/1/geocode?q=berlin&locale=de&debug=true&key=api_key
     *
     * @param  string $params activity log id in 'Y-m-d' format
     * @return object         fitbit response
     */
    public function get($params)
    {
        return $this->graphhopper->get('geocode', $params);
    }
}
