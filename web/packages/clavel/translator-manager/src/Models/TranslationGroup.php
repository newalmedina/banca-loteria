<?php


namespace Clavel\TranslatorManager\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationGroup extends Model
{
    protected $table = 'translations_group';

    protected $guarded = array('id', 'created_at', 'updated_at');
}
