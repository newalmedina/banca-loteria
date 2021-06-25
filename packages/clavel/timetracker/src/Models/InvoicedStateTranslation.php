<?php


namespace Clavel\TimeTracker\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicedStateTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'invoiced_state_translations';

    public function projectInvoicedState()
    {
        return $this->belongsTo('App\Models\InvoicedState');
    }
}
