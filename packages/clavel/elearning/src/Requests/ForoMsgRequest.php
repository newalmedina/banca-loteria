<?php
namespace Clavel\Elearning\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForoMsgRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return array(
            'titulo' => 'required'
        );
    }

    public function attributes()
    {
        return array(
            'titulo' => trans('foro/front_lang.titulo')
        );
    }
}
