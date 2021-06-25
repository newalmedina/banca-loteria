<?php

namespace App\Modules\Newsletter\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminNewsletterListsRequest extends FormRequest
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
            'name' => trans('Newsletter::admin_lang_lists.name')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255'
        ];
    }
}
