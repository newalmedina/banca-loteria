<?php

// A tener en cuenta
// https://stackoverflow.com/questions/41274477/laravel-5-3-single-notification-for-user-collection-followers?answertab=votes#tab-top

namespace App\Http\Controllers\SystemNotification;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class AdminSystemNotificationController extends AdminController
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
                $q->where('visibility', 'admin')->orWhere('visibility', '=', '');
            })->get();
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
        return redirect()->back();
    }
}
