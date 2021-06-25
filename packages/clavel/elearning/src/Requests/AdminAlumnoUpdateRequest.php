<?php
namespace Clavel\Elearning\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAlumnoUpdateRequest extends FormRequest
{
    public function authorize()
    {
        if (!auth()->user()->can('admin-alumnos-update')) {
            return false;
        }

        return true;
    }

    public function rules()
    {
        $iduser = (!empty($this->iduser)) ? $this->iduser : 0;

        // En la actualización validamos teniendo en cuanta que ya existe el usurio
        $rules = array(
            'userProfile.first_name' => 'required',
            'userProfile.last_name' => 'required',
            'userProfile.birthdate' => 'required',
            'email' => 'required|email|unique:users,email,'.$iduser,
            'password' => 'confirmed',
            'username' => 'unique:users,username,'.$iduser.'|required'
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
