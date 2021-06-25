<?php
namespace Clavel\CrudGenerator\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-modulos-crud-create') ||
            !auth()->user()->can('admin-modulos-crud-update')) {
            return false;
        }

        return true;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => trans('crud-generator::modules/admin_lang.title_required'),
            'model.required' => trans('crud-generator::modules/admin_lang.model_required'),

        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'model' => 'required',
        ];
    }
}
