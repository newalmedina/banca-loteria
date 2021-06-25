<?php

namespace Clavel\NotificationBroker\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminBlacklistsRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        parent::__construct();


        $this->validationRules['to'] = 'required';
        $this->validationRules['slug'] = 'required';
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-blacklists-create') || !auth()->user()->can('admin-blacklists-update')) {
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
            'to.required' => trans('notificationbroker::blacklists/admin_lang.fields.to_required'),
        'slug.required' => trans('notificationbroker::blacklists/admin_lang.fields.slug_required')
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
