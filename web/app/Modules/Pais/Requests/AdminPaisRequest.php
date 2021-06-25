<?php

namespace App\Modules\Pais\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPaisRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        parent::__construct();

        
        $this->validationRules['active'] = 'required';
        $this->validationRules['name'] = 'required';
        $this->validationRules['code'] = 'required';
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-pais-create') || !auth()->user()->can('admin-pais-update')) {
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
        return array(
            'active.required' => trans('Pais::pais/admin_lang.fields.active_required'),
        'name.required' => trans('Pais::pais/admin_lang.fields.name_required'),
        'code.required' => trans('Pais::pais/admin_lang.fields.code_required')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->validationRules;
    }
}
