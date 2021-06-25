<?php

namespace Clavel\Elearning\Controllers\HomeElearning;

use App\Http\Controllers\Controller;
use Clavel\Elearning\Models\Asignatura;
use Illuminate\Support\Facades\Redirect;

class HomeElearningController extends Controller
{
    public function __construct()
    {
        //  $this->middleware("guest");
    }

    public function index()
    {
        $asignaturas = Asignatura::orderBy("orden")->active();

        if (!config("elearning.cursos.mostrar_asignaturas") && $asignaturas->count() == 1) {
            $asignatura = $asignaturas->first();
            $url = 'asignaturas/detalle/'.$asignatura->url_amigable."/".$asignatura->id;
        } else {
            $url = "asignaturas";
        }

        $form_data = array(
            'route' => array('login'),
            'method' => 'POST',
            'class' => 'login-form form-transparent-grey'
        );

        return view("elearning::home.front_index", compact(
            "form_data",
            "url"
        ));
    }
}
