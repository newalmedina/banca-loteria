<?php

namespace Clavel\NotificationBroker\Jobs;

use Carbon\Carbon;
use Clavel\NotificationBroker\Models\Notification;
use Clavel\NotificationBroker\Services\NotificationFactory;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

    protected $notification;

    /**
     * Create a new job instance.
     *
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $error_code = 0;
        $error_message = "";
        $platform_uid = null;

        try {
            // Obtenemos la información de la notificacion
            $platform_uid = $this->notification->platform_uid;
            $is_certified = $this->notification->is_certified;
            $defaultBroker = $this->notification->broker;

            // En función de los parametros de la notificación cargamos el broker necesario
            $brokerEmail = NotificationFactory::create($defaultBroker, $is_certified, 'email');
            $response = $brokerEmail->send($this->notification);

            // Verificamos si ha habido un error en el envio
            if ($response['code'] != "0") {
                $error_code = -1;
                $error_message = "Error[".$response['code']."] ".$response['message'];
            } else {
                if (isset($response['data']) && isset($response['data']['id'])) {
                    $platform_uid = $response['data']['id'];
                }
            }
        } catch (Exception $e) {
            $error_code = -1;
            $error_message = $e->getMessage();
        }

        // Grabamos el resultado del Email
        $this->notification->response_code = $error_code;
        $this->notification->response_info = $error_message;
        $this->notification->sent_at = Carbon::now();
        $this->notification->platform_uid = $platform_uid;

        // Si el envio es correcto se marca como enviado sino pasa a retrying
        $this->notification->status_slug = ($error_code==0?'sent':'retrying');
        $this->notification->save();
    }

    /*
    public function handle()
    {
        // Si no es certificados vamos por el canal estándar
        if(!$this->notification->is_certified) {
            $this->sendEmail();
        } else {
            // vemos cual es el certificador de email
            // Recuperamos el servicio de SMS
            $defaultBroker = Config::get('mailCustom.default');
            switch ($defaultBroker) {
                case "mailcertificado":
                    $this->sendEmailEmailCertificado();
                    break;
                default:
                    break;
            }
        }
    }


    protected function sendEmail() {
        $error_code = 0;
        $error_message = "";

        try {
            $payload = json_decode($this->notification->payload, true);
            $html = $this->notification->message;

            Mail::send(array(), array(), function ($message) use ($payload, $html) {
                $message
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->to($payload['to'])
                    ->subject($payload['subject'])
                    ->setBody($html, 'text/html');
                ;

            });
        }
        catch(\Exception $e){
            $error_code = -1;
            $error_message = $e->getMessage();
        }
        $this->notification->response_code = $error_code;
        $this->notification->response_info = $error_message;
        $this->notification->sent_at = Carbon::now();
        $this->notification->broker = 'mail';

        // Si el envio es correcto se marca como enviado sino pasa a retrying
        $this->notification->status_slug = ($error_code==0?'sent':'retrying');
        $this->notification->save();
    }

    protected function sendEmailEmailCertificado() {
        $error_code = 0;
        $error_message = "";
        $platform_uid = null;

        try {
            $broker = Config::get('mailCustom.brokers.mailcertificado');

            $opts = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $context = stream_context_create($opts);
            $wsdl = storage_path('wsdl/').  env('URL_MAILCERTIFICADO') . DIRECTORY_SEPARATOR.$broker['wsdl'];
            //$wsdl = $broker['host']; //PRODUCCIÓN bloqueado acceso
            //$wsdl = "https://soaptest.mailcertificado.net/WSserver?wsdl";
            //$wsdl = "https://soapliteral.mailcertificado.net/WSserver?wsdl";
            //$wsdl = "https://soap.mailcertificado.net/ws/WSserver.php?wsdl";

            $payload = json_decode($this->notification->payload, true);
            $html = $this->notification->message;

            $client = new SoapClient($wsdl, array(
                    'stream_context' => $context,
                    'trace' => true,
                    'login' => $broker['username'],
                    'password' => $broker['password'],
                    'cache_wsdl' => 0
                )
            );

            $result = $client->sendMailWS(
                array(
                    'userData' => array(
                        'user' => $broker['username'],
                        'pass' => $broker['password']
                    ),
                    'to' => $payload['to'],
                    'subject' => $payload['subject'],
                    'body' => $html
                )
            );

            $platform_uid = $result->result->messageId;
        }
        catch(\Exception $e){
            $error_code = -1;
            $error_message = $e->getMessage();
        }
        $this->notification->response_code = $error_code;
        $this->notification->response_info = $error_message;
        $this->notification->sent_at = Carbon::now();
        $this->notification->platform_uid = $platform_uid;
        $this->notification->broker = 'mailcertificado';

        // Si el envio es correcto se marca como enviado sino pasa a retrying
        $this->notification->status_slug = ($error_code==0?'sent':'retrying');
        $this->notification->save();
    }
    */
}
