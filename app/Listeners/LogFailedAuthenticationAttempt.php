<?php

namespace App\Listeners;

use App\Events\LogFailed;
use App\Models\LogAccessFailed;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Request;

class LogFailedAuthenticationAttempt
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param LogFailed $event
     * @return void
     */
    public function handle($event)
    {
        $log = new LogAccessFailed();
        $log->user_id = empty($event->user->id) ? 0 : $event->user->id;
        $log->username = empty($event->credentials['username']) ? 'No informado' : $event->credentials['username'];
        $log->ip_address = Request::getClientIp();
        $log->event = trans('auth.log_failed');
        $log->password = $event->credentials['password'];
        $log->save();
    }
}
