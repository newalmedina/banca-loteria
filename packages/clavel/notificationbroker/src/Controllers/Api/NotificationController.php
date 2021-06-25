<?php

namespace Clavel\NotificationBroker\Controllers\Api;

use App\Http\Controllers\ApiController;
use Clavel\NotificationBroker\Jobs\SendEmailJob;
use Clavel\NotificationBroker\Jobs\SendSmsJob;
use Clavel\NotificationBroker\Models\Blacklist;
use Clavel\NotificationBroker\Models\Notification;
use Clavel\NotificationBroker\Models\NotificationEntity;
use Clavel\NotificationBroker\Models\NotificationGroup;
use Clavel\NotificationBroker\Models\NotificationType;
use Carbon\Carbon;
use Clavel\NotificationBroker\Services\NotificationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Ramsey\Uuid\Uuid;

class NotificationController extends ApiController
{

    /**
     * @OA\Post(
     *     path="/api/v1/notifications/email",
     *     tags={"Notifications"},
     *     summary="Creates a email notification and inserts it into a queue",
     *     description="Creates a email notificationand inserts it into a queue",
     *     operationId="notification-email-store-queue",
     *     security={{"Authorization":{}}},
     *     @OA\RequestBody(
     *         description="Notification data",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/Notification"),
     *              example={"type": "email-001", "forced": false, "receivers":
     * {{"to": "info@aduxia.com",
     * "subject": "Título del email a recibir(opcional)",
     * "uid": "49082340890820349203",
     * "params": {"name": "Jose Juan",
     * "surname": "Calvo", "code":
     * "000305850667"}, "certified": false }}}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification created",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/NotificationResponse"),
     *          )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeEmailQueue(Request $request)
    {
        return $this->sendEmail($request, 'database');
    }


    /**
     * @OA\Post(
     *     path="/api/v1/notifications/email/sync",
     *     tags={"Notifications"},
     *     summary="Creates a email notification",
     *     description="Creates a email notification",
     *     operationId="notification-email-store",
     *     security={{"Authorization":{}}},
     *     @OA\RequestBody(
     *         description="Notification data",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/Notification"),
     *              example={"type": "email-001", "forced": false,
     *  "receivers": {{"to": "info@aduxia.com",
     * "subject": "Título del email a recibir(opcional)",
     * "uid": "49082340890820349203",
     * "params": {"name": "Jose Juan", "surname": "Calvo", "code": "000305850667"}, "certified": false }}
     * }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification created",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/NotificationResponse"),
     *          )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeEmail(Request $request)
    {
        return $this->sendEmail($request, 'sync');
    }

    private function saveFileGroup(Request $request)
    {
        // Guardamos el fichero recibido para su posterior trazabilidad
        $filename = $this->saveFileData($request);

        // Grabamos el grupo de de notificaciones
        $notificationGroup = new NotificationGroup();
        $notificationGroup->fichero_group = $filename;
        $notificationGroup->user_id = Auth::user()->id;
        $notificationGroup->save();

        return $notificationGroup;
    }

    private function sendEmail(Request $request, $connection = 'database')
    {
        try {
            $notificationGroup = $this->saveFileGroup($request);

            // Validamos el tipo de Email
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|max:255'
            ]);

            // Si el email falla retornamos
            if ($validator->fails()) {
                return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $type = NotificationType::where('slug', $request->get('type', ''))->first();

            if (empty($type)) {
                return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Validamos los emails
            $receivers = $request->get("receivers", []);
            $validator = Validator::make($receivers, [
                '*.to' => 'required|string|email|max:255'
            ]);
            if ($validator->fails()) {
                return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Vemos si nos fuerzan a encolarlo igualmente
            $forced = $request->get("forced", false);

            // Cargamos la configuracion del broker
            $configBroker = NotificationEntity::first();

            $ns = new NotificationService();

            $messageIds = [];
            // Iteramos por todos los receptores
            foreach ($receivers as $receiver) {
                $subject = $this->replaceSubjectVars(
                    $type->subject,
                    (isset($receiver["params"]) ? $receiver["params"] : null)
                );

                // Creamos el guid que incluiremos en el email para hacer el tracking
                $guid = Uuid::uuid4()->toString();

                $payload = [
                    'to' => $receiver["to"],
                    'guid' => $guid,
                    'subject' => isset($receiver["subject"]) ? $receiver["subject"] : $subject,
                    'senderName' => $configBroker->sender_name,
                    'css' => "",
                    'logo' => [
                        'path' => $request->getSchemeAndHttpHost() . $configBroker->logo_path,
                        'width' => $configBroker->logo_width,
                        'height' => $configBroker->logo_height
                    ],
                    //'reminder' =>  '<a href="https://madrilena.es/aviso-legal/" target="_blank">Aviso legal</a>',
                    //'reminder' =>  'Este email no recibe comunicaciones entrantes.
                    // Si tienes cualquier pregunta o solicitud y quieres contactar con nosotros, utiliza nuestra
                    // <a href="https://ov.madrilena.es/" target="_blank"><strong>Oficina Virtual</strong></a>
                    // o escríbenos a <a href="mailto:atencion.usuarios@madrilena.es" target="_blank">
                    //<strong>atencion.usuarios@madrilena.es</strong></a>.',
                    //'unsubscribe' =>  '<a href="https://madrilena.es/" target="_blank">Madrileña Red de Gas</a>',
                    //'linkedin' =>  "madrileña-red-de-gas",
                    //'twitter' =>  "madrilenagas",
                    //'facebook' =>  "Madrile%C3%B1a-Red-de-Gas-292127824791661l",
                    'address' => $configBroker->address
                ];

                $payload = array_merge($payload, $receiver["params"]);

                // Enviar mail
                $error_code = null;
                $error_message = "";
                $html = "";
                $retries = 0;
                try {
                    $html = View::make('notificationbroker::notifications.emails.' .
                        $type->slug, compact('payload'))->render();
                } catch (\Exception $e) {
                    $error_code = -1;
                    $error_message = $e->getMessage();
                }

                // Verificamos si el mail no esta en la lista negra
                if ($this->isBlacklisted($receiver["to"], 'email')) {
                    $error_code = -1;
                    $error_message = "El email " . $receiver["to"] . " esta en la lista negra.";
                    // No permitimos volver a enviarse
                    $retries = $ns->getMaxRetries();
                }

                // Verificamos si estamos en los rangos de horas permitidos y sino será el servicio de Delayed
                // Notifications quien encole el mensaje
                $canSend = false;
                if ($forced || $ns->inTimeFrame() || $connection == 'sync') {
                    $canSend = true;
                }

                // Verificamos si nos han solicitado el Email certificado
                $isCertified = false;
                if (isset($receiver["certified"])) {
                    $isCertified = ($receiver["certified"] == true);
                }

                // Recogemos el uid de origen. Es el parametro que la plataforma de origen puede utilizar para hacer
                // busquedas
                $originUid = "";
                if (isset($receiver["uid"])) {
                    $originUid = $receiver["uid"];
                }

                // Creamos la notificaion del email
                $notification = new Notification();
                $notification->receiver = $receiver["to"];
                $notification->guid = $guid;
                $notification->slug_type = $type->slug;
                $notification->user_id = Auth::user()->id;
                $notification->message = $html;
                $notification->payload = json_encode($payload);
                $notification->response_info = $error_message;
                $notification->response_code = $error_code;
                $notification->sent_at = (is_null($error_code) ? null : Carbon::now());
                $notification->notification_group_id = $notificationGroup->id;
                // Si no se envia el Email se marca como retrasado
                $notification->status_slug = (is_null($error_code) ? ($canSend ? 'pending' : 'delayed') : 'error');
                // El email es certificado
                $notification->is_certified = $isCertified;
                // Creditos de Email gastados
                $notification->credits = (is_null($error_code) ? 1 : 0);
                $notification->retries = $retries;
                // UUID original del Email
                $notification->origin_uid = $originUid;

                // Asignamos el broker
                if (!$isCertified) {
                    $defaultBroker = Config::get('notificationbroker.email.default');
                } else {
                    $defaultBroker = Config::get('notificationbroker.email.certified');
                }
                $notification->broker = $defaultBroker;
                $notification->save();

                // Verificamos si estamos en los rangos de horas permitidos y sino será el servicio de Delayed
                // Notifications quien encole el mensaje
                if ($canSend && $error_code >= 0) {
                    SendEmailJob::dispatch($notification)->onConnection($connection);
                }

                $response_data = [
                    'to' => $receiver["to"],
                    'guid' => $notification->guid,
                ];
                $messageIds[] = $response_data;
            }

            // All good so return the token
            $data = [];
            $data['type'] = $type->slug;
            $data['ids'] = $messageIds;


            $headers = ['X-Authorization-Token' => "{$this->getToken($request)}"];
            return response()->json(
                $data,
                Response::HTTP_OK,
                $headers
            );
        } catch (\Exception $e) {
            //return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            return $this->respondWithCustom(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage()
            );
        }
    }

    private function replaceSubjectVars($subject, $params)
    {
        if (empty($params)) {
            return $subject;
        }

        foreach ($params as $key => $value) {
            $subject = str_replace("{##" . $key . "##}", $value, $subject);
        }

        return $subject;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/notifications/email/{guid}",
     *     tags={"Notifications"},
     *     summary="Notification email status",
     *     description="Retrieve a specific notification result",
     *     operationId="notification-email-show",
     *     security={{"Authorization":{}}},
     *     @OA\Parameter(
     *         description="Notification guid",
     *         in="path",
     *         name="guid",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication token",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"to", "guid", "code", "info"},
     *                  @OA\Property(
     *                      property="to",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="guid",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="uid",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="is_certified",
     *                      type="boolean",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="code",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="info",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="sent_at",
     *                      ref="#/components/schemas/DateTime",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      enum={"pending", "retrying", "error", "sent"},
     *                      description="Status of the notification",
     *                      readOnly=true
     *                  ),
     *              )
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    /**
     * Display the specified resource.
     *
     * @param String $guid
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showEmail($guid, Request $request)
    {
        return $this->returnStatus($guid, "guid", $request);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/notifications/email/status",
     *     tags={"Notifications"},
     *     summary="Notification email status",
     *     description="Retrieve a specific notification result",
     *     operationId="notification-email-show-status",
     *     security={{"Authorization":{}}},
     *     @OA\RequestBody(
     *         description="Notification data",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"type", "id"},
     *                  @OA\Property(
     *                      property="type",
     *                      enum={"guid", "uid"},
     *                      type="string"
     *                  ),*
     *                  @OA\Property(
     *                      property="id",
     *                      type="string"
     *                  ),
     *              ),
     *              example={"type": "guid", "id": "6bcf2906-f494-4fb4-84ae-1a705b118aec"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication token",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"to", "guid", "code", "info"},
     *                  @OA\Property(
     *                      property="to",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="guid",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="uid",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="is_certified",
     *                      type="boolean",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="code",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="info",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="sent_at",
     *                      ref="#/components/schemas/DateTime",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      enum={"pending", "retrying", "error", "sent"},
     *                      description="Status of the notification",
     *                      readOnly=true
     *                  ),
     *              )
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    /**
     * Display the specified resource.
     *
     * @param String $guid
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showEmailStatus(Request $request)
    {
        $guid = $request->get("id", "");
        $type = $request->get("type", "");
        return $this->returnStatus($guid, $type, $request);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/notifications/sms",
     *     tags={"Notifications"},
     *     summary="Creates a sms notification and inserts it into a queue",
     *     description="Creates a sms notification and inserts it into a queue",
     *     operationId="notification-sms-store-queue",
     *     security={{"Authorization":{}}},
     *     @OA\RequestBody(
     *         description="Notification data",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/Notification"),
     *              example={"type": "sms-001", "forced": false, "receivers":
     * {{"to": "667786621", "uid": "49082340890820349203",
     * "params": {"name": "Jose Juan", "surname": "Calvo", "code": "000305850667"},
     * "certified": false }}}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification created",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/NotificationResponse"),
     *          )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSMSQueue(Request $request)
    {
        return $this->sendSMS($request, 'database');
    }


    /**
     * @OA\Post(
     *     path="/api/v1/notifications/sms/sync",
     *     tags={"Notifications"},
     *     summary="Creates a sms notification",
     *     description="Creates a sms notification",
     *     operationId="notification-sms-store",
     *     security={{"Authorization":{}}},
     *     @OA\RequestBody(
     *         description="Notification data",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/Notification"),
     *              example={"type": "sms-001", "forced": false, "receivers":
     * {{"to": "667786621", "uid": "49082340890820349203", "params":
     * {"name": "Jose Juan", "surname": "Calvo", "code": "000305850667"}, "certified": false }}}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification created",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/NotificationResponse"),
     *          )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSMS(Request $request)
    {
        return $this->sendSMS($request, 'sync');
    }

    private function sendSMS(Request $request, $connection = 'database')
    {
        try {
            $notificationGroup = $this->saveFileGroup($request);

            // Validamos el tipo de SMS
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|max:255'
            ]);

            // Si el SMS falla retornamos
            if ($validator->fails()) {
                return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $type = NotificationType::where('slug', $request->get('type', ''))->first();

            if (empty($type)) {
                return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Validamos los mobiles
            $receivers = $request->get("receivers", []);
            $validator = Validator::make($receivers, [
                '*.to' => 'required|numeric|min:9'
            ]);
            if ($validator->fails()) {
                return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Vemos si nos fuerzan a encolarlo igualmente
            $forced = $request->get("forced", false);

            $ns = new NotificationService();

            $messageIds = [];
            // Iteramos por todos los receptores
            foreach ($receivers as $receiver) {
                $payload = [
                    'to' => $receiver["to"],
                ];

                $payload = array_merge($payload, $receiver["params"]);

                // Enviar SMS
                $error_code = null;
                $error_message = "";
                $message = "";
                $retries = 0;

                try {
                    $message = View::make('notificationbroker::notifications.sms.' .
                        $type->slug, compact('payload'))
                        ->render();
                } catch (\Exception $e) {
                    $error_code = -1;
                    $error_message = $e->getMessage();
                    // No permitimos volver a enviarse
                    $retries = $ns->getMaxRetries();
                }

                // Verificamos si el movil es valido y no esta en la lista negra
                if (!$this->isCorrectMobileNumber($receiver["to"])) {
                    $error_code = -1;
                    $error_message = "El móvil " . $receiver["to"] . " no es válido.";
                    // No permitimos volver a enviarse
                    $retries = $ns->getMaxRetries();
                }
                if ($this->isBlacklisted($receiver["to"], 'sms')) {
                    $error_code = -1;
                    $error_message = "El móvil " . $receiver["to"] . " esta en la lista negra.";
                    // No permitimos volver a enviarse
                    $retries = $ns->getMaxRetries();
                }

                // Verificamos si estamos en los rangos de horas permitidos y sino será el servicio de Delayed
                // Notifications quien encole el mensaje
                $canSend = false;
                if ($forced || $ns->inTimeFrame() || $connection == 'sync') {
                    $canSend = true;
                }

                // Verificamos si nos han solicitado el SMS certificado
                $isCertified = false;
                if (isset($receiver["certified"])) {
                    $isCertified = ($receiver["certified"] == true);
                }

                // Recogemos el uid de origen. Es el parametro que la plataforma de origen puede utilizar para hacer
                // busquedas
                $originUid = "";
                if (isset($receiver["uid"])) {
                    $originUid = $receiver["uid"];
                }

                // Creamos la notificaion del sms
                $notification = new Notification();
                $notification->receiver = $receiver["to"];
                $notification->guid = Uuid::uuid4()->toString();
                $notification->slug_type = $type->slug;
                $notification->user_id = Auth::user()->id;
                $notification->message = $message;
                $notification->payload = json_encode($payload);
                $notification->response_code = $error_code;
                $notification->response_info = $error_message;
                $notification->sent_at = (is_null($error_code) ? null : Carbon::now());
                $notification->notification_group_id = $notificationGroup->id;
                // Si no se envia el SMS se marca como retrasado
                $notification->status_slug = (is_null($error_code) ? ($canSend ? 'pending' : 'delayed') : 'error');
                // El sms es certificado
                $notification->is_certified = $isCertified;
                // Creditos de SMS gastados
                $notification->credits = (is_null($error_code) ? 1 : 0);
                $notification->retries = $retries;
                // UUID original del SMS
                $notification->origin_uid = $originUid;

                // Asignamos el broker
                if (!$isCertified) {
                    $defaultBroker = Config::get('notificationbroker.sms.default');
                } else {
                    $defaultBroker = Config::get('notificationbroker.sms.certified');
                }
                $notification->broker = $defaultBroker;
                $notification->save();

                // Guardamos el guid que enviaremos a la plataforma para poder despues recuperarlo. Como no tenemos el
                // ID hasta guardarlo lo tenemos que hacer en dos pasos
                // Guardamos en HEX para que ocupe menos en la plataforma destino
                $notification->platform_uid = dechex($notification->id);
                $notification->save();

                // Verificamos si estamos en los rangos de horas permitidos y sino será el servicio de Delayed
                // Notifications quien encole el mensaje si no se ha producido un error
                if ($canSend && $error_code >= 0) {
                    SendSmsJob::dispatch($notification)->onConnection($connection);
                }

                $response_data = [
                    'to' => $receiver["to"],
                    'guid' => $notification->guid,
                ];
                $messageIds[] = $response_data;
            }

            // All good so return the token
            $data = [];
            $data['type'] = $type->slug;
            $data['ids'] = $messageIds;

            $headers = ['X-Authorization-Token' => "{$this->getToken($request)}"];
            return response()->json(
                $data,
                Response::HTTP_OK,
                $headers
            );
        } catch (\Exception $e) {
            //return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            return $this->respondWithCustom(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage()
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/notifications/sms/{guid}",
     *     tags={"Notifications"},
     *     summary="Notification sms status",
     *     description="Retrieve a specific notification result",
     *     operationId="notification-sms-show",
     *     security={{"Authorization":{}}},
     *     @OA\Parameter(
     *         description="Notification guid",
     *         in="path",
     *         name="guid",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication token",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"to", "guid", "code", "info"},
     *                  @OA\Property(
     *                      property="to",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="guid",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="uid",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="is_certified",
     *                      type="boolean",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="code",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="info",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="sent_at",
     *                      ref="#/components/schemas/DateTime",
     *                      readOnly=true
     *                  ),
     *              )
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    /**
     * Display the specified resource.
     *
     * @param String $guid
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showSms($guid, Request $request)
    {
        return $this->returnStatus($guid, "guid", $request);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/notifications/sms/status",
     *     tags={"Notifications"},
     *     summary="Notification sms status",
     *     description="Retrieve a specific notification result",
     *     operationId="notification-sms-show-status",
     *     security={{"Authorization":{}}},
     *     @OA\RequestBody(
     *         description="Notification data",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"type", "id"},
     *                  @OA\Property(
     *                      property="type",
     *                      enum={"guid", "uid"},
     *                      type="string"
     *                  ),*
     *                  @OA\Property(
     *                      property="id",
     *                      type="string"
     *                  ),
     *              ),
     *              example={"type": "guid", "id": "6bcf2906-f494-4fb4-84ae-1a705b118aec"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication token",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"to", "guid", "code", "info"},
     *                  @OA\Property(
     *                      property="to",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="guid",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="uid",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="is_certified",
     *                      type="boolean",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="code",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="info",
     *                      type="string",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="sent_at",
     *                      ref="#/components/schemas/DateTime",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      enum={"pending", "retrying", "error", "sent"},
     *                      description="Status of the notification",
     *                      readOnly=true
     *                  ),
     *                  @OA\Property(
     *                      property="crt_doc",
     *                      type="string",
     *                      description="Filled if is_certified is true and the platform has received the
     *                                   certified document. The document is base 64 encoded, so you must decode it.",
     *                      readOnly=true
     *                  ),
     *              )
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    /**
     * Display the specified resource.
     *
     * @param String $guid
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showSmsStatus(Request $request)
    {
        $guid = $request->get("id", "");
        $type = $request->get("type", "");
        return $this->returnStatus($guid, $type, $request);
    }

    /**
     * Display the specified resource.
     *
     * @param String $guid
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function returnStatus($guid, $type, Request $request)
    {
        try {
            // Segun el tipo consultamos un campo y otro
            $searchField = 'guid';
            if ($type == 'uid') {
                $searchField = 'origin_uid';
            }
            $notification = Notification::where($searchField, $guid)->first();
            if (is_null($notification)) {
                return $this->respondWithCode(Response::HTTP_NOT_FOUND);
            }

            $payload = json_decode($notification->payload);

            // Leemos el notification service para validar si hemos acabamos los reintentos
            $ns = new NotificationService();
            $maxReintentos = $ns->getMaxRetries();

            // Respondemos con los datos de la notificación
            $data = [];
            $data['to'] = $notification->receiver;
            $data['guid'] = $notification->guid;
            $data['uid'] = $notification->origin_uid;
            $data['is_certified'] = (bool) $notification->is_certified;
            // Sólo si hemos superado el máximo número de envios respondemos con el error final
            if (is_null($notification->response_code) || $notification->response_code == 0) {
                // Pendiente de envío o envío satisfactorio
                $data['code'] = (is_null($notification->response_code) ? "" : $notification->response_code);
                $data['info'] = $notification->response_info;
                $data['sent_at'] = $notification->sent_at;
                $data['status'] = (is_null($notification->response_code) ? 'pending' : 'sent');
                // Si ademas de estar enviado tenemos un certificado, lo devolvemos
                if ($notification->is_certified && !empty($notification->certificate_file)) {
                    $fileName = $notification->certificate_file;
                    if (file_exists(storage_path("app/" . $fileName))) {
                        $data['crt_doc'] = base64_encode(Storage::disk('local')->get($fileName));
                    }
                }
            } else {
                // Verificamos si hemos llegado al máximo de intentos
                if ($notification->retries >= $maxReintentos || $notification->status_slug == 'error') {
                    // Devolvemos el error
                    $data['code'] = $notification->response_code;
                    $data['info'] = $notification->response_info;
                    $data['sent_at'] = $notification->sent_at;
                    $data['status'] = 'error';
                } else {
                    // Devolvemos como si estuviesemos pendientes todavía de enviar
                    $data['code'] = '';
                    $data['info'] = '';
                    $data['sent_at'] = null;
                    $data['status'] = 'retrying';
                }
            }

            $headers = ['X-Authorization-Token' => "{$this->getToken($request)}"];
            return response()->json(
                $data,
                Response::HTTP_OK,
                $headers
            );
        } catch (\Exception $e) {
            return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            //return $this->respondWithCustom(
                // Response::HTTP_INTERNAL_SERVER_ERROR,
                // Response::HTTP_INTERNAL_SERVER_ERROR,
                // $e->getMessage());
        }
    }

    protected function saveFileData(Request $request)
    {
        try {
            // Verificamos la existencia de la carpeta de ficheros de notificacion
            if (!Storage::disk('local')->exists("/notifications")) {
                Storage::disk('local')->makeDirectory("/notifications", 0775, true); //creates directory
            }

            // Creamos carpetas por años-meses para ordenarlas
            $dateDir = Carbon::now()->format("Ym");
            if (!Storage::disk('local')->exists("/notifications/" . $dateDir)) {
                Storage::disk('local')->makeDirectory("/notifications/" . $dateDir, 0775, true); //creates directory
            }

            $data = json_encode($request->all());
            $fileName = "notifications/" . $dateDir . "/" . Uuid::uuid4()->toString() . ".json";
            Storage::disk('local')->put(
                $fileName,
                $data
            );
        } catch (Exception $ex) {
            $fileName = "";
        }

        return $fileName;
    }

    protected function isCorrectMobileNumber($to)
    {
        // Esta es la expresión regular para comprobar que un número de telefono es correcto en España.
        // Tiene que empezar por 6 o por 7 y tener un total de 9 dígitos.
        $expresion = '/^[6|7][0-9]{8}$/';
        if (preg_match($expresion, $to)) {
            return true;
        }

        return false;
    }

    protected function isBlacklisted($to, $slug)
    {
        $isBlacklisted = Blacklist::where('slug', $slug)
            ->where('to', strtolower($to))
            ->count();

        return ($isBlacklisted > 0);
    }
}
