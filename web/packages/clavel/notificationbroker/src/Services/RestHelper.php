<?php
namespace Clavel\NotificationBroker\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class RestHelper
 * @package Clavel\Sms
 */
class RestHelper
{
    /**
     * @var string API url
     */
    private $baseAPIUrl = '';

    private $APIFormat = 'json';

    public $config;
    public $user = '';
    public $password = '';
    public $sender = 'Clavel';


    /**
     * Broker constructor.
     *
     * @param string $clientId your client id
     * @param string $secret your secret key
     */
    public function __construct($config = [], $baseAPIUrl = '', $APIFormat = 'json')
    {
        $this->config = $config;
        $this->APIFormat = $APIFormat;
        $this->baseAPIUrl = $baseAPIUrl;

        $this->user = trim($this->config['user']);
        $this->password = trim($this->config['pwd']);
        $this->sender = trim($this->config['sender']);
    }

    /**
     * Send simple post
     * @param string $endpoint Broker endpoint
     * @param array $data data to send as POST
     * @param bool $ssl
     * @param bool $raw
     * @return object               broker response
     */
    public function post($endpoint, $data, $ssl = false, $raw = false)
    {
        $url = $this->APIFormat . '/' . $endpoint;
        return $this->sentToRestAPI($url, 'POST', $data, $ssl, $raw);
    }

    /**
     * Send simple get request
     * @param string $endpoint Broker endpoint
     * @param array $data data to send as GET parameter
     * @param boolean $withUser send with info about user
     * @param bool $ssl
     * @param bool $raw
     * @return object            broker response
     */
    public function get($endpoint, $data = [], $withUser = true, $ssl = false, $raw = false)
    {
        $url =  $this->APIFormat . '/' . $endpoint . '?' . http_build_query($data);
        return $this->sentToRestAPI($url, 'GET', [], $ssl, $raw);
    }

    /**
     * Send get without info about user
     * @param  string  $endpoint Broker endpoint
     * @param  array   $data     data to send as GET parameter
     * @return object            broker response
     */
    public function info($endpoint, $data)
    {
        return $this->get($endpoint, $data, false);
    }


    /**
     * Sent request to Broker REST API
     * @param $url
     * @param $method
     * @param array $data sent data
     * @param bool $ssl
     * @param bool $raw
     * @return object         response from Broker
     */
    private function sentToRestAPI($url, $method, $data = [], $ssl = false, $raw = false)
    {
        $url = $this->baseAPIUrl . '/' . $url;
        $defaults = [
            'headers'  => [
                'Cache-Control' => 'no-cache',
                'Accept'        => 'application/json',
                'accept-language' => 'es_ES',
                'Authorization' => 'Basic '. base64_encode($this->user . ':' . $this->password)
            ]
        ];

        return $this->send($url, $method, $data, $defaults, $ssl, $raw);
    }

    /**
     * Do an API request
     * @param string $url where to send
     * @param string $method method of request
     * @param array $data data to send in body
     * @param array $defaults http client set
     * @param bool $ssl
     * @param bool $raw
     * @return object               broker response
     */
    private function send($url, $method, $data = [], $defaults = [], $ssl = false, $raw = false)
    {
        $client = new Client();

        $method = strtolower($method);
        //$defaults['debug'] = true;
        if ($method == 'post') {
            $defaults['json'] = $data;
        }

        if (!$ssl) {
            $defaults['verify'] = false;
        }

        try {
            $request = $client
                ->$method($url, $defaults);
        } catch (ClientException $e) {
            throw new RestHelperAPIException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e->getPrevious());
        }

        if ($raw) {
            return $request->getBody()->getContents();
        }
        return json_decode($request->getBody()->getContents());
    }
}
