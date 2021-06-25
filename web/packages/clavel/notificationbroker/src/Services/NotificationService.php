<?php

namespace Clavel\NotificationBroker\Services;

use Clavel\NotificationBroker\Jobs\SendEmailJob;
use Clavel\NotificationBroker\Jobs\SendSmsJob;
use Clavel\NotificationBroker\Models\Notification;
use Clavel\NotificationBroker\Models\NotificationBrokerSettings;
use Clavel\NotificationBroker\Models\NotificationEntity;
use Clavel\NotificationBroker\Models\NotificationTmp;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Client;

class NotificationService
{
    private $retry_times = [15, 30, 60];

    private $time_frames = [
        [
            "start_time" => '08:00',
            "end_time" => '21:00'
        ],
    ];

    public function getMaxRetries()
    {
        return sizeof($this->retry_times);
    }

    public function retry()
    {
        try {
            // Sólo haremos retries si estamos dentro del time frame permitido
            if ($this->inTimeFrame()) {
                // Leemos las notificaciones que no han podido ser enviadas según un criterio temporal
                // 1er reintento (response_code <> 0 sent_at a los 15 minutos)
                // 2do reintento (response_code <> 0 retry_at = 1 sent_at a los 30 minutos)
                // 3do reintento (response_code <> 0 retry_at = 2 sent_at a los 60 minutos)


                for ($i = 0; $i < $this->getMaxRetries(); $i++) {
                    $notifications_failed = Notification::where("response_code", "<>", 0)
                        ->where('status_slug', 'retrying')
                        ->whereNotNull('response_code')
                        ->whereNotNull('sent_at')
                        ->where('retries', $i)
                        ->where(function ($query) use ($i) {
                            // El created at lo controlamos para evitar enviar mensajes antiguos con un margen de 1 día
                            $query->where('sent_at', '<', Carbon::now()
                                ->subMinutes($this->retry_times[$i])->format('Y-m-d H:i:s'))
                                ->where('created_at', '>', Carbon::now()->startOfDay()
                                    ->subDays(1)->format('Y-m-d H:i:s'));
                        })
                        ->get();

                    foreach ($notifications_failed as $notification) {
                        // Marcamos el reintento y marcamos como si no se hubiese enviado
                        $notification->retries = $notification->retries + 1;
                        $notification->retry_at = Carbon::now();
                        $notification->sent_at = null;
                        $notification->response_code = 0;
                        $notification->response_info = '';
                        $notification->save();

                        if (substr($notification->slug_type, 0, 3) === "sms") {
                            SendSmsJob::dispatch($notification);
                        } else {
                            SendEmailJob::dispatch($notification);
                        }
                    }
                }
            }


            // Ahora marcamos como error aquellos que han superado el máximo de intentos y estan como error
            $sql = "UPDATE notifications_broker " .
                " SET status_slug = 'error'" .
                " WHERE response_code = -1 " .
                " AND sent_at IS NOT NULL" .
                " AND retries >=" . $this->getMaxRetries() .
                " AND status_slug = 'retrying'";
            DB::statement($sql);
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function delayed()
    {
        try {
            // Sólo haremos retries si estamos dentro del time frame permitido
            if ($this->inTimeFrame()) {
                // Leemos las notificaciones retrasadas
                $notifications_delayed = Notification::where('status_slug', 'delayed')
                    ->whereNull('response_code')
                    ->whereNull('sent_at')
                    ->where(function ($query) {
                        // El created at lo controlamos para evitar enviar mensajes antiguos con un margen de 1 día
                        $query->where('created_at', '>', Carbon::now()
                            ->startOfDay()->subDays(1)->format('Y-m-d H:i:s'));
                    })
                    ->get();

                foreach ($notifications_delayed as $notification) {
                    // Marcamos la notificacion como enviada
                    $notification->sent_at = null;
                    $notification->response_code = null;
                    $notification->response_info = '';
                    $notification->status_slug = 'pending';
                    $notification->save();

                    if (substr($notification->slug_type, 0, 3) === "sms") {
                        SendSmsJob::dispatch($notification);
                    } else {
                        SendEmailJob::dispatch($notification);
                    }
                }
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function inTimeFrame()
    {
        $now = Carbon::now();

        $canSend = false;

        try {
            foreach ($this->time_frames as $timeFrame) {
                $startTime = Carbon::parse($timeFrame['start_time']);
                $endTime = Carbon::parse($timeFrame['end_time']);
                $canSend |= $now->between($startTime, $endTime);
            }
        } catch (Exception $ex) {
        }
        return $canSend;
    }

    public function massive()
    {
        try {
            // Sólo haremos retries si estamos dentro del time frame permitido
            if ($this->inTimeFrame()) {
                $client = new Client();
                $token = "";
                set_time_limit(0);

                try {
                    $res = $client->request(
                        'POST',
                        'https://nb.madrilena.es/api/v1/auth/signin',
                        [
                            'headers' => [
                                'Content-Type' => 'application/json'
                            ],
                            "body" => '{"email": "info@ov-madrilena.com", "password": "ApiMRG1"}',
                            'verify' => false //PARA PRUEBAS EN CALIDAD
                        ]
                    );

                    $body = json_decode($res->getBody(), true);

                    $token = $body['token'];
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }

                $notifications_massive = NotificationTmp::orderBy('id')->get();

                foreach ($notifications_massive as $data) {
                    $json = [
                        "type" => $data->slug_type,
                        "receivers" => [
                            [
                                'to' => $data->receiver,
                                'params' => [
                                    "name" => "",
                                    "surname" => "",
                                    "code" => ""
                                ]
                            ]
                        ]
                    ];

                    $tipo = substr($data->slug_type, 0, 3);
                    if ($tipo != 'sms') {
                        $tipo = 'email';
                    }

                    $response = $client->request(
                        'POST',
                        'https://nb.madrilena.es/api/v1/notifications/' . $tipo,
                        [
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Bearer ' . $token
                            ],
                            "body" => json_encode($json)
                        ]
                    );
                    $data->delete();
                }
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function validateCertifiedEmail()
    {
        try {
            // Leemos las notificaciones no verificadas
            // Importante el IndexRaw y el orden de los where
            $sql = "SELECT *
                FROM notifications_broker
                WHERE `validated_at` IS NULL
                AND `platform_uid` IS NOT NULL
                AND `certificate_file` IS NULL
                AND `slug_type` LIKE 'email%'
                AND `status_slug` = 'sent'
                AND `response_code` = 0
                AND `is_certified` = 1
                ORDER BY `id` ASC
                LIMIT 200";

            $notifications_not_verified = DB::select($sql);

            foreach ($notifications_not_verified as $notificationObject) {
                try {
                    $notification = Notification::find($notificationObject->id);

                    // Obtenemos el broker para cargar su configuracion
                    $broker = $notification->broker;
                    $is_certified = $notification->is_certified;

                    // En función de los parametros de la notificación cargamos el broker necesario
                    $brokerSMS = NotificationFactory::create($broker, $is_certified, 'email');

                    $response = $brokerSMS->getStatus($notification);
                    // Verificamos si ha habido un error en el envio
                    if ($response['code'] != "0") {
                        $notification->response_code = -1;
                        $notification->response_info = "Error[" . $response['code'] . "] " . $response['message'];
                        $notification->status_slug = 'error';
                        $notification->validated_at = Carbon::now();
                        $notification->save();
                    } else {
                        if (!$is_certified) {
                            $notification->credits =
                                empty($response['data']['credits'] ? 1 : $response['data']['credits']);
                            $notification->validated_at = Carbon::now();
                            $notification->save();
                        } else {
                            // obtenemos el certificado
                            $responseCrt = $brokerSMS->getCrt($notification);
                            if ($responseCrt['code'] == 0) {
                                try {
                                    // Verificamos la existencia de la carpeta de ficheros de certificados
                                    if (!Storage::disk('local')->exists("/certificados/email")) {
                                        //creates directory
                                        Storage::disk('local')->makeDirectory("/certificados/email", 0775, true);
                                    }

                                    $fileName = "certificados/email/certificado_" . $notification->guid . '.pdf';
                                    Storage::disk('local')->put($fileName, base64_decode($responseCrt['data']['crt']));
                                } catch (Exception $ex) {
                                    $notification->response_code = -1;
                                    $notification->response_info = "Error en certificado email: " . $ex->getMessage();
                                    $notification->status_slug = 'error';
                                    $fileName = "";
                                }
                                // Actualizamos los creditos de Emails gastados
                                $notification->credits =
                                    empty($response['data']['credits'] ? 1 : $response['data']['credits']);
                                $notification->validated_at = Carbon::now();
                                $notification->certificate_file = $fileName;
                                $notification->save();
                            } else {
                                // Error en envio de Email
                                $notification->response_code = -1;
                                $notification->response_info =
                                    "Error[" . $responseCrt['code'] . "] " . $responseCrt['message'];
                                $notification->status_slug = 'error';
                                $notification->validated_at = Carbon::now();
                                $notification->certificate_file = "";
                                $notification->save();
                            }
                        }
                    }
                } catch (\Exception $ex) {
                    $notification->response_code = -1;
                    $notification->response_info = "Error: " . $ex->getMessage();
                    $notification->status_slug = 'error';
                    $notification->validated_at = Carbon::now();
                    $notification->certificate_file = "";
                    $notification->save();
                }
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    private function validateSMSBase($notifications_not_verified)
    {
        foreach ($notifications_not_verified as $notificationObject) {
            try {
                $notification = Notification::find($notificationObject->id);

                // Obtenemos el broker para cargar su configuracion
                $payload = json_decode($notification->payload, true);
                $broker = $notification->broker;
                $is_certified = $notification->is_certified;

                // En función de los parametros de la notificación cargamos el broker necesario
                $brokerSMS = NotificationFactory::create($broker, $is_certified, 'sms');

                $id = $notification->platform_uid;
                $to = $payload['to'];
                if ($broker == "linkmobility" || $broker == "linkmobility-certified") {
                    $to = substr($notification->guid, 0, 30);
                }

                $response = $brokerSMS->getStatus($id, $to);
                // Verificamos si ha habido un error en el envio
                if ($response['code'] != "0") {
                    // Ahora miramos si estamos en estado pendiente o en estado error. Si error lo marcamos,
                    // Si es 1 no hacemos nada
                    if ($response['code'] != "1") {
                        $notification->response_code = -1;
                        $notification->response_info = "Error[" . $response['code'] . "] " . $response['message'];
                        $notification->status_slug = 'error';
                        $notification->validated_at = Carbon::now();
                        $notification->save();
                    }
                } else {
                    if (!$is_certified) {
                        $notification->credits =
                            empty($response['data']['credits'] ? 1 : $response['data']['credits']);
                        $notification->validated_at = Carbon::now();
                        $notification->save();
                    } else {
                        // obtenemos el certificado
                        $responseCrt = $brokerSMS->getCrt($id, $to);
                        if ($responseCrt['code'] == 0) {
                            try {
                                // Verificamos la existencia de la carpeta de ficheros de certificados
                                if (!Storage::disk('local')->exists("/certificados/sms")) {
                                    //creates directory
                                    Storage::disk('local')->makeDirectory("/certificados/sms", 0775, true);
                                }

                                $fileName = "certificados/sms/certificado_" . $notification->guid . '.pdf';
                                Storage::disk('local')->put($fileName, $responseCrt['data']['crt']);
                            } catch (Exception $ex) {
                                $notification->response_code = -1;
                                $notification->response_info = "Error en certificado sms: " . $ex->getMessage();
                                $notification->status_slug = 'error';
                                $fileName = "";
                            }
                            // Actualizamos los creditos de SMS gastados
                            $notification->credits =
                                empty($response['data']['credits'] ? 1 : $response['data']['credits']);
                            $notification->validated_at = Carbon::now();
                            $notification->certificate_file = $fileName;
                            $notification->save();
                        } else {
                            // Error en envio de SMS
                            $notification->response_code = -1;
                            $notification->response_info =
                                "Error[" . $responseCrt['code'] . "] " . $responseCrt['message'];
                            $notification->status_slug = 'error';
                            $notification->validated_at = Carbon::now();
                            $notification->certificate_file = "";
                            $notification->save();
                        }
                    }
                }
            } catch (Exception $ex) {
            }
        }
    }

    public function validateSMS()
    {
        try {
            // Leemos las notificaciones no verificadas
            // Importante el IndexRaw y el orden de los where
            $sql = "SELECT *
                FROM notifications_broker
                WHERE `validated_at` IS NULL
                AND `platform_uid` IS NOT NULL
                AND `slug_type` LIKE 'sms%'
                AND `status_slug` = 'sent'
                AND `response_code` = 0
                AND `is_certified` = 0
                ORDER BY `id` ASC
                LIMIT 1000";

            $notifications_not_verified = DB::select($sql);

            $this->validateSMSBase($notifications_not_verified);
        } catch (Exception $e) {
        }
    }

    public function validateSMSCertified()
    {
        try {
            // Leemos las notificaciones no verificadas
            // Importante el IndexRaw y el orden de los where
            $sql = "SELECT *
                FROM notifications_broker
                WHERE `validated_at` IS NULL
                AND `platform_uid` IS NOT NULL
                AND `certificate_file` IS NULL
                AND `slug_type` LIKE 'sms%'
                AND `status_slug` = 'sent'
                AND `response_code` = 0
                AND `is_certified` = 1
                ORDER BY `id` ASC
                LIMIT 200";

            $notifications_not_verified = DB::select($sql);

            $this->validateSMSBase($notifications_not_verified);
        } catch (Exception $e) {
        }
    }

    public function verifySmsCredits()
    {
        try {
            // Recuperamos el servicio de SMS
            $defaultBroker = Config::get('notificationbroker.sms.default');
            // En función de los parametros de la notificación cargamos el broker necesario
            $brokerSMS = NotificationFactory::create($defaultBroker, false, 'sms');

            $response = $brokerSMS->getCredits();

            // Verificamos si ha habido un error en el envio
            if ($response['code'] == 0) {
                $credits = $response['data']['credits'];

                $settings = NotificationBrokerSettings::first();
                $settings->sms_credits = round($credits, 2);
                $settings->sms_verified_at = Carbon::now();
                $settings->save();

                // Cargamos la configuracion del broker
                $configBroker = NotificationEntity::first();

                // Verificamos si estamos en alerta por falta de SMS y no hemos enviado ya el SMS
                if ($settings->sms_credits_limit > $credits &&
                    empty($settings->sms_limit_notified_at)
                ) {
                    // Enviamos email de Alerta


                    $subject = "Límite de crédito de SMS's excedido";

                    $payload = [
                        'to' => str_replace(' ', '', $settings->sms_limit_notify_to),
                        'subject' => $subject,
                        'senderName' => $configBroker->sender_name,
                        'css' => "",
                        'logo' => [
                            'path' => Config::get('app.url') . $configBroker->logo_path,
                            'width' => $configBroker->logo_width,
                            'height' => $configBroker->logo_height
                        ],
                        'address' => $configBroker->address
                    ];

                    $payload['credits'] = number_format($settings->sms_credits, 2, ",", ".");

                    try {
                        $html = View::make('modules.notifications.smsLimit', compact('payload'))->render();
                        Mail::send(array(), array(), function ($message) use ($payload, $html) {
                            $emails = explode(";", $payload['to']);

                            $message
                                ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                                ->to($emails)
                                ->subject($payload['subject'])
                                ->setBody($html, 'text/html');
                        });
                    } catch (\Exception $e) {
                        dd($e->getMessage());
                    }
                    $settings->sms_limit_notified_at =  Carbon::now();
                    $settings->save();
                } else {
                    if ($settings->sms_credits_limit <= $credits) {
                        // Volvemos a tener SMS
                        $settings->sms_limit_notified_at = null;
                        $settings->save();
                    }
                }
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
    /*
        public function verifySmsCreditsOld() {
            try {
                // Recuperamos el servicio de SMS
                $defaultBroker = Config::get('notificationbroker.default');
                $broker = Config::get('sms.brokers.'.$defaultBroker);
                $sms_service = new \Clavel\Sms\Sms($broker, $defaultBroker);

                $response = $sms_service->broker->getBalance();

                // Verificamos si ha habido un error en el envio
                if($response->code == "0") {
                    $credits = $response->credits;

                    $settings = NotificationBrokerSettings::first();
                    $settings->sms_credits = round($credits,2);
                    $settings->sms_verified_at = Carbon::now();
                    $settings->save();


                    // Verificamos si estamos en alerta por falta de SMS y no hemos enviado ya el SMS
                    if($settings->sms_credits_limit > $credits &&
                        empty($settings->sms_limit_notified_at) ) {

                        // Enviamos email de Alerta


                        $subject = "Límite de crédito de SMS's excedido";

                        $payload = [
                            'to' => str_replace(' ', '', $settings->sms_limit_notify_to),
                            'subject' => $subject,
                            'senderName' => "MADRILEÑA RED DE GAS, S.A.U.",
                            'css' => "",
                            'logo' => [
                                'path' => Config::get('app.url') . "/assets/front/img/logo_mrg.png",
                                'width' => 220,
                                'height' => 70
                            ],
                            'address' => "A-65142309"
                        ];

                        $payload['credits'] = number_format($settings->sms_credits,2,",",".");

                        try {


                            $html = View::make('modules.notifications.smsLimit', compact('payload'))->render();
                            Mail::send(array(), array(), function ($message) use ($payload, $html) {

                                $emails = explode(";", $payload['to']);

                                $message
                                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                                    ->to($emails)
                                    ->subject($payload['subject'])
                                    ->setBody($html, 'text/html');

                            });
                        } catch (\Exception $e) {
                            dd($e->getMessage());
                        }
                        $settings->sms_limit_notified_at =  Carbon::now();
                        $settings->save();

                    } else {
                        if($settings->sms_credits_limit <= $credits ) {
                            // Volvemos a tener SMS
                            $settings->sms_limit_notified_at = null;
                            $settings->save();
                        }
                    }
                }
            } catch (Exception $e) {
                dd($e->getMessage());
            }
        }
    */
    public function resend()
    {
        try {
            // Leemos las notificaciones que queremos reenviar
            $notifications_retry = Notification::where("response_code", "<>", 0)
                ->where('slug_type', 'email-27')
                //->where('id', '2960004')
                ->where('status_slug', 'error')
                ->whereNotNull('response_code')
                ->whereNotNull('sent_at')
                ->get();

            foreach ($notifications_retry as $notification) {
                $payload = json_decode($notification->payload, true);
                try {
                    $html = View::make('modules.notifications.emails.' .
                        $notification->slug_type, compact('payload'))
                        ->render();
                } catch (\Exception $e) {
                    dd($e->getMessage());
                    $error_code = -1;
                    $error_message = $e->getMessage();
                }

                // Marcamos el reintento y marcamos como si no se hubiese enviado
                $notification->message = $html;
                $notification->retries = 1;
                $notification->retry_at = Carbon::now();
                $notification->sent_at = null;
                $notification->response_code = 0;
                $notification->response_info = '';
                $notification->save();

                SendEmailJob::dispatch($notification);
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function normalizeData()
    {
        try {
            // Leemos las notificaciones que no han sido normalizadas
            $notifications = Notification::where('receiver', '')
                ->limit(10000)
                ->get();

            foreach ($notifications as $notification) {
                $payload = json_decode($notification->payload, true);

                // Grabamos el destinatario
                $notification->receiver = $payload["to"];
                $notification->save();
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
