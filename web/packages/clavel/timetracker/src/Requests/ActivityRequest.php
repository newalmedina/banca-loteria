<?php
namespace Clavel\TimeTracker\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    protected $validationRules = array();


    public function __construct()
    {
        parent::__construct();

        $this->locale = app()->getLocale();
        $this->validationRules['name'] = 'required';
        $this->validationRules['customer_id'] = 'required';
        $this->validationRules['fixed_rate'] = 'numeric';
        $this->validationRules['hourly_rate'] = 'numeric';
    }

    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-activities-create') || !auth()->user()->can('admin-activities-update')) {
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
            'name.required' => trans('timetracker::activities/admin_lang.name_required'),
            'fixed_rate.numeric' => trans('timetracker::activities/admin_lang.fixed_rate_incorrect'),
            'hourly_rate.numeric' => trans('timetracker::activities/admin_lang.hourly_rate_incorrect'),

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
