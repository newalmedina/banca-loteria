<?php

namespace Clavel\Elearning\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ContenidoRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        $this->locale = config('app.default_locale');
        $this->validationRules['userlang.'.$this->locale.'.nombre'] = 'required';
        $this->validationRules['tipo_contenido_id'] = 'required';
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
        if (isset($this->request)) {
            $userlangs = $this->input("userlang");
            foreach ($userlangs as $userlang) {
                if (!empty($userlang["nombre"])) {
                    unset($this->validationRules['userlang.' . $this->locale . '.nombre']);
                }
            }
        }
        return $this->validationRules;
    }

    public function attributes()
    {
        return array(
            'userlang.'.$this->locale.'.nombre' => trans('contenidos/admin_lang.nombre'),
            'tipo_contenido_id' => trans('elearning::contenidos/admin_lang.tipo_contenido')
        );
    }
}
