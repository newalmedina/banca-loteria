<?php

namespace Clavel\Basic\Models;

use Illuminate\Database\Eloquent\Model;

class PageProviderTranslation extends Model
{
    protected $table = 'page_provider_translations';
    public $timestamps = false;

    protected $fillable = ['name', 'value'];
}
