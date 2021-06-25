<?php

namespace Clavel\TimeTracker\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSheet extends Model
{
    protected $table = "timesheet";

    public function project()
    {
        return $this->HasOne(Project::class, "id", "project_id");
    }
    public function customer()
    {
        return $this->HasOne(Customer::class, "id", "id");
    }

    public function activity()
    {
        return $this->HasOne(Activity::class, "id", "activity_id");
    }

    public function getStartTimeFormattedAttribute()
    {
        if ($this->start_time != null && $this->start_time != '') {
            try {
                $start_time = new \Carbon\Carbon($this->start_time);
                return $start_time->format('d/m/Y H:i');
            } catch (\Exception $ex) {
            }
        }

        return "";
    }

    public function getEndTimeFormattedAttribute()
    {
        if ($this->end_time != null && $this->end_time != '') {
            try {
                $end_time = new \Carbon\Carbon($this->end_time);
                return $end_time->format('d/m/Y H:i');
            } catch (\Exception $ex) {
            }
        }

        return "";
    }
}
