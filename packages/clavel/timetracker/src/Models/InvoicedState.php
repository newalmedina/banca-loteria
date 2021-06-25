<?php


namespace Clavel\TimeTracker\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class InvoicedState extends Model
{
    use Translatable;

    public $useTranslationFallback = true;

    public $translatedAttributes = ['name', 'description'];

    protected $table = 'invoiced_states';

    public function scopeActives($query)
    {
        return $query->where("active", true);
    }
}
