<?php
namespace Clavel\TimeTracker\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    protected $validationRules = array();


    public function __construct()
    {
        parent::__construct();

        $this->locale = app()->getLocale();
        $this->validationRules['name'] = 'required';
        $this->validationRules['timezone'] = 'required';
        $this->validationRules['country'] = 'required';
        $this->validationRules['currency'] = 'required';
        $this->validationRules['email'] = 'email';
        $this->validationRules['fixed_rate'] = 'numeric';
        $this->validationRules['hourly_rate'] = 'numeric';
    }

    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-customers-create') || !auth()->user()->can('admin-customers-update')) {
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
        return [
            'name.required' => trans('timetracker::customers/admin_lang.name_required'),
            'timezone.required' => trans('timetracker::customers/admin_lang.timezone_required'),
            'currency.required' => trans('timetracker::customers/admin_lang.currency_required'),
            'country.required' => trans('timetracker::customers/admin_lang.country_required'),
            'email.email' => trans('timetracker::customers/admin_lang.email_incorrect'),
            'fixed_rate.numeric' => trans('timetracker::customers/admin_lang.fixed_rate_incorrect'),
            'hourly_rate.numeric' => trans('timetracker::customers/admin_lang.hourly_rate_incorrect'),

        ];
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
