<?php
namespace App\Modules\Contacto\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'fullname.required' => trans('Contacto::front_lang.fullname_required'),
            'email.required' => trans('Contacto::front_lang.email_required'),
            'email.email' => trans('Contacto::front_lang.email_formato_incorrecto'),
            'message.required' => trans('Contacto::front_lang.message_required'),
            'message.min' => trans('Contacto::front_lang.message_min_required'),
            'message.max' => trans('Contacto::front_lang.message_max_required'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array(
            'fullname' => 'required',
            'email' => 'required|email',
            'message' => 'required|min:20|max:255'
        );
    }
}
