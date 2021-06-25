<?php

namespace Clavel\NotificationBroker\Models;

use Illuminate\Database\Eloquent\Model;

class BouncedEmail extends Model
{
    protected $table = "bouncedemails";

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'created_at',
        'updated_at',
        'description',
        'active',
        'bounce_type_id',
        'bounce_code',
        'email',
    ];


    public function bounceType()
    {
        return $this->belongsTo(BounceType::class, 'bounce_type_id');
    }
}
