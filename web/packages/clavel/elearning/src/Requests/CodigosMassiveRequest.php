<?php
namespace Clavel\Elearning\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CodigosMassiveRequest extends FormRequest
{
    protected $validationRules = array();
    protected $locale;

    public function authorize()
    {
        if (!empty(Auth::user())) {
            return true;
        }
        return false;
    }

    public function rules()
    {
        $idcodigo = (!isset($this->id)) ? 0 : $this->id;

        if (empty($idcodigo)) {
            return array(
                'multi.prefix' => 'required',
                'multi.n_inicio' => 'required|integer',
                'multi.n_final' => 'required|integer',
                'sel_roles' => 'required',
            );
        } else {
            return array(
                'sel_roles' => 'required',
                'codigo' => 'required|unique:codigos,codigo,'.$idcodigo
            );
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return array(
            'multi.prefix.required' => trans('elearning::codigos/admin_lang.prefijo_requerido'),
            'multi.n_inicio.required' => trans('elearning::codigos/admin_lang.n_inicio_requerido'),
            'multi.n_inicio.integer' => trans('elearning::codigos/admin_lang.n_inicio_entero'),
            'multi.n_final.required' => trans('elearning::codigos/admin_lang.n_final_requerido'),
            'multi.n_final.integer' => trans('elearning::codigos/admin_lang.n_final_entero')
        );
    }
}
