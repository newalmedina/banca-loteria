<?php

namespace Clavel\TimeTracker\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function type()
    {
        return $this->HasOne(ProjectType::class, "id", "project_type_id");
    }

    public function customer()
    {
        return $this->HasOne(Customer::class, "id", "customer_id");
    }

    public function responsable()
    {
        return $this->HasOne(User::class, "id", "responsable_id");
    }
    public function facturado()
    {
        return $this->HasOne(InvoicedState::class, "id", "invoiced");
    }


    public function scopeActives($query)
    {
        return $query->where("active", true);
    }

    public function scopeNotHistorified($query)
    {
        return $query->where("historified", false);
    }

    public function getExpireAtFormattedAttribute()
    {
        if ($this->expire_at!=null && $this->expire_at != '') {
            $expire_at = new Carbon($this->expire_at);
            return $expire_at->format('d/m/Y');
        }

        return "";
    }

    public function getClosedAtFormattedAttribute()
    {
        if ($this->closed_at!=null && $this->closed_at != '') {
            $closed_at = new Carbon($this->closed_at);
            return $closed_at->format('d/m/Y');
        }

        return "";
    }
}
