<?php namespace Clavel\NotificationBroker\Services;

use Clavel\NotificationBroker\Models\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailNativo extends NotificationFactory implements NotificationEmailInterface
{
    protected $config = null;
    public $bcc = '';


    public function __construct()
    {
        $this->config = Config::get('notificationbroker.brokers.mail');
        $this->bcc = trim($this->config['bcc']);
    }

    /**
     * @inheritDoc
     */
    public function send(Notification $notification)
    {
        $responseData = [];

        try {
            $payload = json_decode($notification->payload, true);
            $html = $notification->message;

            Mail::send(array(), array(), function ($message) use ($payload, $html) {
                $message
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->to($payload['to'])
                    ->subject($payload['subject'])
                    ->setBody($html, 'text/html');
                if (!empty($this->bcc)) {
                    $message->bcc([$this->bcc]);
                }
            });

            $responseData['code'] = 0;
            $responseData['message'] = '';
            $responseData['status'] = 'success';
            $responseData['data'] = [
                'id' => ''
            ];
        } catch (\Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: '.$e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(Notification $notification)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getCrt(Notification $notification)
    {
        return null;
    }
}
