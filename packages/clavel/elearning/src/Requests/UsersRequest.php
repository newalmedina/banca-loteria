<?php

namespace Clavel\Elearning\Requests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UsersRequest extends FormRequest
{
    public function authorize()
    {
        if ($this->route()->uri() == "admin" || $this->route()->getPrefix() == "/admin" ||
            substr($this->route()->getPrefix(), 0, 6) === "admin/"
        ) {
            return Auth::user()->can('admin-users-create') || Auth::user()->can('admin-users-update');
        } else {
            return true;
        }
    }

    public function rules()
    {
        $iduser = (!isset($this->id)) ? 0 : $this->id;
        $iduser = ($iduser == 0 && isset($this->iduser) && $this->iduser != '') ? $this->iduser : $iduser;

        if ($iduser == '0' || is_null($iduser)) {
            return array(
                'user_profile.first_name' => 'required',
                'user_profile.last_name' => 'required',
                // 'user_profile.gender' => 'required',
                // 'username' => 'required|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'nif' => 'required',
                'provincia' => 'required',
                'municipio' => 'required',
                'centro' => 'required',
                // 'especialidad' => 'required',
                'password' => 'required|confirmed',
                'user_profile.confirmed' => 'required',
                // 'birthdate' => 'required|date_format:"d/m/Y"'
            );
        }
        return array(
            'user_profile.first_name' => 'required',
            'user_profile.last_name' => 'required',
            //'user_profile.gender' => 'required',
            // 'username' => 'required|unique:users,username,' . $iduser,
            'email' => 'required|email|unique:users,email,' . $iduser,
            'nif' => 'required',
            'provincia' => 'required',
            'municipio' => 'required',
            'centro' => 'required',
            // 'especialidad' => 'required',
            'password' => 'confirmed',
            // 'birthdate' => 'required|date_format:"d/m/Y"',
        );
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_profile.first_name.required' => trans('profile/front_lang.nombre_obligatorio'),
            'user_profile.last_name.required' => trans('profile/front_lang.apellidos_obligatorio'),
            'nif.required' => trans('profile/front_lang.nif_obligatorio'),
            'provincia.required' => trans('profile/front_lang.provincia_obligatorio'),
            'municipio.required' => trans('profile/front_lang.municipio_obligatorio'),
            'centro.required' => trans('profile/front_lang.centro_obligatorio'),
            //  'especialidad.required' => trans('profile/front_lang.especialidad_obligatorio'),
            'user_profile.confirmed.required' => trans('profile/front_lang.user_profile_confirmed'),
            //'user_profile.gender.required' => trans('profile/front_lang.user_profile_gender'),
            'email.required' => trans('profile/front_lang.email_obligatorio'),
            'email.email' => trans('profile/front_lang.email_formato_incorrecto'),
            'email.unique' => trans('profile/front_lang.email_ya_existe'),
            //'username.required' => trans('profile/front_lang.required_username'),
            //'username.unique' => trans('profile/front_lang.usuarios_ya_existe'),
            'password.required' => trans('profile/front_lang.required_password'),
            'password.confirmed' => trans('profile/front_lang.password_no_coincide'),
            //'birthdate.required' => trans('profile/front_lang.birthdate_required'),
            //'birthdate.date_format' => trans('profile/front_lang.birthdate_date_format'),
        ];
    }
}
