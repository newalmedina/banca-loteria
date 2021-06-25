<?php

namespace Clavel\Grafos\Services\GraphHopper\API;

use Clavel\Fitbit\FitBit;
use Clavel\Grafos\Services\GraphHopper\GraphHopper;

class Module
{
    public $graphhopper;
    public function __construct(GraphHopper $graphhopper)
    {
        $this->graphhopper = $graphhopper;
    }
}
