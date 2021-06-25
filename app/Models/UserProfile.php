<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    //
    protected $table = "user_profiles";

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    public function scopeUsers($query)
    {
        return $query->join('users', 'users.id', '=', 'user_profiles.user_id');
    }


    public function getFullNameAttribute()
    {
        return trim($this->attributes['first_name'] . " " . $this->attributes['last_name']);
    }

    public function getBirthdateFormattedAttribute()
    {
        if (!empty($this->birthdate)) {
            return (Carbon::createFromFormat('Y-m-d', $this->birthdate))->format('d/m/Y');
        }

        return '';
    }
}
