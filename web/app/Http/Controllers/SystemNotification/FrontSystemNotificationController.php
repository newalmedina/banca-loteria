<?php


namespace App\Http\Controllers\SystemNotification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontSystemNotificationController extends Controller
{
    public function mark(Request $request)
    {
        $notification = auth()->user()->notifications()->find($request->input("notify_id", ""));
        if ($notification) {
            $notification->markAsRead();
            if (!empty($notification->data['url'])) {
                return redirect($notification->data['url']);
            }
        }
        return redirect()->back();
    }

    public function markAll()
    {
        $notifications = auth()->user()->unreadNotifications()
            ->where(function ($q) {
                $q->where('visibility', 'front')->orWhere('visibility', '=', '');
            })->get();
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
        return redirect()->back();
    }
}
