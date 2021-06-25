<?php

namespace Clavel\Elearning\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class AsignaturaRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        $this->locale = config('app.default_locale');
        $this->validationRules['userlang.' . $this->locale . '.titulo'] = 'required';
    }

    public function authorize()
    {
        if (!empty(Auth::user())) {
            return true;
        }
        return false;
    }

    public function rules()
    {
        // Si recibimos el titulo en alguno de los idiomas disponibles no validamos este campo

        if (isset($this->request)) {
            $userlangs = $this->input("userlang");
            foreach ($userlangs as $userlang) {
                if (!empty($userlang["titulo"])) {
                    unset($this->validationRules['userlang.' . $this->locale . '.titulo']);
                }
            }
        }
        return $this->validationRules;
    }

    public function attributes()
    {
        return array(
            'userlang.' . $this->locale . '.titulo' => trans('elearning::asignaturas/admin_lang.titulo')
        );
    }
}
