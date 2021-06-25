<?php

namespace Clavel\Posts\Notifications;

use App\Models\User;
use App\Notifications\CustomDbChannel;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class NewPostNotification extends Notification
{
    use Queueable;

    private $post;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomDbChannel::class, 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $user = User::find($notifiable->id);
        $payload = [
            'to' => "info@aduxia.com",
            'senderName' => "Emocional.reg",
            'css' => "",
            'logo' => [
                'path' => Config::get('app.url') . "/assets/front/img/email/logo.png",
                'width' => 401,
                'height' => 81
            ],
            'reminder' =>  '<a href="https://www.emocionalreg.com/aviso-legal/" target="_blank">Aviso legal</a>',
            'reminder' =>  'Este email no recibe comunicaciones entrantes. Si tienes cualquier
                    pregunta o solicitud y quieres contactar con nosotros,
                    escrÍbenos a <a href="mailto:info@emocionalreg.com" target="_blank">
                    <strong>info@emocionalreg.com</strong></a>.',
            'unsubscribe' =>  '<a href="https://www.emocionalreg.com/" target="_blank">Emocionalreg.com</a>',
            'password' =>   env('APP_URL') . "/password/reset",
            'linkedin' =>  "emocionalreg-com",
            'twitter' =>  "emocionalreg",
            'facebook' =>  "emocionalreg",
            'youtube' =>  "emocionalreg",
            'address' => "B-?????"
        ];
        return (new MailMessage)->view(
            'posts::mail.email_post',
            ['post' => $this->post, 'user' => $user, 'payload' => $payload]
        );
        /*

          return (new MailMessage)->view(
              'posts::mail.email_post', ['post' => $this->post, 'user' => $user]
          );*/
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'id' => $this->post->id,
            'title' => $this->post->title,
            'date' => $this->post->date_post_formatted,
            'url' =>  url('posts/post/' . $this->post->url_seo),
            'visibility' => 'front'
        ];
    }
}
