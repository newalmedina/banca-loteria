<?php
namespace Clavel\TimeTracker\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class MyTimesRequest extends FormRequest
{
    protected $validationRules = array();


    public function __construct()
    {
        parent::__construct();

        $this->locale = app()->getLocale();
        $this->validationRules['customer_id'] = 'required';
        $this->validationRules['project_id'] = 'required';
        $this->validationRules['activity_id'] = 'required';
        $this->validationRules['start_time'] = 'required|date_format:d/m/Y H:i';
        $this->validationRules['end_time'] = 'nullable|date_format:d/m/Y H:i';
        $this->validationRules['fixed_rate'] = 'nullable|numeric';
        $this->validationRules['hourly_rate'] = 'nullable|numeric';
    }

    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-mytimes-create') || !auth()->user()->can('admin-mytimes-update')) {
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
            'project_id.required' => trans('timetracker::mytimes/admin_lang.project_id_required'),
            'customer_id.required' => trans('timetracker::mytimes/admin_lang.customer_id_required'),
            'activity_id.required' => trans('timetracker::mytimes/admin_lang.activity_id_required'),
            'start_time.required' => trans('timetracker::mytimes/admin_lang.start_time_required'),
            'fixed_rate.numeric' => trans('timetracker::mytimes/admin_lang.fixed_rate_incorrect'),
            'hourly_rate.numeric' => trans('timetracker::mytimes/admin_lang.hourly_rate_incorrect'),
            'start_time.date_format' => trans('timetracker::mytimes/admin_lang.start_time_incorrect'),
            'end_time.date_format' => trans('timetracker::mytimes/admin_lang.end_time_incorrect'),


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
