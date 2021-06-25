<?php

namespace Clavel\Posts\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;

    public $translatedAttributes = ['title', 'url_seo', 'body', 'meta_title', 'meta_content'];

    public function getDatePostFormattedAttribute()
    {
        if (!empty($this->date_post)) {
            $date_post = new Carbon($this->date_post);
            return $date_post->format('d/m/Y');
        }

        return "";
    }

    public function getDateHumanFormattedAttribute()
    {
        if (!empty($this->date_post)) {
            $date_post = new Carbon($this->date_post);
            return $date_post->format('d M Y');
        }

        return "";
    }

    public function getDateDeactivationFormattedAttribute()
    {
        if ($this->date_deactivation!=null && $this->date_deactivation != '') {
            $date_deactivation = new Carbon($this->date_deactivation);
            return $date_deactivation->format('d/m/Y');
        }

        return "";
    }

    public function getDateActivationFormattedAttribute()
    {
        if ($this->date_activation!=null && $this->date_activation != '') {
            $date_activation = new Carbon($this->date_activation);
            return $date_activation->format('d/m/Y');
        }

        return "";
    }

    public function getDateDeactivationHomeFormattedAttribute()
    {
        if ($this->date_deactivation_home!=null && $this->date_deactivation_home != '') {
            $date_deactivation_home = new Carbon($this->date_deactivation_home);
            return $date_deactivation_home->format('d/m/Y');
        }

        return "";
    }

    public function roles()
    {
        return $this->belongsToMany('Clavel\Posts\Models\PostRoles', 'post_role', 'post_id', 'role_id')
            ->withPivot('post_id')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany('Clavel\Posts\Models\PostImage');
    }

    public function comments()
    {
        return $this->hasMany('Clavel\Posts\Models\PostComment');
    }

    public function author()
    {
        return $this->hasOne('App\Models\User', 'id', 'author_id');
    }

    public function scopeOnlyHomeActive($query)
    {
        return $query->where('active', 1)
            ->where('in_home', 1)
            ->whereRaw(
                "(date_deactivation_home > ? or COALESCE(date_deactivation_home,'') = '')",
                [Carbon::today()]
            )
            ->whereRaw(
                "((date_activation < ? OR COALESCE(date_activation,'') = '') 
                            AND (date_deactivation > ? OR COALESCE(date_deactivation,'') = ''))",
                [
                    Carbon::today(),
                    Carbon::today()
                ]
            );
    }

    public function scopeActivePosts($query)
    {
        return $query->where('active', 1)
            ->whereRaw(
                "((date_activation < ? OR COALESCE(date_activation,'') = '') 
                AND (date_deactivation > ? OR COALESCE(date_deactivation,'') = ''))",
                [
                    Carbon::today(),
                    Carbon::today()
                ]
            );
    }

    public function getLeadNewAttribute()
    {
        // Quitamos algunos tipos de tags que no queremos que salgan en el resumen

        $tags = array("video");

        $stringToTidy = preg_replace('#<(' . implode('|', $tags) . ')(?:[^>]+)?>.*?</\1>#s', '', $this->body);
        $stringToTidy = substr($stringToTidy, 0, 200)." [...]";
        $config = array(
            'indent'         => true,
            'output-xhtml'   => true,
            'wrap'           => 5);

        // Tidy
        $tidy = tidy_parse_string($stringToTidy, $config, 'utf8');
        $tidy->cleanRepair();

        return $tidy;
    }

    public function getDatePostHomeFormattedAttribute()
    {
        $this->dateLocale();
        $dateInfo = new Carbon($this->date_post);
        return ucfirst($dateInfo->formatLocalized('%B, %d %Y'));
    }

    public function getDayPostHomeFormattedAttribute()
    {
        $this->dateLocale();
        $dateInfo = new Carbon($this->date_post);
        return ucfirst($dateInfo->formatLocalized('%d'));
    }

    public function getMonthPostHomeFormattedAttribute()
    {
        $this->dateLocale();
        $dateInfo = new Carbon($this->date_post);
        return ucfirst($dateInfo->formatLocalized('%b'));
    }

    public function getPostDayAttribute()
    {
        $dateInfo = new Carbon($this->date_post);
        return $dateInfo->format('d');
    }

    public function getPostMonthAttribute()
    {
        $this->dateLocale();
        $dateInfo = new Carbon($this->date_post);
        return ucfirst(substr($dateInfo->formatLocalized('%B'), 0, 3));
    }

    private function dateLocale()
    {
        /* Esto es una chapuza que tengo que mirar como solucionar */
        Carbon::setLocale(app()->getLocale());
        $strLocale = app()->getLocale()."_".strtoupper(app()->getLocale());
        setlocale(LC_CTYPE, $strLocale.'.utf8');
        setlocale(LC_ALL, $strLocale.'.utf8');
        /* Fin de la chapuza */
    }

    public function tags()
    {
        return $this->belongsToMany('Clavel\Posts\Models\PostTag');
    }

    public function tagSelected($tag_id)
    {
        if (isset($this->attributes["id"])) {
            return (self::join("post_post_tag", "posts.id", "=", "post_post_tag.post_id")
                    ->where("post_post_tag.post_tag_id", "=", $tag_id)
                    ->where("post_post_tag.post_id", "=", $this->attributes["id"])
                    ->count()>0) ? true : false;
        } else {
            return false;
        }
    }

    public function scopeDataValue($query, $search)
    {
        $pivot = $this->tagPivot()->getTable();

        return $query->whereHas('tagPivot', function ($q) use ($search, $pivot) {
            $q->where("{$pivot}.post_tag_id", $search);
        });
    }

    public function tagPivot()
    {
        return $this->belongsToMany('Clavel\Posts\Models\PostTag', 'post_post_tag')
            ->withPivot('post_id');
    }

    public function leadNewsletter($numWords = 0, $idioma = '')
    {
        if ($idioma=="") {
            $idioma = config("app.locale");
        }
        if ($numWords==0) {
            $numWords=500;
        }
        $stringToTidy = substr($this->{'resumen:'.$idioma}, 0, $numWords);
        $config = array(
            'indent'         => true,
            "show-body-only" => true,
            "show-warnings"  => false,
            'output-xhtml'   => true,
            'wrap'           => 5);

        // Tidy
        $tidy = tidy_parse_string($stringToTidy, $config, 'utf8');
        $tidy->cleanRepair();

        return $tidy->value;
    }
}
