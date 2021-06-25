<?php

namespace Clavel\Grafos\Services\GraphHopper\API;

use Clavel\Grafos\Services\GraphHopper\API\Module;

class IsochroneAPI extends Module
{
    /**
     *
     * https://graphhopper.com/api/1/isochrone?point=51.131108,12.414551&key=[YOUR_KEY]
     *
     * @param  string $params activity log id in 'Y-m-d' format
     * @return object         fitbit response
     */
    public function get($params)
    {
        return $this->graphhopper->get('isochrone', $params);
    }
}
