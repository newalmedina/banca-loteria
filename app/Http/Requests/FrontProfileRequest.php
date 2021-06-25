<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrontProfileRequest extends FormRequest
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
            'userProfile.first_name.required' => trans('profile/front_lang.nombre_obligatorio'),
            'userProfile.last_name.required' => trans('profile/front_lang.apellidos_obligatorio'),
            'email.required' => trans('profile/front_lang.email_obligatorio'),
            'email.email' => trans('profile/front_lang.email_formato_incorrecto'),
            'email.unique' => trans('profile/front_lang.email_ya_existe'),
            'username.required' => trans('profile/front_lang.required_username'),
            'username.unique' => trans('profile/front_lang.usuarios_ya_existe'),
            'password.required' => trans('profile/front_lang.required_password'),
            'password.confirmed' => trans('profile/front_lang.password_no_coincide'),
            'password.min' => trans('users/lang.password_min'),
            'terms.required' => trans('profile/front_lang.terms_obligatorio'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $idprofile = auth()->user()->getAuthIdentifier();

        return [
            'userProfile.first_name' => 'required',
            'userProfile.last_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$idprofile,
            'username' => 'unique:users,username,'.$idprofile.'|required',
            'password' => 'nullable|confirmed',
            'terms' => 'required',
        ];
    }
}
