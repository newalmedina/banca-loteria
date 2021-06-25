<?php

namespace App\Modules\Events\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;
    protected $table = 'events';

    public $translatedAttributes = ['title', 'url_seo', 'body', 'localization', 'link'];


    /**
     * Returs the creator of the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getDateStartFormattedAttribute()
    {
        if (!empty($this->date_start)) {
            $date_start = new Carbon($this->date_start);
            return $date_start->format('d/m/Y');
        }

        return "";
    }

    public function getDateEndFormattedAttribute()
    {
        if ($this->date_end!=null && $this->date_end != '') {
            $date_end = new Carbon($this->date_end);
            return $date_end->format('d/m/Y');
        }

        return "";
    }

    public function getDateStartListFormattedAttribute()
    {
        $this->dateLocale();
        $dateInfo = new Carbon($this->date_start);
        return ucfirst($this->getNewMonthAttribute().", ".$dateInfo->formatLocalized('%d %Y'));
    }

    public function getNewMonthAttribute()
    {
        $this->dateLocale();
        $dateInfo = new Carbon($this->date_start);
        return ucfirst(substr($dateInfo->formatLocalized('%B'), 0, 3));
    }

    private function dateLocale()
    {
        /* Esto es una chapuza que tengo que mirar como solucionar */
        Carbon::setLocale(config('app.locale'));
        $strLocale = config('app.locale')."_".strtoupper(config('app.locale'));
        setlocale(LC_CTYPE, $strLocale.'.utf8');
        setlocale(LC_ALL, $strLocale.'.utf8');
        /* Fin de la chapuza */
    }

    public function roles()
    {
        return $this->belongsToMany('App\Modules\Events\Models\EventRoles', 'event_role', 'event_id', 'role_id')
            ->withPivot('event_id')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany('App\Modules\Events\Models\EventImage');
    }

    public function scopeOnlyHomeActive($query)
    {
        return $query->where('active', 1)
            ->where('in_home', 1);
    }

    public function scopeActiveEvents($query)
    {
        return $query->where('active', 1);
    }

    public function getLeadEventAttribute()
    {
        $stringToTidy = substr($this->body, 0, 300)." [...]";
        $config = array(
            'indent'         => true,
            'output-xhtml'   => true,
            'wrap'           => 5);

        // Tidy
        $tidy = tidy_parse_string($stringToTidy, $config, 'utf8');
        $tidy->cleanRepair();

        return strip_tags($tidy);
    }

    public function tags()
    {
        return $this->belongsToMany('App\Modules\Events\Models\EventTag');
    }

    public function favourites()
    {
        return $this->hasMany('App\Modules\Events\Models\EventFavourite');
    }

    public function tagSelected($tag_id)
    {
        if (isset($this->attributes["id"])) {
            return (self::join("event_event_tag", "events.id", "=", "event_event_tag.event_id")
                    ->where("event_event_tag.event_tag_id", "=", $tag_id)
                    ->where("event_event_tag.event_id", "=", $this->attributes["id"])
                    ->count()>0) ? true : false;
        } else {
            return false;
        }
    }

    public function scopeDataValue($query, $search)
    {
        $pivot = $this->tag_pivot()->getTable();

        return $query->whereHas('tag_pivot', function ($q) use ($search, $pivot) {
            $q->where("{$pivot}.event_tag_id", $search);
        });
    }

    public function scopeDataValues($query, array $search, $bool = 'and')
    {
        $pivot = $this->tag_pivot()->getTable();

        $query->whereHas('tag_pivot', function ($q) use ($search, $pivot, $bool) {
            $q->where(function ($q) use ($search, $pivot, $bool) {
                foreach ($search as $field => $value) {
                    if (is_array($value)) {
                        $field = key($value);
                        $q->where("{$pivot}.{$field}", $value[$field][0], $value[$field][1], $bool);
                    } else {
                        $q->where("{$pivot}.{$field}", $value[0], $value[1], $bool);
                    }
                }
            });
        });
    }

    public function tagPivot()
    {
        return $this->belongsToMany('App\Modules\Events\Models\EventTag', 'event_event_tag')
            ->withPivot('event_id');
    }

    public function eventNext()
    {
        return $this
            ->where('date_start', ">=", $this->attributes["date_start"])
            ->where("id", "<>", $this->attributes["id"])
            ->orderBy("date_start", "ASC")
            ->activeEvents()
            ->count();
    }

    public function eventPrevious()
    {
        return $this
            ->where('date_start', "<=", $this->attributes["date_start"])
            ->where("id", "<>", $this->attributes["id"])
            ->orderBy("date_start", "DESC")
            ->activeEvents()
            ->count();
    }

    public function eventsRelated()
    {
        if ($this->tags()->count()>0) {
            $a_sender_array = [];

            foreach ($this->tags as $value) {
                $a_sender_array[]["event_tag_id"] = array("=", $value->id);
            }
            $a_sender_array[]["id"] = array("=", $this->attributes["id"]);

            // Se puede hacer también la llamada al array de values ['value' => 'findMe', 'otherField' => 'findMeToo']
            return $this->dataValues($a_sender_array, 'or')
                ->where("id", "<>", $this->attributes["id"])
                ->where("date_start", ">=", Carbon::today())
                ->limit(4)
                ->get();
        }
        return null;
    }
}
