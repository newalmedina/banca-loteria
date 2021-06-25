<?php

namespace App\Modules\Poblacions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPoblacionsRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        parent::__construct();

        
        $this->validationRules['active'] = 'required';
        $this->validationRules['name'] = 'required';
        $this->validationRules['code'] = 'required';
        $this->validationRules['pais_id'] = 'required';
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-poblacions-create') || !auth()->user()->can('admin-poblacions-update')) {
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
            'active.required' => trans('Poblacions::poblacions/admin_lang.fields.active_required'),
        'name.required' => trans('Poblacions::poblacions/admin_lang.fields.name_required'),
        'code.required' => trans('Poblacions::poblacions/admin_lang.fields.code_required'),
        'pais_id.required' => trans('Poblacions::poblacions/admin_lang.fields.pais_required')
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
