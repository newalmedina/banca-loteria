<?php

namespace Clavel\Elearning\Controllers\Asignaturas;

use Clavel\Elearning\Requests\ConvocatoriaRequest;
use App\Http\Controllers\AdminController;
use Clavel\Elearning\Models\Convocatoria;
use Clavel\Elearning\Models\Certificado;
use Clavel\Elearning\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class AdminConvocatoriasController extends AdminController
{
    protected $page_title_icon = '<i class="fa  fa-file-image-o" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-asignaturas-convocatorias';
    }

    public function index($asignatura_id)
    {
        if (!Auth::user()->can('admin-asignaturas-convocatorias-list')) {
            abort(404);
        }
        $convocatorias = Convocatoria::where("asignatura_id", $asignatura_id)
            ->orderBy("fecha_inicio", "DESC")
            ->orderBy("fecha_fin", "DESC")
            ->get();
        return view(
            'elearning::asignaturas.admin_convocatorias_index',
            compact(
                'convocatorias',
                'asignatura_id'
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getFormulario($asignatura_id, $id = '')
    {
        if ((!Auth::user()->can('admin-asignaturas-convocatorias-create') && $id == '')
            || (!Auth::user()->can('admin-asignaturas-convocatorias-update') && $id != '')
        ) {
            abort(404);
        }
        $convocatoria = ($id != '') ? Convocatoria::findOrFail($id) : new Convocatoria();
        $convocatoria->asignatura_id = $asignatura_id;
        $certificados = Certificado::all();
        $grupos = Grupo::all();
        $form_data = array(
            'route' => array(
                'asignaturas.convocatorias.formulario'
            ),
            'method' => 'POST', 'id' => 'formData'
        );
        return view(
            'elearning::asignaturas.admin_convocatorias_edit',
            compact('convocatoria', 'certificados', 'grupos', 'form_data')
        );
    }

    public function postFormulario(Request $request, $asignatura_id, $id = "")
    {
        $request->merge(array(
            'fecha_inicio' => ($request->input("fecha_inicio") != '') ?
                Carbon::createFromFormat('d/m/Y', $request->input("fecha_inicio")) : null,
            'fecha_fin' => ($request->input("fecha_fin") != '') ?
                Carbon::createFromFormat('d/m/Y', $request->input("fecha_fin")) : null,
            'certificado_id' => ($request->input("certificado_id") != '') ?
                $request->input("certificado_id") : null
        ));

        if ($id) {
            $convocatoria = Convocatoria::findOrFail($request->input("id"));
            $convocatoria->update($request->except("_token", "id", "sel_grupos"));
        } else {
            $convocatoria = Convocatoria::create($request->except("_token", "id", "sel_grupos"));
        }

        $sel_grupos = $request->input('sel_grupos');
        $convocatoria->gruposPivot()->detach();
        if (!is_null($request->input('sel_grupos'))) {
            $convocatoria->gruposPivot()->sync($sel_grupos);
        }

        return Response::json(array(
            'success' => true
        ));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('admin-asignaturas-convocatorias-delete')) {
            abort(404);
        }

        $convocatoria = Convocatoria::findOrFail($id);
        $convocatoria->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Asignatura eliminada',
            'id' => $convocatoria->id
        ));
    }
}
