<?php

namespace App\Modules\pruebas\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class prueba extends Model
{
    

    

    protected $table = "pruebas";

    protected $dates = ['created_at',
'updated_at',
];

    protected $fillable = ['created_at',
'updated_at',
'name',
'description',
'active',
];

    

    
}
