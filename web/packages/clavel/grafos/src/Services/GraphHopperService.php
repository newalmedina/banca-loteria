<?php

namespace Clavel\Grafos\Services;

use Clavel\Grafos\Services\GraphHopper\GraphHopper;
use Clavel\Grafos\Services\GraphHopper\GraphHopperAPIException;
use Clavel\Grafos\Services\GraphHopper\Helpers\ObjectSerializer;

class GraphHopperService
{
    protected $APIKey = "";

    /**
     * GraphHopper constructor.
     *
     * @param string $clientId your client id
     * @param string $secret your secret key
     */
    public function __construct($APIKey)
    {
        $this->APIKey = $APIKey;
    }

    public function RoutingAPI()
    {
        try {
            $gh = new GraphHopper($this->APIKey);

            // $json = '{
            //     "points": [
            //         [
            //             41.322524283701334
            //             2.1028089523315434
            //         ],
            //         [
            //             41.32555379770166
            //             2.1045255661010747
            //         ]
            //     ],
            //     "point_hints": [
            //         "Origen",
            //         "Destino"
            //     ],
            //     "snap_preventions": [
            //         "motorway",
            //         "ferry",
            //         "tunnel"
            //     ],
            //     "vehicle": "car",
            //     "locale": "es_ES",
            //     "elevation": false,
            //     "instructions": true,
            //     "calc_points": true,
            //     "details": [
            //         "street_name",
            //         "time",
            //         "distance",
            //         "max_speed"
            //     ],
            //     "debug": false,
            //     "points_encoded": false
            // }';

            $json = '{
                "points": [
                    [
                        2.102723,
                        41.3225
                    ],
                    [
                        2.104601,
                        41.325497
                    ]
                ],
                "vehicle": "car",
                "debug": false,
                "locale": "en",
                "points_encoded": false,
                "instructions": true,
                "elevation": false,
                "optimize": "true"
            }';



            $params = json_decode($json);

            // $params = [
            //     'point1' => '41.3225,2.102723',
            //     'point2' => '41.325497,2.104601',
            //     "vehicle"=> "car",
            //     "debug"=> false,
            //     "locale"=> "en",
            //     "points_encoded"=> false,
            //     "instructions"=> true,
            //     "elevation"=> false
            // ];


            $response = $gh->routingAPI->get($params);
            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new GraphHopperAPIException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $gh->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            $returnType = '\Clavel\Grafos\Services\GraphHopper\Models\RouteResponse';
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];
        } catch (GraphHopperAPIException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\RouteResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\GHError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\GHError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\GHError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\GHError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 501:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\GHError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        } catch (\Exception $ex) {
            dd($ex);
        }
    }

    public function RouteOptimizationAPI()
    {
        try {
            $gh = new GraphHopper($this->APIKey);

            // $json = '
            // {
            //     "vehicles": [
            //         {
            //             "vehicle_id": "vehicle-1",
            //             "type_id": "cargo-bike",
            //              "start_address": {
            //                 "location_id": "madrid",
            //                 "lon": -3.7025600,
            //                 "lat": 40.4165000
            //               },
            //             "max_jobs": 21
            //         },
            //         {
            //             "vehicle_id": "vehicle-2",
            //             "type_id": "cargo-bike",
            //             "start_address": {
            //                 "location_id": "madrid",
            //                 "lon": -3.7025600,
            //                 "lat": 40.4165000
            //               },
            //               "max_jobs": 18
            //         },
            //         {
            //             "vehicle_id": "vehicle-3",
            //             "type_id": "cargo-bike",
            //              "start_address": {
            //                 "location_id": "madrid",
            //                 "lon": -3.7025600,
            //                 "lat": 40.4165000
            //               },
            //             "max_jobs": 19
            //         },
            //     {
            //             "vehicle_id": "vehicle-4",
            //             "type_id": "cargo-bike",
            //              "start_address": {
            //                 "location_id": "madrid",
            //                 "lon": -3.7025600,
            //                 "lat": 40.4165000
            //               },
            //             "max_jobs": 24
            //         },
            //         {
            //             "vehicle_id": "vehicle-5",
            //             "type_id": "cargo-bike",
            //              "start_address": {
            //                 "location_id": "madrid",
            //                 "lon": -3.7025600,
            //                 "lat": 40.4165000
            //               },
            //             "max_jobs": 18
            //         }
            //     ],
            //     "vehicle_types": [
            //         {
            //             "type_id": "cargo-bike",
            //             "capacity": [
            //                 40
            //             ],
            //             "profile": "bike"
            //         }
            //     ],
            //     "services": [
            //         {
            //             "id": "s-1",
            //             "name": "visit-1",
            //             "address": {
            //                 "location_id": "location_1",
            //                 "lon": -3.3650283,
            //                 "lat": 40.4830409
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //         {
            //             "id": "s-2",
            //             "name": "visit-2",
            //             "address": {
            //                 "location_id": "location_2",
            //                 "lon": -3.3866859,
            //                 "lat": 40.4842597
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //         {
            //             "id": "s-3",
            //             "name": "visit-3",
            //             "address": {
            //                 "location_id": "location_3",
            //                 "lon": -3.3522410,
            //                 "lat": 40.5122640
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //         {
            //             "id": "s-4",
            //             "name": "visit-4",
            //             "address": {
            //                 "location_id": "location_4",
            //                 "lon": -3.3497527,
            //                 "lat": 40.4865885
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //         {
            //             "id": "s-5",
            //             "name": "visit-5",
            //             "address": {
            //                 "location_id": "location_5",
            //                 "lon": -3.3680898,
            //                 "lat": 40.4865446
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //         {
            //             "id": "s-10",
            //             "name": "visit-10",
            //             "address": {
            //                 "location_id": "location_10",
            //                 "lon": -3.3650283,
            //                 "lat": 40.4830409
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-11",
            //             "name": "visit-11",
            //             "address": {
            //                 "location_id": "location_11",
            //                 "lon": -3.3866859,
            //                 "lat": 40.4842597
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-12",
            //             "name": "visit-12",
            //             "address": {
            //                 "location_id": "location_12",
            //                 "lon": -3.352241,
            //                 "lat": 40.512264
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-13",
            //             "name": "visit-13",
            //             "address": {
            //                 "location_id": "location_13",
            //                 "lon": -3.3497527,
            //                 "lat": 40.4865885
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-14",
            //             "name": "visit-14",
            //             "address": {
            //                 "location_id": "location_14",
            //                 "lon": -3.3680898,
            //                 "lat": 40.4865446
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-15",
            //             "name": "visit-15",
            //             "address": {
            //                 "location_id": "location_15",
            //                 "lon": -3.3686562,
            //                 "lat": 40.4698479
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-16",
            //             "name": "visit-16",
            //             "address": {
            //                 "location_id": "location_16",
            //                 "lon": -3.3944852,
            //                 "lat": 40.4871011
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-17",
            //             "name": "visit-17",
            //             "address": {
            //                 "location_id": "location_17",
            //                 "lon": -3.3602939,
            //                 "lat": 40.4788593
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-18",
            //             "name": "visit-18",
            //             "address": {
            //                 "location_id": "location_18",
            //                 "lon": -3.3624893,
            //                 "lat": 40.4971027
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-19",
            //             "name": "visit-19",
            //             "address": {
            //                 "location_id": "location_19",
            //                 "lon": -3.365984,
            //                 "lat": 40.466468
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-20",
            //             "name": "visit-20",
            //             "address": {
            //                 "location_id": "location_20",
            //                 "lon": -3.3548,
            //                 "lat": 40.51038
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-21",
            //             "name": "visit-21",
            //             "address": {
            //                 "location_id": "location_21",
            //                 "lon": -3.3605837,
            //                 "lat": 40.4844049
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-22",
            //             "name": "visit-22",
            //             "address": {
            //                 "location_id": "location_22",
            //                 "lon": -3.3723317,
            //                 "lat": 40.4997163
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-23",
            //             "name": "visit-23",
            //             "address": {
            //                 "location_id": "location_23",
            //                 "lon": -3.372805,
            //                 "lat": 40.471106
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-24",
            //             "name": "visit-24",
            //             "address": {
            //                 "location_id": "location_24",
            //                 "lon": -3.3683492,
            //                 "lat": 40.4904954
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-25",
            //             "name": "visit-25",
            //             "address": {
            //                 "location_id": "location_25",
            //                 "lon": -3.367757,
            //                 "lat": 40.4993
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-26",
            //             "name": "visit-26",
            //             "address": {
            //                 "location_id": "location_26",
            //                 "lon": -3.3923821,
            //                 "lat": 40.4857611
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-27",
            //             "name": "visit-27",
            //             "address": {
            //                 "location_id": "location_27",
            //                 "lon": -3.3483627,
            //                 "lat": 40.5009755
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-28",
            //             "name": "visit-28",
            //             "address": {
            //                 "location_id": "location_28",
            //                 "lon": -3.3991589,
            //                 "lat": 40.5034443
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-29",
            //             "name": "visit-29",
            //             "address": {
            //                 "location_id": "location_29",
            //                 "lon": -3.36334,
            //                 "lat": 40.49837
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-30",
            //             "name": "visit-30",
            //             "address": {
            //                 "location_id": "location_30",
            //                 "lon": -3.3481646,
            //                 "lat": 40.4915864
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-31",
            //             "name": "visit-31",
            //             "address": {
            //                 "location_id": "location_31",
            //                 "lon": -3.365727,
            //                 "lat": 40.4674374
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-32",
            //             "name": "visit-32",
            //             "address": {
            //                 "location_id": "location_32",
            //                 "lon": -3.3780014,
            //                 "lat": 40.4769859
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-33",
            //             "name": "visit-33",
            //             "address": {
            //                 "location_id": "location_33",
            //                 "lon": -3.3961,
            //                 "lat": 40.48563
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-34",
            //             "name": "visit-34",
            //             "address": {
            //                 "location_id": "location_34",
            //                 "lon": -3.3478175,
            //                 "lat": 40.4909716
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-35",
            //             "name": "visit-35",
            //             "address": {
            //                 "location_id": "location_35",
            //                 "lon": -3.3762472,
            //                 "lat": 40.4790699
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-36",
            //             "name": "visit-36",
            //             "address": {
            //                 "location_id": "location_36",
            //                 "lon": -3.3577502,
            //                 "lat": 40.4819718
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-37",
            //             "name": "visit-37",
            //             "address": {
            //                 "location_id": "location_37",
            //                 "lon": -3.3672663,
            //                 "lat": 40.4786288
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-38",
            //             "name": "visit-38",
            //             "address": {
            //                 "location_id": "location_38",
            //                 "lon": -3.3760552,
            //                 "lat": 40.4749686
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-39",
            //             "name": "visit-39",
            //             "address": {
            //                 "location_id": "location_39",
            //                 "lon": -3.3914851,
            //                 "lat": 40.4861623
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-40",
            //             "name": "visit-40",
            //             "address": {
            //                 "location_id": "location_40",
            //                 "lon": -3.37411,
            //                 "lat": 40.5044083
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-41",
            //             "name": "visit-41",
            //             "address": {
            //                 "location_id": "location_41",
            //                 "lon": -3.3545852,
            //                 "lat": 40.4834658
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-42",
            //             "name": "visit-42",
            //             "address": {
            //                 "location_id": "location_42",
            //                 "lon": -3.3656166,
            //                 "lat": 40.501836
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-43",
            //             "name": "visit-43",
            //             "address": {
            //                 "location_id": "location_43",
            //                 "lon": -3.3741923,
            //                 "lat": 40.4782514
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-44",
            //             "name": "visit-44",
            //             "address": {
            //                 "location_id": "location_44",
            //                 "lon": -3.3504921,
            //                 "lat": 40.4963236
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-45",
            //             "name": "visit-45",
            //             "address": {
            //                 "location_id": "location_45",
            //                 "lon": -3.3472041,
            //                 "lat": 40.498176
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-46",
            //             "name": "visit-46",
            //             "address": {
            //                 "location_id": "location_46",
            //                 "lon": -3.3389912,
            //                 "lat": 40.4905281
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-47",
            //             "name": "visit-47",
            //             "address": {
            //                 "location_id": "location_47",
            //                 "lon": -3.3715831,
            //                 "lat": 40.5109224
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-48",
            //             "name": "visit-48",
            //             "address": {
            //                 "location_id": "location_48",
            //                 "lon": -3.3689903,
            //                 "lat": 40.49786
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-49",
            //             "name": "visit-49",
            //             "address": {
            //                 "location_id": "location_49",
            //                 "lon": -3.3708732,
            //                 "lat": 40.4777203
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-50",
            //             "name": "visit-50",
            //             "address": {
            //                 "location_id": "location_50",
            //                 "lon": -3.347126,
            //                 "lat": 40.497601
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-51",
            //             "name": "visit-51",
            //             "address": {
            //                 "location_id": "location_51",
            //                 "lon": -3.8525254,
            //                 "lat": 40.2635198
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-52",
            //             "name": "visit-52",
            //             "address": {
            //                 "location_id": "location_52",
            //                 "lon": -3.3661147,
            //                 "lat": 40.5099056
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-53",
            //             "name": "visit-53",
            //             "address": {
            //                 "location_id": "location_53",
            //                 "lon": -3.3697435,
            //                 "lat": 40.4988434
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-54",
            //             "name": "visit-54",
            //             "address": {
            //                 "location_id": "location_54",
            //                 "lon": -3.3673794,
            //                 "lat": 40.4898743
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-55",
            //             "name": "visit-55",
            //             "address": {
            //                 "location_id": "location_55",
            //                 "lon": -3.354253,
            //                 "lat": 40.4846641
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-56",
            //             "name": "visit-56",
            //             "address": {
            //                 "location_id": "location_56",
            //                 "lon": -3.3527582,
            //                 "lat": 40.4888591
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-57",
            //             "name": "visit-57",
            //             "address": {
            //                 "location_id": "location_57",
            //                 "lon": -3.369824,
            //                 "lat": 40.497261
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-58",
            //             "name": "visit-58",
            //             "address": {
            //                 "location_id": "location_58",
            //                 "lon": -3.3625243,
            //                 "lat": 40.4696548
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-59",
            //             "name": "visit-59",
            //             "address": {
            //                 "location_id": "location_59",
            //                 "lon": -3.3938927,
            //                 "lat": 40.4816737
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-60",
            //             "name": "visit-60",
            //             "address": {
            //                 "location_id": "location_60",
            //                 "lon": -3.3698138,
            //                 "lat": 40.4987302
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-61",
            //             "name": "visit-61",
            //             "address": {
            //                 "location_id": "location_61",
            //                 "lon": -3.361919,
            //                 "lat": 40.5035386
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-62",
            //             "name": "visit-62",
            //             "address": {
            //                 "location_id": "location_62",
            //                 "lon": -3.3624614,
            //                 "lat": 40.5020326
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-63",
            //             "name": "visit-63",
            //             "address": {
            //                 "location_id": "location_63",
            //                 "lon": -3.3793425,
            //                 "lat": 40.4776247
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-64",
            //             "name": "visit-64",
            //             "address": {
            //                 "location_id": "location_64",
            //                 "lon": -3.3690717,
            //                 "lat": 40.4814989
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-65",
            //             "name": "visit-65",
            //             "address": {
            //                 "location_id": "location_65",
            //                 "lon": -3.3640316,
            //                 "lat": 40.4924976
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-66",
            //             "name": "visit-66",
            //             "address": {
            //                 "location_id": "location_66",
            //                 "lon": -3.3714414,
            //                 "lat": 40.4701274
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-67",
            //             "name": "visit-67",
            //             "address": {
            //                 "location_id": "location_67",
            //                 "lon": -3.3642354,
            //                 "lat": 40.4864328
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-68",
            //             "name": "visit-68",
            //             "address": {
            //                 "location_id": "location_68",
            //                 "lon": -3.3614248,
            //                 "lat": 40.4902867
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-69",
            //             "name": "visit-69",
            //             "address": {
            //                 "location_id": "location_69",
            //                 "lon": -3.3764677,
            //                 "lat": 40.4950534
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-70",
            //             "name": "visit-70",
            //             "address": {
            //                 "location_id": "location_70",
            //                 "lon": -3.3688047,
            //                 "lat": 40.497576
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-71",
            //             "name": "visit-71",
            //             "address": {
            //                 "location_id": "location_71",
            //                 "lon": -3.3671809,
            //                 "lat": 40.4977737
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-72",
            //             "name": "visit-72",
            //             "address": {
            //                 "location_id": "location_72",
            //                 "lon": -3.3735653,
            //                 "lat": 40.507863
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-73",
            //             "name": "visit-73",
            //             "address": {
            //                 "location_id": "location_73",
            //                 "lon": -3.3954194,
            //                 "lat": 40.4843907
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-74",
            //             "name": "visit-74",
            //             "address": {
            //                 "location_id": "location_74",
            //                 "lon": -3.3565896,
            //                 "lat": 40.4819396
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-75",
            //             "name": "visit-75",
            //             "address": {
            //                 "location_id": "location_75",
            //                 "lon": -3.3485657,
            //                 "lat": 40.4968486
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-76",
            //             "name": "visit-76",
            //             "address": {
            //                 "location_id": "location_76",
            //                 "lon": -3.3710404,
            //                 "lat": 40.4979763
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-77",
            //             "name": "visit-77",
            //             "address": {
            //                 "location_id": "location_77",
            //                 "lon": -3.3492583,
            //                 "lat": 40.4863411
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-78",
            //             "name": "visit-78",
            //             "address": {
            //                 "location_id": "location_78",
            //                 "lon": -3.3701566,
            //                 "lat": 40.49924
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-79",
            //             "name": "visit-79",
            //             "address": {
            //                 "location_id": "location_79",
            //                 "lon": -3.3651796,
            //                 "lat": 40.4920327
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         },
            //     {
            //             "id": "s-80",
            //             "name": "visit-80",
            //             "address": {
            //                 "location_id": "location_80",
            //                 "lon": -3.3734242,
            //                 "lat": 40.5114658
            //             },
            //             "type": "service",
            //             "duration": 3000,
            //             "size": [
            //                 1
            //             ]
            //         }



            //     ],
            //     "objectives": [
            //         {
            //             "type": "min",
            //             "value": "vehicles"
            //         },
            //         {
            //             "type": "min",
            //             "value": "completion_time"
            //         }
            //     ],
            //     "configuration": {
            //         "routing": {
            //             "calc_points": true
            //         }
            //     }
            // }
            // ';

            $json = '
            {
                "vehicles": [
                    {
                        "vehicle_id": "vehicle-1",
                        "type_id": "car",
                         "start_address": {
                            "location_id": "madrid",
                            "lon": -3.7025600,
                            "lat": 40.4165000
                          },
                        "max_jobs": 21
                    }
                ],
                "vehicle_types": [
                    {
                        "type_id": "car",
                        "capacity": [
                            40
                        ],
                        "profile": "car"
                    }
                ],
                "services": [
                    {
                        "id": "s-1",
                        "name": "visit-1",
                        "address": {
                            "location_id": "location_1",
                            "lon": -3.3650283,
                            "lat": 40.4830409
                        },
                        "type": "service",
                        "duration": 3000,
                        "size": [
                            1
                        ]
                    },
                    {
                        "id": "s-2",
                        "name": "visit-2",
                        "address": {
                            "location_id": "location_2",
                            "lon": -3.3866859,
                            "lat": 40.4842597
                        },
                        "type": "service",
                        "duration": 3000,
                        "size": [
                            1
                        ]
                    },
                    {
                        "id": "s-3",
                        "name": "visit-3",
                        "address": {
                            "location_id": "location_3",
                            "lon": -3.3522410,
                            "lat": 40.5122640
                        },
                        "type": "service",
                        "duration": 3000,
                        "size": [
                            1
                        ]
                    },
                    {
                        "id": "s-4",
                        "name": "visit-4",
                        "address": {
                            "location_id": "location_4",
                            "lon": -3.3497527,
                            "lat": 40.4865885
                        },
                        "type": "service",
                        "duration": 3000,
                        "size": [
                            1
                        ]
                    },
                    {
                        "id": "s-5",
                        "name": "visit-5",
                        "address": {
                            "location_id": "location_5",
                            "lon": -3.3680898,
                            "lat": 40.4865446
                        },
                        "type": "service",
                        "duration": 3000,
                        "size": [
                            1
                        ]
                    }



                ],
                "objectives": [
                    {
                        "type": "min",
                        "value": "vehicles"
                    },
                    {
                        "type": "min",
                        "value": "completion_time"
                    }
                ],
                "configuration": {
                    "routing": {
                        "calc_points": true
                    }
                }
            }
            ';
            $params = json_decode($json);
            $response = $gh->routeOptimizationAPI->get($params);
            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new GraphHopperAPIException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $gh->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            $returnType = '\Clavel\Grafos\Services\GraphHopper\Models\Response';
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];
        } catch (GraphHopperAPIException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\BadRequest',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\InlineResponse404',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\GHError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\GHError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\GHError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 501:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Clavel\Grafos\Services\GraphHopper\Models\GHError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        } catch (\Exception $ex) {
            dd($e);
        }
    }

    public function MatrixAPI()
    {
        try {
            $gh = new GraphHopper($this->APIKey);

            $json = '{
                "points": [
                    [41.32261221364778, 2.103037900893092],
                    [41.32567395163192, 2.104668683974147],
                    [41.326076801183284, 2.099926538435817],
                    [41.32538389841272, 2.0959139537495375],
                    [41.32209653835895, 2.094948358504176]
                    ]
                }';
            $params = json_decode($json);
            $info = $gh->matrixAPI->get($params);
            dd($info);
        } catch (GraphHopperAPIException $ex) {
            dd($ex);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function GeocodingAPI()
    {
        try {
            $gh = new GraphHopper($this->APIKey);

            $params = [
                'q' => 'Carrer de Mataró, 08820, el Prat de Llobregat, Spain',
                'locale' => 'es_ES',
                'debug' => false
            ];
            $info = $gh->geocodingAPI->get($params);
            dd($info);
        } catch (GraphHopperAPIException $ex) {
            dd($ex);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function IsochroneAPI()
    {
        try {
            $gh = new GraphHopper($this->APIKey);

            $params = [
                'point' => '41.32261221364778,2.103037900893092'
            ];
            $info = $gh->isochroneAPI->get($params);
            dd($info);
        } catch (GraphHopperAPIException $ex) {
            dd($ex);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function ClusterAPI()
    {
        try {
            $gh = new GraphHopper($this->APIKey);

            $json = '{
                "configuration": {
                  "response_type": "json",
                  "routing": {
                    "profile": "car",
                    "cost_per_second": 1,
                    "cost_per_meter": 0
                  },
                  "clustering": {
                    "num_clusters": 10,
                    "max_quantity": 50,
                    "min_quantity": 30
                  }
                },
                "customers": [
                  {
                    "id": "GraphHopper GmbH",
                    "address": {
                      "lon": 11.53941,
                      "lat": 48.118434,
                      "street_hint": "Lindenschmitstraße 52"
                    },
                    "quantity": 10
                  }
                ]
              }';
            $params = json_decode($json);
            $info = $gh->clusterAPI->get($params);
            dd($info);
        } catch (GraphHopperAPIException $ex) {
            dd($ex);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
