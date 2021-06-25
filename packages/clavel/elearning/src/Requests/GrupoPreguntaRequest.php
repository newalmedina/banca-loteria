<?php
namespace Clavel\Elearning\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrupoPreguntaRequest extends FormRequest
{
    protected $validationRules = array();

    public function __construct()
    {
        parent::__construct();

        $this->validationRules['titulo'] = 'required';
        $this->validationRules['contenido_id'] = 'required';
        $this->validationRules['color'] = 'required';
    }

    public function authorize()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-contenidos-create') || !auth()->user()->can('admin-contenidos-update')) {
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
            'titulo.required' => trans('grupos_preguntas/admin_lang.titulo_required'),
            'contenido_id.required' => trans('grupos_preguntas/admin_lang.contenido_id_required'),
            'color.required' => trans('grupos_preguntas/admin_lang.color_required'),
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
