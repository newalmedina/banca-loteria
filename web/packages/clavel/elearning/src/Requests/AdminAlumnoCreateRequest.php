<?php
namespace Clavel\Elearning\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAlumnoCreateRequest extends FormRequest
{
    public function authorize()
    {
        if (!auth()->user()->can('admin-alumnos-create')) {
            return false;
        }

        return true;
    }

    public function rules()
    {
        // La primera vez realizamos todas las validaciones
        $rules = array(
            'userProfile.first_name' => 'required',
            'userProfile.last_name' => 'required',
            'userProfile.birthdate' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'username' => 'unique:users,username|required'
        );

        return $rules;
    }

    public function messages()
    {
        return [
            'userProfile.first_name.required' => trans('users/lang.nombre_obligatorio'),
            'userProfile.last_name.required' => trans('users/lang.apellidos_obligatorio'),
            'userProfile.birthdate.required' => trans('users/lang.birthdate_obligatorio'),
            'email.required' => trans('users/lang.email_obligatorio'),
            'email.email' => trans('users/lang.email_formato_incorrecto'),
            'email.unique' => trans('users/lang.email_ya_existe'),
            'username.required' => trans('users/lang.required_username'),
            'username.unique' => trans('users/lang.usuarios_ya_existe'),
            'password.required' => trans('users/lang.required_password'),
            'password.confirmed' => trans('users/lang.password_no_coincide')
        ];
    }
}
