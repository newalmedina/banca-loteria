<?php

namespace Clavel\Grafos\Services\GraphHopper\API;

use Clavel\Grafos\Services\GraphHopper\API\Module;

class RouteOptimizationAPI extends Module
{
    /**
     *
     * https://graphhopper.com/api/1/route?point=51.131,12.414&point=48.224,3.867&vehicle=car&locale=de&calc_points=false&key=api_key
     *
     * @param  string $params activity log id in 'Y-m-d' format
     * @return object         fitbit response
     */
    public function get($params)
    {
        return $this->graphhopper->post('vrp', $params, false, true);
    }
}
