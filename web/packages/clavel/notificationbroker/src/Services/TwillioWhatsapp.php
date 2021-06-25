<?php namespace Clavel\NotificationBroker\Services;

use Clavel\NotificationBroker\Models\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class TwillioWhatsapp extends NotificationFactory implements NotificationWhatsappInterface
{
    protected $config = null;
    public $sid = '';
    public $token = '';
    public $number = '';


    public function __construct()
    {
        $this->config = Config::get('notificationbroker.brokers.twillio-whatsapp');
        $this->token = trim($this->config['token']);
        $this->sid = trim($this->config['sid']);
        $this->number = trim($this->config['number']);
    }


    /**
     * @inheritDoc
     */
    public function send(Notification $notification)
    {
        $responseData = [];

        try {
            $twilio = new Client($this->sid, $this->token);

            $to = str_replace("+", "", $notification->receiver);
            if (!Str::startsWith($to, "34") && !empty($to)) {
                $to = "+34".$to;
            }

            $response = $twilio->messages
                ->create(
                    "whatsapp:".$to, // to
                    array(
                        "from" => "whatsapp:".$this->number,
                        "body" =>  $notification->message,
                        "mediaUrl" => array("https://nb.madrilena.es/assets/admin/img/user.png")

                    )
                );

            if (empty($response->errorCode)) {
                $responseData['code'] = 0;
                $responseData['message'] = '';
                $responseData['status'] = 'success';
                $responseData['data'] = [
                    'id' => $response->sid
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error ['.$response->errorCode.']'.$response->error_message;
                $responseData['status'] = 'error';
            }
        } catch (\Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: '.$e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }
}
