<?php
namespace Clavel\Elearning\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CodigoAsignaturaRequest extends FormRequest
{
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
            'codigo' => 'required'
        );
    }

    public function attributes()
    {
        return array(
            'codigo' => trans('elearning::asignaturas/front_lang.codigo_required')
        );
    }
}
