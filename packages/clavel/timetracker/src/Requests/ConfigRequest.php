<?php
namespace Clavel\TimeTracker\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ConfigRequest extends FormRequest
{
    protected $validationRules = array();


    public function __construct()
    {
        parent::__construct();

        $this->locale = app()->getLocale();
        $this->validationRules['budget_prefix'] = 'required';
        $this->validationRules['budget_counter'] = 'required|integer|min:0';
        $this->validationRules['budget_digits'] = 'required|integer|min:2';
        $this->validationRules['order_prefix'] = 'required';
        $this->validationRules['order_counter'] = 'required|integer|min:0';
        $this->validationRules['order_digits'] = 'required|integer|min:2';
    }

    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-timetracker-config-create') ||
            !auth()->user()->can('admin-timetracker-config-update')) {
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
            'budget_prefix.required' => trans('timetracker::config/admin_lang.budget_prefix_required'),
            'budget_counter.required' => trans('timetracker::config/admin_lang.budget_counter_required'),
            'order_prefix.required' => trans('timetracker::config/admin_lang.order_prefix_required'),
            'order_counter.required' => trans('timetracker::config/admin_lang.order_counter_required'),

            'budget_counter.integer' => trans('timetracker::config/admin_lang.budget_counter_incorrect'),
            'order_counter.integer' => trans('timetracker::config/admin_lang.order_counter_incorrect'),
            'budget_counter.min' => trans('timetracker::config/admin_lang.budget_counter_min'),
            'order_counter.min' => trans('timetracker::config/admin_lang.order_counter_min'),

            'budget_digits.integer' => trans('timetracker::config/admin_lang.budget_digits_incorrect'),
            'order_digits.integer' => trans('timetracker::config/admin_lang.order_digits_incorrect'),
            'budget_digits.min' => trans('timetracker::config/admin_lang.budget_digits_min'),
            'order_digits.min' => trans('timetracker::config/admin_lang.order_digits_min'),
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
