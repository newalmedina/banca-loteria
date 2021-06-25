<?php namespace App\Services;

use App\Models\Idioma;
use Illuminate\Support\Facades\DB;

class LanguageService
{
    protected $locale;

    public function __construct($locale = '')
    {
        $this->locale = ($locale=="") ? app()->getLocale() : $locale;
    }

    public function getTranslations($item)
    {
        // Array de retorno
        $a_trans = array();

        // Idiomas disponibles
        $locale = config('app.default_locale');
        $idiomas = Idioma::where("active", "=", "1")
            ->orderByRaw(DB::raw("IF(code='".$locale."','0',id)"))
            ->get();

        //Primero pongo el idioma por defecto y después los demás
        foreach ($idiomas as $key => $value) {
            $a_trans[$value->code]["idioma"] = $value->name;
            $a_trans[$value->code]["id"] = ($item->translate($value->code)!=null) ?
                $item->translate($value->code)->id : null;
        }

        return $a_trans;
    }
}
