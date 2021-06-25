<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminProfileRequest extends FormRequest
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
            'userProfile.first_name' => trans('profile/admin_lang._NOMBRE_USUARIO'),
            'userProfile.last_name' => trans('profile/admin_lang._APELLIDOS_USUARIO'),
            'email' => trans('profile/admin_lang._EMAIL_USUARIO'),
            'username' => trans('profile/admin_lang.usuario'),
            'password' => trans('profile/admin_lang._CONTASENYA_USUARIO')
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
            'username' => 'unique:users,username,'.$idprofile.'|required'
        ];
    }
}
