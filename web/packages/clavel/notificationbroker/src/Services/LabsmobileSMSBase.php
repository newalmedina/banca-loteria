<?php


namespace Clavel\NotificationBroker\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class LabsmobileSMSBase extends NotificationFactory implements NotificationSmsInterface
{
    protected $config = null;
    public $user = '';
    public $password = '';
    public $sender = 'Clavel';

    protected $sms = null;

    public function __construct()
    {
        $this->config = Config::get('notificationbroker.brokers.labsmobile');
        $this->user = trim($this->config['user']);
        $this->password = trim($this->config['pwd']);
        $this->sender = trim($this->config['sender']);

        $this->sms = new RestHelper($this->config, trim($this->config['host']), 'json');
    }

    /**
     * Send SMS https://apidocs.labsmobile.com/#send-sms
     * @param string $to Variable that includes a mobile number recipient. The number must
     * include the country code without ‘+’ ó ‘00’. Each customer account has a maximum number
     * of msisdn per sending. See the terms of your account to see this limit.
     * @param string $message The message to be sent. The maximum message length is 160 characters.
     * Only characters in the GSM 3.38 7bit alphabet, found at the end of this document, are valid.
     * Otherwise you must send ucs2 variable.
     * @param string $id Message ID included in the ACKs (delivery confirmations). It is a unique delivery
     *  ID issued by the API client. It has a maximum length of 20 characters.
     * @return array
     */
    public function send($to, $message, $id)
    {
        $responseData = [];

        try {
            $data =
                [
                    'message' => $message,
                    'tpoa' => $this->sender, // Sender of the message. May have a numeric
                        // (maximum length, 14 digits) or an alphanumeric (maximum capacity, 11 characters) value.
                        // The messaging platform assigns a default sender if this variable is not included.
                        // By including the sender's mobile number, the message recipient can easily respond
                        // from their mobile phone with the "Reply" button. The sender can only be defined in
                        // some countries due to the restrictions of the operators. Otherwise the sender is a
                        // random numeric value.
                    'test' => false,
                    'recipient' => [
                        [
                            "msisdn" => $to
                        ]
                    ],
                    'subid' => $id
                ];

            $response = $this->sms->post('send', $data, true);
            /*
             {
                  "code":"0",
                  "message":"Message has been successfully sent",
                  "subid":"56fbab0586192"
                }
             */
            if ($response->code == 0) {
                $responseData['code'] = 0;
                $responseData['message'] = $response->message;
                $responseData['status'] = 'success';
                $responseData['data'] = [
                    'id' => $response->subid
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: '.$response->message;
                $responseData['status'] = 'error';
            }
        } catch (Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: '.$e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }

    /**
     * Send SMS https://apidocs.labsmobile.com/#get-message-status
     * @param  string $to   Phone number within the sending request to check the status,
     *          credits and other associated details.
     * @param  string $id    Identifier of the sending request.
     * @return object
     */
    public function getStatus($id, $to = "")
    {
        try {
            if (!Str::startsWith($to, "34") && !empty($to)) {
                $to = "34".$to;
            }

            $data =
                [
                    'subid' => $id,
                    'msisdn' => $to
                ];

            $response =  $this->sms->post('ack', $data, true);
            /*
             {
              "code":"0",
              "subid":"56fbab0611391",
              "msisdn":"12015550123",
              "status":"handset",
              "credits":"1.04",
              "desc":"",
              "timestamp":"2018-03-18 15:23:10"
            }
             */
            if ($response->code == 0) {
                $code = -2;
                $status = 'error';
                $credits = 0;
                $timestamp = '';
                switch ($response->status) {
                    case "gateway":
                    case "operator":
                    case "processed":
                        $code = 1;
                        $status = 'pending';
                        break;
                    case "error":
                        $code = -2;
                        $status = 'error';
                        break;
                    case "test":
                    case "handset":
                        $code = 0;
                        $status = 'success';
                        $credits = $response->credits;
                        $timestamp = $response->timestamp;
                        break;
                }
                $responseData['code'] = $code;
                $responseData['message'] = '';
                $responseData['status'] = $status;
                $responseData['data'] = [
                    'credits' => $credits,
                    'timestamp' => $timestamp,
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: '.$response->message;
                $responseData['status'] = 'error';
            }
        } catch (Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: '.$e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }

    /**
     * Send SMS https://apidocs.labsmobile.com/#get-account-credits
     * @return array
     */
    public function getCredits()
    {
        try {
            $response = $this->sms->post('balance', [], true);

            /*
             {
              "code":"0",
              "credits":"33.02"
            }
             */
            if ($response->code == 0) {
                $responseData['code'] = 0;
                $responseData['message'] = '';
                $responseData['status'] = 'success';
                $responseData['data'] = [
                    'credits' => $response->credits
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: '.$response->message;
                $responseData['status'] = 'error';
            }
        } catch (Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Operation error: '.$e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }

    /**
     * @inheritDoc
     */
    public function getCrt($id, $to = "")
    {
        return null;
    }
}
