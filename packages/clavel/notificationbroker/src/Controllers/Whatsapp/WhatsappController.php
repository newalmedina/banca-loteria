<?php

namespace Clavel\NotificationBroker\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;

class WhatsappController extends Controller
{
    const GOOD_BOY_URL = "https://images.unsplash.com/photo-1518717758536-85ae29035b6d?'.
        'ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*
         {
        "SmsMessageSid":"SMf7d78b04a765c5d17dff5eb4b93bce19",
        "NumMedia":"0",
        "SmsSid":"SMf7d78b04a765c5d17dff5eb4b93bce19",
        "SmsStatus":"received",
        "Body":"Hola mundo",
        "To":"whatsapp:+14155238886",
        "NumSegments":"1",
        "MessageSid":"SMf7d78b04a765c5d17dff5eb4b93bce19",
        "AccountSid":"AC61f00d53f63d4dd9d82cf27155906340",
        "From":"whatsapp:+34667786621",
        "ApiVersion":"2010-04-01"
        }
         */
        try {
            $SmsMessageSid = $request->input("SmsMessageSid", "");
            $NumMedia = $request->input("NumMedia", "");
            $SmsSid = $request->input("SmsSid", "");
            $SmsStatus = $request->input("SmsStatus", "");
            $Body = $request->input("Body", "");
            $To = str_replace("whatsapp:", "", $request->input("To", ""));
            $NumSegments = $request->input("NumSegments", "");
            $MessageSid = $request->input("MessageSid", "");
            $AccountSid = $request->input("AccountSid", "");
            $From = str_replace("whatsapp:", "", $request->input("From", ""));
            $ApiVersion = $request->input("ApiVersion", "");

            // Verificamos la existencia de la carpeta de ficheros de certificados
            if (!Storage::disk('local')->exists("/whatsapp")) {
                Storage::disk('local')->makeDirectory("/whatsapp", 0775, true); //creates directory
            }

            $id = Uuid::uuid4()->toString();
            if (!empty($SmsMessageSid)) {
                $id = $SmsMessageSid;
            }

            $fileName = "whatsapp/whatsapp_" .$id."_".$From."_".$To.'.txt';
            Storage::disk('local')->put($fileName, json_encode($request->all()));

            $brokerWhatsapp = \Clavel\Sms\lib\NotificationFactory::create('twillio-whatsapp', false, 'whatsapp');
            $notification = new \Clavel\NotificationBroker\Models\Notification();
            $notification->message = "Gracias!!! Hemos recibido tu mensaje.
                En breve nos ponemos en contacto contigo. Tu codigo es: ".$id;
            $notification->receiver = $From;
            $brokerWhatsapp->send($notification);

            $response = new MessagingResponse();
            if ($NumMedia === 0) {
                $message = $response->message("Hemos recibido tu mensaje! Sin imagen! Envianos una si quieres.");
            } else {
                $message = $response->message("Gracias por la imagen.");
                $message->media(GOOD_BOY_URL);
            }

            return $response;
        } catch (\Exception $ex) {
        }
    }
}
