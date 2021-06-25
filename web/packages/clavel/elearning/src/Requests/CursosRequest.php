<?php
namespace Clavel\Elearning\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CursosRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        $this->locale = config('app.default_locale');
        $this->validationRules['userlang.'.$this->locale.'.nombre'] = 'required';
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
            'userlang.'.$this->locale.'.nombre' => trans('elearning::cursos/admin_lang.nombre'),
        );
    }
}
