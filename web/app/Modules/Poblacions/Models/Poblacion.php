<?php

namespace App\Modules\Poblacions\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Poblacion extends Model
{
    protected $table = "poblacions";

    protected $dates = ['created_at',
    'updated_at',
    ];

    protected $fillable = ['created_at',
    'updated_at',
    'name',
    'description',
    'active',
    'code',
    'pais_id',
    ];

    

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }
}
