<?php

namespace Clavel\Basic\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminMenuStructureRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        $this->locale = app()->getLocale();
        $this->validationRules['userlang.'.$this->locale.'.title'] = 'required';
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->check()) {
            // app()->abort(403);
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
            'userlang.'.$this->locale.'.title' => trans('basic::menu/admin_lang.title')
        ];
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
