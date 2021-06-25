<?php

namespace Clavel\Elearning\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class PreguntaRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        $this->locale = config('app.default_locale');
        $this->validationRules['userlang.'.$this->locale.'.nombre'] = 'required';
        $this->validationRules['tipo_pregunta_id'] = 'required';
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
        return $this->validationRules;
    }

    public function attributes()
    {
        return array(
            'userlang.'.$this->locale.'.nombre' => trans('contenidos/admin_lang.nombre_contenido'),
            'tipo_pregunta_id' => trans('contenidos/admin_lang.tipos')
        );
    }
}
