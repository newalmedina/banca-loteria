<?php

namespace Clavel\Posts\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrontPostCommentRequest extends FormRequest
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
            //'fullname' => trans('posts/front_lang.nombre'),
            //'email' => trans('posts/front_lang.email'),
            'message.required' => trans('posts::front_lang.message_required'),
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
            //'fullname' => 'required',
            //'email' => 'required|email',
            'message' => 'required'
        ];
    }
}
