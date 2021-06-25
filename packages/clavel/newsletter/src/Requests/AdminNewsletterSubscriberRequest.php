<?php
/**
 * Created by PhpStorm.
 * User: Jose Juan
 * Date: 05/10/2017
 * Time: 8:58
 */

namespace App\Modules\Newsletter\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminNewsletterSubscriberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si no tiene permisos para lo echa.
        if (!auth()->check()) {
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
        return array(
            'subscriptor_name' => trans('Newsletter::admin_lang.subscriptor_name'),
            'subscriptor_surname' => trans('Newsletter::admin_lang.subscriptor_surname'),
            'email.required' => trans('Newsletter::admin_lang.email_obligatorio'),
            'email.email' => trans('Newsletter::admin_lang.email_formato_incorrecto'),
            'email.unique' => trans('Newsletter::admin_lang.email_ya_existe'),
            'authorized' => trans('Newsletter::admin_lang.authorized'),
        );
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

        return [
            'subscriptor_name' => 'required',
            'subscriptor_surname' => 'required',
            'email' => 'required|email|unique:users,email,'.$iduser,
            'authorized' => 'required'
        ];
    }
}
