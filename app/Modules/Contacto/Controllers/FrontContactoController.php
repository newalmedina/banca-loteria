<?php

namespace App\Modules\Contacto\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Contacto\Requests\ContactRequest;
use App\Models\User;

use App\Services\MailSpamService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use ReCaptcha\ReCaptcha;

class FrontContactoController extends Controller
{
    public function create()
    {
        $page_title = trans("Contacto::front_lang.Contacto");

        $user = (auth()->user()!=null) ? auth()->user() : new User();
        $form_data = array('route' => array('contactus'), 'method' => 'POST', 'id' => 'contact-form');

        return view('Contacto::front_contact_form', compact('page_title', 'user', 'form_data'));
    }

    public function store(ContactRequest $request)
    {
        $recaptcha = $request->get("g-recaptcha-response", "");

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $channel = curl_init();
        curl_setopt_array($channel, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'secret' => env('RECAPTCHA_SITE_KEY', ''),
                'response' => $recaptcha,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $output = curl_exec($channel);
        curl_close($channel);
        $captcha_success = json_decode($output);
        if (!$captcha_success->success) {
            // Eres un robot
            return redirect()->to('contactus')
                ->with('error', trans("Contacto::front_lang.errorform"));
        }

        // Vemos si el honey pot de fax se ha marcado
        if (!empty($request->input('faxonly', ""))) {
            // Eres un robot pero te decimos que si que molas para que no sospeches. Putos spammers
            return redirect()->to('contactus')
                ->with('success', trans("Contacto::front_lang.okform_bots"));
        }

        // Ahora recuperamos realmente todos los campos reales
        $fullname = $request->input('fullname');
        $email = $request->input('email');
        $description = $request->input('message');

        // Vemos si hay palabras que sabemos que son restringidas
        if (MailSpamService::hasForbiddenWords($description)) {
            // Eres un robot pero te decimos que si que molas para que no sospeches. Putos spammers
            return redirect()->to('contactus')
                ->with('success', trans("Contacto::front_lang.okform_bots"));
        }


        // Enviamos la confirmación de su solicitud al usuario
        // No se la enviamos por que parece que tenemos algun hackercete cabron
        Mail::send(
            'Contacto::email',
            compact('fullname', 'email', 'description'),
            function ($message) use ($fullname, $email) {
                $message
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->to($email, $fullname)
                    ->subject(trans('Contacto::front_lang.email_account_confirmation').env("APP_NAME"));
            }
        );

        // Enviamos una notificacion de contacto al administrador
        Mail::send(
            'Contacto::email_notice',
            compact('fullname', 'email', 'description'),
            function ($message) {
                $message
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->to(env("MAIL_FROM_ADDRESS", ''), env("MAIL_FROM_NAME", ''))
                    ->subject(trans('Contacto::front_lang.email_account_confirmation').env("APP_NAME"));
            }
        );

        return redirect()->to('contactus')
            ->with('success', trans("Contacto::front_lang.okform"));
    }
}
