<?php

namespace App\Modules\Newsletter\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminNewsletterCampaignsRequest extends FormRequest
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
            'name' => trans('Newsletter::admin_lang_campaigns.name'),
            'list_id.required' => trans('Newsletter::admin_lang_campaigns.list_required'),
            'newsletter_id.required' => trans('Newsletter::admin_lang_campaigns.newsletter_required')
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
            'name' => 'required|max:255',
            'list_id' => 'required',
            'newsletter_id' => 'required'
        ];
    }
}
