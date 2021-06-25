<?php

namespace App\Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterRow extends Model
{
    protected $table = 'newsletter_rows';
    protected $fillable = [];
    protected $guarded = [];

    public function newsletter()
    {
        return $this->belongsTo('App\Modules\Newsletter\Models\Newsletter', 'newsletter_id', 'id');
    }

    public function newsletterFields()
    {
        return $this->hasMany('App\Modules\Newsletter\Models\NewsletterField')->orderBy("position");
    }
}
