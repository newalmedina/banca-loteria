<?php
namespace Clavel\Elearning\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CodigosRequest extends FormRequest
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
                'codigo' => 'required_without:multi.prefix|required_without:multi.n_inicio|'.
                            'required_without:multi.n_final|unique:codigos,codigo',
                'sel_roles' => 'required',
            );
        } else {
            return array(
                'sel_roles' => 'required',
                'codigo' => 'required|unique:codigos,codigo,'.$idcodigo
            );
        }
    }

    public function attributes()
    {
        return array(
            'codigo' => trans('elearning::codigos/admin_lang.codigo'),
            'sel_roles' => trans('elearning::codigos/admin_lang.roles'),
        );
    }
}
