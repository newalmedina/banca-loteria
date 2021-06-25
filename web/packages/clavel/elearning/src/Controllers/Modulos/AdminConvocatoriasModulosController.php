<?php

namespace Clavel\Elearning\Controllers\Modulos;

use App\Http\Controllers\AdminController;
use Clavel\Elearning\Requests\ConvocatoriaModuloRequest;
use Clavel\Elearning\Models\Convocatoria;
use Clavel\Elearning\Models\Modulo;
use Clavel\Elearning\Models\ModuloConvocatoria;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Response;

class AdminConvocatoriasModulosController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-modulos-convocatorias-update';
    }

    public function index($modulo_id)
    {
        $modulo = Modulo::find($modulo_id);
        return view('elearning::modulos.admin_convocatorias_list', compact('modulo'));
    }

    public function getFormulario($modulo_id, $id = '')
    {
        if (!Auth::user()->can('admin-asignaturas-convocatorias-update') && $id != '') {
            abort(404);
        }

        $convocatoriaAsignatura = Convocatoria::findOrFail($id);
        $convocatoria = ModuloConvocatoria::where('convocatoria_id', '=', $id)
            ->where("modulo_id", "=", $modulo_id)
            ->first();
        if (is_null($convocatoria)) {
            $convocatoria = new ModuloConvocatoria();
        }


        $convocatoria->modulo_id = $modulo_id;
        $convocatoria->convocatoria_id = $id;
        if ($convocatoria->fecha_inicio == "") {
            $convocatoria->fecha_inicio = $convocatoriaAsignatura->fecha_inicio;
        }
        if ($convocatoria->fecha_fin == "") {
            $convocatoria->fecha_fin = $convocatoriaAsignatura->fecha_fin;
        }
        if ($convocatoria->consultar == "") {
            $convocatoria->consultar = $convocatoriaAsignatura->consultar;
        }
        if ($convocatoria->porcentaje == "") {
            $convocatoria->porcentaje = $convocatoriaAsignatura->porcentaje;
        }


        $form_data = array(
            'route' => array(
                'admin.modulos.convocatorias.formulario'
            ),
            'method' => 'POST', 'id' => 'formData'
        );
        return view(
            'elearning::modulos.admin_convocatorias_edit',
            compact(
                'convocatoria',
                'form_data',
                'convocatoriaAsignatura'
            )
        );
    }

    public function postFormulario(ConvocatoriaModuloRequest $request)
    {
        $request->merge(array(
            'fecha_inicio' => ($request->input("fecha_inicio") != '') ?
                Carbon::createFromFormat(
                    'd/m/Y',
                    $request->input("fecha_inicio")
                ) : null,
            'fecha_fin' => ($request->input("fecha_fin") != '') ?
                Carbon::createFromFormat(
                    'd/m/Y',
                    $request->input("fecha_fin")
                ) : null,
        ));

        if ($request->input("id") != '') {
            $convocatoria = ModuloConvocatoria::findOrFail($request->input("id"));
            $convocatoria->update($request->except("_token", "id"));
        } else {
            ModuloConvocatoria::create($request->except("_token", "id"));
        }

        return Response::json(array(
            'success' => true
        ));
    }
}
