<?php

namespace App\Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;
    public $translatedAttributes = ['subject'];
    public $translationForeignKey = 'newsletter_id';

    protected $table = 'newsletters';
    protected $fillable = [];
    protected $guarded = [];

    public function newsletterRows()
    {
        return $this->hasMany('App\Modules\Newsletter\Models\NewsletterRow')->orderBy("position");
    }

    public function scopeDataTableNewsletters($query)
    {
        return $query->join('newsletter_estates', 'newsletters.newsletter_estate_id', '=', 'newsletter_estates.id');
    }

    public function arrayPosts()
    {
        $a_return = array();

        if ($this->id!='') {
            foreach ($this->newsletterRows as $row) {
                $nX = 1;
                foreach ($row->newsletterFields as $field) {
                    $a_return[$row->id][$nX]["id"] = $field->id;
                    $a_return[$row->id][$nX]["in_box"] = $field->in_box;
                    if ($field->type=='post') {
                        $a_return[$row->id][$nX]["post"] = $field->getPost();
                    } else {
                        $a_return[$row->id][$nX]["value"] = $field->body;
                    }

                    $nX++;
                }
            }
        }

        return $a_return;
    }

    public function arrayPostsDesigner()
    {
        $a_return = array();

        if ($this->id!='') {
            foreach ($this->newsletterRows as $row) {
                $nX = 1;
                foreach ($row->newsletterFields as $field) {
                    $a_return[$row->id][$nX]["id"] = $field->id;
                    $a_return[$row->id][$nX]["in_box"] = $field->in_box;
                    if ($field->type=='post') {
                        $a_return[$row->id][$nX]["post"] = $field->getPostMultilang();
                    } else {
                        $a_return[$row->id][$nX]["value"] = $field->getContentMultilang();
                    }

                    $nX++;
                }
            }
        }

        return $a_return;
    }

    public function template()
    {
        return $this->belongsTo('App\Modules\Newsletter\Models\NewsletterTemplates');
    }
}
