<?php

namespace Clavel\NotificationBroker\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];
    protected $table = "notifications_broker";

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(NotificationType::class);
    }

    public function group()
    {
        return $this->belongsTo(NotificationGroup::class);
    }

    public function status()
    {
        return $this->belongsTo(NotificationStatus::class, 'status_slug', 'slug');
    }

    public function getCreditsFormattedAttribute()
    {
        if ($this->credits!=null && $this->credits != '') {
            return number_format($this->credits, 2, ",", ".");
        }

        return "";
    }

    public function getSentAtFormattedAttribute()
    {
        if (!empty($this->sent_at)) {
            try {
                return Carbon::parse($this->sent_at)->format("d/m/Y H:i:s");
            } catch (\Exception $e) {
            }
        }

        return "";
    }

    public function getRetriesFormattedAttribute()
    {
        $resultado =  '';
        if (!empty($this->retries) && $this->retries>0 && $this->response_code >= 0) {
            $resultado =  $this->retries;
        }
        return $resultado;
    }
}
