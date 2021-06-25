<?php

namespace App\Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterTemplates extends Model
{
    protected $table = 'newsletter_templates';
    protected $fillable = [];
    protected $guarded = [];

    public function scopeActivos($query)
    {
        return $query->where("active", 1);
    }

    public function newsletters()
    {
        return $this->belongsTo('App\Modules\Newsletter\Models\Newsletter');
    }
}
