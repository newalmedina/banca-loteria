<?php

namespace Clavel\Elearning\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ConvocatoriaModuloRequest extends FormRequest
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
            'fecha_inicio' => trans('convocatorias/admin_lang.fecha_inicio'),
            'fecha_fin' => trans('convocatorias/admin_lang.fecha_inicio'),
            'porcentaje' => trans('convocatorias/admin_lang.porcentaje'),
        );
    }
}
