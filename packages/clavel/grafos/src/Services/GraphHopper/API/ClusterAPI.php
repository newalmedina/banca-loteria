<?php

namespace Clavel\Grafos\Services\GraphHopper\API;

use Clavel\Grafos\Services\GraphHopper\API\Module;

class ClusterAPI extends Module
{
    /**
     *
     * https://graphhopper.com/api/1/cluster?key=[YOUR_KEY]
     *
     * @param  string $params activity log id in 'Y-m-d' format
     * @return object         fitbit response
     */
    public function get($params)
    {
        return $this->graphhopper->post('cluster', $params);
    }
}
