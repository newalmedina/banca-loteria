<?php

namespace App\Modules\Idiomas\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminIdiomasRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        parent::__construct();

        $this->locale = app()->getLocale();
        $this->validationRules['active'] = 'required';
        $this->validationRules['code'] = 'required';
        $this->validationRules['lang.'.$this->locale.'.name'] = 'required';
        $this->validationRules['default'] = 'required';
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-idiomas-create') || !auth()->user()->can('admin-idiomas-update')) {
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
            'active.required' => trans('Idiomas::idiomas/admin_lang.fields.active_required'),
        'code.required' => trans('Idiomas::idiomas/admin_lang.fields.code_required'),
        'lang.'.$this->locale.'.name.required' => trans('Idiomas::idiomas/admin_lang.fields.name_required'),
        'default.required' => trans('Idiomas::idiomas/admin_lang.fields.default_required')
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
