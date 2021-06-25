<?php

namespace Clavel\Posts\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPostTagRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function __construct()
    {
        parent::__construct();

        $this->locale = app()->getLocale();
        $this->validationRules['userlang.'.$this->locale.'.tag'] = 'required';
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-posts-tags-create') || !auth()->user()->can('admin-posts-tags-update')) {
            // app()->abort(403);
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
            'userlang.'.$this->locale.'.tag' => trans('posts::admin_lang.titulo'),
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
