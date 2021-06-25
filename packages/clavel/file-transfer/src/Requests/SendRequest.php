<?php
namespace Clavel\FileTransfer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendRequest extends FormRequest
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
            'email_destino.required' => trans('file-transfer::front_lang.email_destino_required'),
            'email.required' => trans('file-transfer::front_lang.email_required'),
            'message.required' => trans('file-transfer::front_lang.message_required'),
            'select-link.required' => trans('file-transfer::front_lang.select-link_required'),
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
            'email_destino' => 'required',
            'email' => 'required',
            'message' => 'required',
            'select-link' => 'required'
        );
    }
}
