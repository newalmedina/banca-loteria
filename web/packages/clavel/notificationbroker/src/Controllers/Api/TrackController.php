<?php namespace Clavel\NotificationBroker\Controllers\Api;

use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Clavel\NotificationBroker\Models\Notification;
use Illuminate\Http\Request;

class TrackController extends ApiController
{
    public function trackonline(Request $request, $guid)
    {
        $notification = Notification::where("guid", $guid)->first();
        if (!empty($notification) && empty($notification->validated_at)) {
            $notification->validated_at = Carbon::now();
            $notification->save();
        }

        $pathToFile = resource_path('/assets/front/img/spacer.gif');
        return response()->file($pathToFile);
    }
}
