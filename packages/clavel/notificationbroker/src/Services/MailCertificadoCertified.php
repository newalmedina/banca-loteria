<?php namespace Clavel\NotificationBroker\Services;

use Clavel\NotificationBroker\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use SoapClient;

class MailCertificadoCertified extends NotificationFactory implements NotificationEmailInterface
{
    protected $config = null;
    public $user = '';
    public $password = '';
    public $host = '';
    public $env = '';
    public $bcc = '';


    public function __construct()
    {
        $this->config = Config::get('notificationbroker.brokers.mailcertificado');
        $this->user = trim($this->config['username']);
        $this->password = trim($this->config['password']);
        $this->host = trim($this->config['host']);
        $this->env = trim($this->config['env']);
        $this->wsdl = trim($this->config['wsdl']);
        $this->bcc = trim($this->config['bcc']);
    }


    /**
     * @inheritDoc
     */
    public function send(Notification $notification)
    {
        $responseData = [];

        try {
            $opts = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $context = stream_context_create($opts);
            $wsdl = storage_path('wsdl/').  $this->env . DIRECTORY_SEPARATOR.$this->wsdl;
            //$wsdl = $broker['host']; //PRODUCCIÓN bloqueado acceso
            //$wsdl = "https://soaptest.mailcertificado.net/WSserver?wsdl";
            //$wsdl = "https://soapliteral.mailcertificado.net/WSserver?wsdl";
            //$wsdl = "https://soap.mailcertificado.net/ws/WSserver.php?wsdl";

            $payload = json_decode($notification->payload, true);
            $html = $notification->message;

            $client = new SoapClient($wsdl, array(
                    'stream_context' => $context,
                    'trace' => true,
                    'login' => $this->user,
                    'password' => $this->password,
                    'cache_wsdl' => 0
                ));

            $response = $client->sendMailWS(
                array(
                    'userData' => array(
                        'user' => $this->user,
                        'pass' => $this->password
                    ),
                    'to' => $payload['to'],
                    'subject' => $payload['subject'],
                    'body' => $html
                )
            );

            $responseData['code'] = 0;
            $responseData['message'] = '';
            $responseData['status'] = 'success';
            $responseData['data'] = [
                'id' => $response->result->messageId
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
        $responseData = [];

        try {
            $opts = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $context = stream_context_create($opts);
            $wsdl = storage_path('wsdl/').  $this->env . DIRECTORY_SEPARATOR.$this->wsdl;
            //$wsdl = $broker['host']; //PRODUCCIÓN bloqueado acceso
            //$wsdl = "https://soaptest.mailcertificado.net/WSserver?wsdl";
            //$wsdl = "https://soapliteral.mailcertificado.net/WSserver?wsdl";
            //$wsdl = "https://soap.mailcertificado.net/ws/WSserver.php?wsdl";

            $client = new SoapClient($wsdl, array(
                    'stream_context' => $context,
                    'trace' => true,
                    'login' => $this->user,
                    'password' => $this->password,
                    'cache_wsdl' => 0
                ));

            $response = $client->getMsgStatusWS(
                array(
                    'userData' => array(
                        'user' => $this->user,
                        'pass' => $this->password
                    ),
                    'messageId' => $notification->platform_uid
                )
            );

            if (strtolower($response->result->status) == "leido" ||
                strtolower($response->result->status) == "entregado") {
                $responseData['code'] = 0;
                $responseData['message'] = $response->result->status;
                $responseData['status'] = 'success';
                $responseData['data'] = [
                    'credits' => 1,
                    'timestamp' => Carbon::now()->toString(),
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: '.$response->result->status;
                $responseData['status'] = 'error';
            }
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
    public function getCrt(Notification $notification)
    {
        $responseData = [];

        try {
            $opts = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $context = stream_context_create($opts);
            $wsdl = storage_path('wsdl/').  $this->env . DIRECTORY_SEPARATOR.$this->wsdl;
            //$wsdl = $broker['host']; //PRODUCCIÓN bloqueado acceso
            //$wsdl = "https://soaptest.mailcertificado.net/WSserver?wsdl";
            //$wsdl = "https://soapliteral.mailcertificado.net/WSserver?wsdl";
            //$wsdl = "https://soap.mailcertificado.net/ws/WSserver.php?wsdl";

            $client = new SoapClient($wsdl, array(
                    'stream_context' => $context,
                    'trace' => true,
                    'login' => $this->user,
                    'password' => $this->password,
                    'cache_wsdl' => 0
                ));

            $response = $client->getMsgCertificateWS(
                array(
                    'userData' => array(
                        'user' => $this->user,
                        'pass' => $this->password
                    ),
                    'messageId' => $notification->platform_uid,
                    'type' => 'general'
                )
            );

            $code = -2;
            $status = 'error';
            $crt = null;

            // Miramos si viene con el documento de certificado adjunto
            if (isset($response->data)) {
                try {
                    $code = 0;
                    $status = 'success';
                    $crt = $response->data;
                } catch (\Exception $ex) {
                    $code = -2;
                    $status = 'error';
                    $crt = null;
                }
            }

            $responseData['code'] = $code;
            $responseData['message'] = '';
            $responseData['status'] = $status;
            $responseData['data'] = [
                'crt' => $crt
            ];
        } catch (\Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: '.$e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }
}
