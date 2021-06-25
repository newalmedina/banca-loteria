<?php
namespace Clavel\Grafos\Services\GraphHopper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Clavel\Grafos\Services\GraphHopper\API\MatrixAPI;
use Clavel\Grafos\Services\GraphHopper\API\ClusterAPI;
use Clavel\Grafos\Services\GraphHopper\API\RoutingAPI;
use Clavel\Grafos\Services\GraphHopper\API\GeocodingAPI;
use Clavel\Grafos\Services\GraphHopper\API\IsochroneAPI;
use Clavel\Grafos\Services\GraphHopper\GraphHopperAPIException;
use Clavel\Grafos\Services\GraphHopper\API\RouteOptimizationAPI;

class GraphHopper
{
    /**
     * @var string API url
     */
    private $baseAPIUrl = 'https://graphhopper.com/api/';

    public $url = '';

    /**
     * @var string
     */
    private $token;

    private $baseAPIVersion = '1';
    private $APIKey;


    public $routingAPI;
    public $routeOptimizationAPI;
    public $matrixAPI;
    public $geocodingAPI;
    public $isochroneAPI;
    public $clusterAPI;

    /**
     * GraphHopper constructor.
     *
     * @param string $clientId your client id
     * @param string $secret your secret key
     */
    public function __construct($APIKey)
    {
        $this->APIKey = $APIKey;

        $this->routingAPI = new RoutingAPI($this);
        $this->routeOptimizationAPI = new RouteOptimizationAPI($this);
        $this->matrixAPI = new MatrixAPI($this);
        $this->geocodingAPI = new GeocodingAPI($this);
        $this->isochroneAPI = new IsochroneAPI($this);
        $this->clusterAPI = new ClusterAPI($this);
    }

    public function getUri()
    {
        return $this->url;
    }


    /**
     * Send simple post
     * @param  string $endpoint     Fitbit endpoint
     * @param  array $data          data to send as POST
     * @param  bool   $ssl
     * @return object               fitbit response
     */
    public function post($endpoint, $data, $ssl = false, $raw = false)
    {
        $url = $endpoint;
        $url=$url."?key=".$this->APIKey;
        return $this->sentToRestAPI($url, 'POST', $data, $ssl, $raw);
    }

    /**
     * Send simple get request
     * @param  string  $endpoint Fitbit endpoint
     * @param  array   $data     data to send as GET parameter
     * @param  bool   $ssl
     * @param  bool   $raw  devuelve la consulta en crudo o en json
     * @return object            fitbit response
     */
    public function get($endpoint, $data = [], $ssl = false, $raw = false)
    {
        // Aqui tenemos un pequeño problema y es que hablan de puntos repetidos y http_build_query
        // no acepta parametros repetidos
        $url = $endpoint . '?' . http_build_query($data);
        $url=$url."&key=".$this->APIKey;
        $url=str_replace("?&", "?", $url);

        $urlProcessed = preg_replace('/point\d{1,2}=/', 'point=', $url);
        $urlProcessed = preg_replace('/details\d{1,2}=/', 'details=', $urlProcessed);

        return $this->sentToRestAPI($urlProcessed, 'GET', [], $ssl, $raw);
    }

    /**
     * Send get without info about user
     * @param  string  $endpoint Fitbit endpoint
     * @param  array   $data     data to send as GET parameter
     * @return object            fitbit response
     */
    public function info($endpoint, $data)
    {
        return $this->get($endpoint, $data, false);
    }

    /**
     * Send delete request
     * @param  string  $endpoint Fitbit endpoint
     * @param  bool   $ssl
     * @return object            fitbit response
     */
    public function delete($endpoint, $ssl = false, $raw = false)
    {
        $url = $endpoint;
        return $this->sentToRestAPI($url, 'DELETE', [], $ssl, $raw);
    }

    /**
     * Sent request to Fitbit REST API
     * @param  [string] $url    endpoint
     * @param  [string] $method http method
     * @param  array  $data     sent data
     * @param  bool $ssl
     * @return object         response from Fitbit
     */
    private function sentToRestAPI($url, $method, $data = [], $ssl = false, $raw = false)
    {
        $url = $this->baseAPIUrl . $this->baseAPIVersion . '/' . $url;
        $defaults = [
            'headers'  => [
                'Accept'        => 'application/json',
                'accept-language' => 'es_ES',
                'Authorization' => 'Bearer ' . $this->token
            ]
        ];

        return $this->send($url, $method, $data, $defaults, $ssl, $raw);
    }

    /**
     * Do an API request
     * @param  string $url          where to send
     * @param  string $method       method of request
     * @param  array  $data         data to send in body
     * @param  array  $defaults     http client set
     * @param  bool   $ssl
     * @return object               fitbit response
     */
    private function send($url, $method, $data = [], $defaults = [], $ssl = false, $raw = false)
    {
        $this->url = $url;

        $client = new Client();

        $method = strtolower($method);

        if ($method == 'post') {
            $defaults['json'] = $data;
        }

        if (!$ssl) {
            $defaults['verify'] = false;
        }

        try {
            $request = $client
                ->$method($url, $defaults);
        } catch (RequestException $e) {
            throw new GraphHopperAPIException(
                "[{$e->getCode()}] {$e->getMessage()}",
                $e->getCode(),
                $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
            );
        }

        if ($raw) {
            return $request;
        }
        return json_decode($request->getBody()->getContents());
    }
}
