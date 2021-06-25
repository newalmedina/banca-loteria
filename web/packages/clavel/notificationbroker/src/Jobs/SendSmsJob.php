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
use Illuminate\Support\Facades\Config;

class SendSmsJob implements ShouldQueue
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
            $payload = json_decode($this->notification->payload, true);
            $message = $this->notification->message;
            $platform_uid = $this->notification->platform_uid;
            $is_certified = $this->notification->is_certified;
            $defaultBroker = $this->notification->broker;

            $id = $this->notification->guid;

            // Labsmobile tiene una limitación en cuanto a longitud
            if ($defaultBroker == 'labsmobile') {
                $id = $this->notification->platform_uid;
            }

            // En función de los parametros de la notificación cargamos el broker necesario
            $brokerSMS = NotificationFactory::create($defaultBroker, $is_certified, 'sms');
            $response = $brokerSMS->send($payload['to'], $message, $id);

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

        // Grabamos el resultado del SMS
        $this->notification->response_code = $error_code;
        $this->notification->response_info = $error_message;
        $this->notification->platform_uid = $platform_uid;
        $this->notification->sent_at = Carbon::now();

        // Si el envio es correcto se marca como enviado sino pasa a retrying
        $this->notification->status_slug = ($error_code==0?'sent':'retrying');
        $this->notification->save();
    }
}
