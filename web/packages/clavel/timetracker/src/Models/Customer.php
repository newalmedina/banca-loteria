<?php

namespace Clavel\TimeTracker\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function getCountryAttribute()
    {
        if (empty($this->country)) {
            return "ES";
        }

        return $this->country;
    }

    public function getCurrencyAttribute()
    {
        if (empty($this->currency)) {
            return "EUR";
        }

        return $this->currency;
    }

    public function scopeActives($query)
    {
        return $query->where("active", true);
    }
}
