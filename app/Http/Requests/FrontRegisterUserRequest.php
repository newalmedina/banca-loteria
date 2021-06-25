<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrontRegisterUserRequest extends FormRequest
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
            'first_name.required' => trans('users/lang.nombre_obligatorio'),
            'last_name.required' => trans('users/lang.apellidos_obligatorio'),
            'email.required' => trans('users/lang.email_obligatorio'),
            'email.email' => trans('users/lang.email_formato_incorrecto'),
            'email.unique' => trans('users/lang.email_ya_existe'),
            'username.required' => trans('users/lang.required_username'),
            'username.unique' => trans('users/lang.usuarios_ya_existe'),
            'password.required' => trans('users/lang.required_password'),
            'password.confirmed' => trans('users/lang.password_no_coincide'),
            'password.min' => trans('users/lang.password_min'),
            'terms.required' => trans('users/lang.aceptar_terminos'),
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'username' => 'unique:users,username|required',
            'terms' => 'required',
        );

        return $rules;
    }
}
