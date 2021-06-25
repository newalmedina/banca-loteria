<?php
namespace Clavel\TimeTracker\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    protected $validationRules = array();


    public function __construct()
    {
        parent::__construct();

        $this->locale = app()->getLocale();
        $this->validationRules['name'] = 'required';
        $this->validationRules['customer_id'] = 'required';
        $this->validationRules['budget'] = 'numeric';
        $this->validationRules['fixed_rate'] = 'numeric';
        $this->validationRules['hourly_rate'] = 'numeric';
        $this->validationRules['project_type_id'] = 'required';
    }

    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-projects-create') || !auth()->user()->can('admin-projects-update')) {
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
            'name.required' => trans('timetracker::projects/admin_lang.name_required'),
            'customer_id.required' => trans('timetracker::projects/admin_lang.customer_id_required'),
            'budget.numeric' => trans('timetracker::projects/admin_lang.budget_incorrect'),
            'fixed_rate.numeric' => trans('timetracker::projects/admin_lang.fixed_rate_incorrect'),
            'hourly_rate.numeric' => trans('timetracker::projects/admin_lang.hourly_rate_incorrect'),
            'project_type_id.required' => trans('timetracker::projects/admin_lang.project_type_id_required'),

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
