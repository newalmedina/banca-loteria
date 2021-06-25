<?php
namespace Clavel\Elearning\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class GruposRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function authorize()
    {
        if (!empty(Auth::user())) {
            return true;
        }
        return false;
    }

    public function rules()
    {
        return array(
            'nombre' => 'required'
        );
    }

    public function attributes()
    {
        return array(
            'nombre' => trans('grupos/admin_lang.nombre')
        );
    }
}
