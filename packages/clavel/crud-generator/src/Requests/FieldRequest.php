<?php
namespace Clavel\CrudGenerator\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class FieldRequest extends FormRequest
{
    protected $validationRules = array();


    public function __construct()
    {
        parent::__construct();

        $this->validationRules['column_title'] = 'required';
        $this->validationRules['column_name'] = 'required';
        $this->validationRules['order_list'] = 'nullable|integer|min:1|max:9000';
        $this->validationRules['order_create'] = 'nullable|integer|min:1|max:9000';
        $this->validationRules['min_length'] = 'nullable|integer|min:1|max:9000';
        $this->validationRules['max_length'] = 'nullable|integer|min:1|max:9000';
    }

    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-modulos-crud-create') ||
            !auth()->user()->can('admin-modulos-crud-update')) {
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
            'column_title.required' => trans('crud-generator::fields/admin_lang.column_title_required'),
            'column_name.required' => trans('crud-generator::fields/admin_lang.column_name_required'),


            'order_list.required' => trans('crud-generator::fields/admin_lang.order_list_required'),
            'order_create.required' => trans('crud-generator::fields/admin_lang.order_create_required'),

            'order_list.integer' => trans('crud-generator::fields/admin_lang.order_list_incorrect'),
            'order_create.integer' => trans('crud-generator::fields/admin_lang.order_create_incorrect'),
            'order_list.min' => trans('crud-generator::fields/admin_lang.order_list_min'),
            'order_create.min' => trans('crud-generator::fields/admin_lang.order_create_min'),
            'order_list.max' => trans('crud-generator::fields/admin_lang.order_list_max'),
            'order_create.max' => trans('crud-generator::fields/admin_lang.order_create_max'),

            'min_length.integer' => trans('crud-generator::fields/admin_lang.min_length_incorrect'),
            'max_length.integer' => trans('crud-generator::fields/admin_lang.max_length_incorrect'),
            'min_length.min' => trans('crud-generator::fields/admin_lang.min_length_min'),
            'max_length.min' => trans('crud-generator::fields/admin_lang.max_length_min'),
            'min_length.max' => trans('crud-generator::fields/admin_lang.min_length_max'),
            'max_length.max' => trans('crud-generator::fields/admin_lang.max_length_max'),

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
