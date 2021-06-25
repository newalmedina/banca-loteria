<?php
namespace Clavel\Sms;

use Clavel\Sms\lib\Broker;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class Sms
 * @package Clavel\Sms
 */
class Sms
{
    /**
     * @var string API url
     */
    private $baseAPIUrl = 'https://api.labsmobile.com';

    private $APIFormat = 'json';

    private $config;
    private $user = '';
    private $password = '';
    public $sender = 'Clavel';

    public $broker;

    /**
     * Fitbit constructor.
     *
     * @param string $clientId your client id
     * @param string $secret your secret key
     */
    public function __construct($config = [])
    {
        $this->config = $config;

        $this->user = trim($this->config['user']);
        $this->password = trim($this->config['pwd']);
        $this->sender = trim($this->config['sender']);
        $this->broker = new Broker($this);
    }

    /**
     * Send simple post
     * @param  string $endpoint     Fitbit endpoint
     * @param  array $data          data to send as POST
     * @param  bool   $ssl
     * @return object               fitbit response
     */
    public function post($endpoint, $data, $ssl = false)
    {
        $url = $this->APIFormat . '/' . $endpoint;
        return $this->sentToRestAPI($url, 'POST', $data, $ssl);
    }

    /**
     * Send simple get request
     * @param  string  $endpoint Fitbit endpoint
     * @param  array   $data     data to send as GET parameter
     * @param  boolean $withUser send with info about user
     * @param  bool   $ssl
     * @return object            fitbit response
     */
    public function get($endpoint, $data = [], $withUser = true, $ssl = false)
    {
        $url =  $this->APIFormat . '/' . $endpoint . '?' . http_build_query($data);
        return $this->sentToRestAPI($url, 'GET', [], $ssl);
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
     * Sent request to Fitbit REST API
     * @param  [string] $url    endpoint
     * @param  [string] $method http method
     * @param  array  $data     sent data
     * @param  bool $ssl
     * @return object         response from Fitbit
     */
    private function sentToRestAPI($url, $method, $data = [], $ssl = false)
    {
        $url = $this->baseAPIUrl . '/' . $url;
        $defaults = [
            'headers'  => [
                'Accept'        => 'application/json',
                'accept-language' => 'es_ES',
                'Authorization' => 'Basic '. base64_encode($this->user . ':' . $this->password)
            ]
        ];

        return $this->send($url, $method, $data, $defaults, $ssl);
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
    private function send($url, $method, $data = [], $defaults = [], $ssl = false)
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
            throw new SmsAPIException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e->getPrevious());
        }

        return json_decode($request->getBody()->getContents());
    }
}
