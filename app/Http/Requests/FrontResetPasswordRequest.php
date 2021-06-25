<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrontResetPasswordRequest extends FormRequest
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
            'password.required' => trans('users/lang.required_password'),
            'password.confirmed' => trans('users/lang.password_no_coincide'),
            'password.min' => trans('users/lang.password_min')
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
            'password' => 'required|confirmed'
        );

        return $rules;
    }
}
