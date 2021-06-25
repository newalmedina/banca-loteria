<?php

namespace App\Modules\Pais\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pais extends Model
{
    protected $table = "pais";

    protected $dates = ['created_at',
    'updated_at',
    ];

    protected $fillable = ['created_at',
    'updated_at',
    'name',
    'description',
    'active',
    'code',
    ];
}
