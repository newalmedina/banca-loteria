<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class FrontResetPasswordNotification extends Notification
{
    use Queueable;

    protected $token = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        //
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $payload = [
            'to' => $notifiable->email,
            'senderName' => trans("general/front_custom_lang.email.sender_name"),
            'css' => "",
            'logo' => [
                'path' => config('app.url') . trans("general/front_custom_lang.email.logo"),
                'width' => trans("general/front_custom_lang.email.logo_width"),
                'height' => trans("general/front_custom_lang.email.logo_height")
            ],
            'address' =>   trans("general/front_custom_lang.email.address"),
            'reminder' =>  trans("general/front_custom_lang.email.reminder", [ 'url' => config('app.url')]),
            /*'unsubscribe' =>  trans("general/front_custom_lang.email.unsubscribe", [ 'url' => config('app.url')]),*/
            'password' =>   env('APP_URL') . "/password/reset",
            /*'linkedin' =>  trans("general/front_custom_lang.email.linkedin"),
            'twitter' =>  trans("general/front_custom_lang.email.twitter"),
            'facebook' =>  trans("general/front_custom_lang.email.facebook"),
            'youtube' =>  trans("general/front_custom_lang.email.youtube"),
            */
        ];

        return (new MailMessage)
            ->from(env("MAIL_FROM_ADDRESS"), env("MAIL_FROM_NAME"))
            ->subject(trans("auth/lang.subjectemailpassword")." ".config('app.name'))
            ->view(
                'front.email.reset_password',
                ['token' => $this->token, 'payload' => $payload]
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
