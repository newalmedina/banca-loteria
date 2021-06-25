<?php
namespace Clavel\Elearning\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ModuloConvocatoriaRequest extends FormRequest
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
            'fecha_inicio' => 'required|date_format:"d/m/Y"',
            'fecha_fin' => 'required|date_format:"d/m/Y"',
            'porcentaje' => 'integer|max:100',
        );
    }

    public function attributes()
    {
        return array(
            'fecha_inicio' => trans('elearning::convocatorias/admin_lang.fecha_inicio'),
            'fecha_fin' => trans('elearning::convocatorias/admin_lang.fecha_inicio'),
            'porcentaje' => trans('elearning::convocatorias/admin_lang.porcentaje'),
        );
    }
}
