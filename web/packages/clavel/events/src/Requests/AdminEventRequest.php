<?php

namespace App\Modules\Events\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminEventRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        parent::__construct();

        $this->locale = app()->getLocale();
        $this->validationRules['userlang.'.$this->locale.'.title'] = 'required';
        $this->validationRules['date_start'] = 'required';
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth()->check()) {
            return true;
        }
        return false;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return array(
            'userlang.'.$this->locale.'.title' => trans('Events::admin_lang.titulo'),
            'date_start' => trans('Events::admin_lang.date_start')
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
