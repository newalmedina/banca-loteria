<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrontPassLostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'email.required' => trans('users/lang.email_obligatorio'),
            'email.email' => trans('users/lang.email_formato_incorrecto'),
        ];
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = array(
            'email' => 'required|email',
        );

        return $rules;
    }
}
