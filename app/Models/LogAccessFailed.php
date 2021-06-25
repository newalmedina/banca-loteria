<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAccessFailed extends Model
{
    //
    protected $table = "logaccess_failed";
    protected $fillable = ['user_id', 'username', 'ip_address', 'event', 'password'];
}
