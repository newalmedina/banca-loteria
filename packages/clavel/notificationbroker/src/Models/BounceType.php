<?php

namespace Clavel\NotificationBroker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BounceType extends Model
{
    use SoftDeletes;

    protected $table = "bouncetypes";

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'created_at',
        'updated_at',
        'deleted_at',
        'name',
        'description',
        'active',
    ];
}
