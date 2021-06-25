<?php

namespace Clavel\NotificationBroker\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminBouncedEmailsRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        parent::__construct();


        $this->validationRules['active'] = 'required';
        $this->validationRules['email'] = 'required';
        $this->validationRules['description'] = 'required';
        $this->validationRules['bounce_code'] = 'required';
        $this->validationRules['bounce_type_id'] = 'required';
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-bouncedemails-create') || !auth()->user()->can('admin-bouncedemails-update')) {
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
            'active.required' => trans(
                'notificationbroker::bouncedemails/admin_lang.fields.active_required'
            ),
            'email.required' => trans(
                'notificationbroker::bouncedemails/admin_lang.fields.email_required'
            ),
            'description.required' => trans(
                'notificationbroker::bouncedemails/admin_lang.fields.description_required'
            ),
            'bounce_code.required' => trans(
                'notificationbroker::bouncedemails/admin_lang.fields.bounce_code_required'
            ),
            'bounce_type_id.required' => trans(
                'notificationbroker::bouncedemails/admin_lang.fields.bounce_type_required'
            )
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->validationRules;
    }
}
