<?php

namespace Clavel\Basic\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminMediaDirRequest extends FormRequest
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
            'foldername' => trans('basic::media/admin_lang.nameFolder')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'foldername' => 'required'
        ];
    }
}
