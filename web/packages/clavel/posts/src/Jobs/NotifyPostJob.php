<?php

namespace  Clavel\Posts\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Clavel\Posts\Models\Post;
use Clavel\Posts\Notifications\NewPostNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Notification;

class NotifyPostJob implements ShouldQueue
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

    protected $post;

    /**
     * Create a new job instance.
     *
     * @param Post $post
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Leemos los usuarios a los que enviaremos la notificación
            $users = User::get();
            foreach ($users as $user) {
                Notification::send($user, new NewPostNotification($this->post));
            }
        } catch (\Exception $e) {
            return;
        }
    }
}
