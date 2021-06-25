<?php

namespace Clavel\Sms;

use GuzzleHttp\Exception\ClientException;

/**
 * Define a custom exception class
 */
class SmsAPIException extends ClientException
{
    private $response;
    public function __construct($message, $request, $response, $previous)
    {
        $this->response = $response->getBody()->getContents();
        $message = join('<br />', $this->getMessages());
        parent::__construct($message, $request, $response, $previous);
    }
    /**
     * Get Sms body response
     * @return Object  response object
     */
    public function getSmsResponse()
    {
        return json_decode($this->response);
    }
    /**
     * Get array of messages
     * @return array  error messages
     */
    public function getMessages()
    {
        $errors = [];
        foreach ($this->getSmsResponse()->errors as $error) {
            $errors[] = $error->message;
        }
        return $errors;
    }
    /**
     * Error codes
     * @return array  get array of error codes
     */
    public function getCodes()
    {
        $errors = [];
        foreach ($this->getSmsResponse()->errors as $error) {
            $errors[] = $error->errorType;
        }
        return $errors;
    }
}
