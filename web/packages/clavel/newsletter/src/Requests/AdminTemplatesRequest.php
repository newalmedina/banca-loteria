<?php

namespace App\Modules\Newsletter\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminTemplatesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-templates-create') || !auth()->user()->can('admin-templates-update')) {
            return false;
        }

        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = array(
            'nombre' => 'required',
        );

        return $rules;
    }

    public function attributes()
    {
        return array(
            'nombre' => trans("templates/admin_lang.nombre"),
        );
    }
}
