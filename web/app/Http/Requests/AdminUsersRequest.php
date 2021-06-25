<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-users-create') || !auth()->user()->can('admin-users-update')) {
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
            'userProfile.first_name.required' => trans('users/lang.nombre_obligatorio'),
            'userProfile.last_name.required' => trans('users/lang.apellidos_obligatorio'),
            'email.required' => trans('users/lang.email_obligatorio'),
            'email.email' => trans('users/lang.email_formato_incorrecto'),
            'email.unique' => trans('users/lang.email_ya_existe'),
            'username.required' => trans('users/lang.required_username'),
            'username.unique' => trans('users/lang.usuarios_ya_existe'),
            'password.required' => trans('users/lang.required_password'),
            'password.confirmed' => trans('users/lang.password_no_coincide')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $iduser = (empty($this->id)) ? 0 : $this->id;
        $iduser = (!empty($this->iduser)) ? $this->iduser : $iduser;


        if ($iduser==0) {
            // La primera vez realizamos todas las validaciones
            $rules = array(
                'userProfile.first_name' => 'required',
                'userProfile.last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed',
                'username' => 'unique:users,username|required'
            );
        } else {
            // En la actualización validamos teniendo en cuanta que ya existe el usurio
            $rules = array(
                'userProfile.first_name' => 'required',
                'userProfile.last_name' => 'required',
                'email' => 'required|email|unique:users,email,'.$iduser,
                'password' => 'confirmed',
                'username' => 'unique:users,username,'.$iduser.'|required'
            );
        }

        return $rules;
    }
}
