<?php

namespace Clavel\Elearning\Controllers\Cursos;

use Clavel\Elearning\Models\Certificado;
use Clavel\Elearning\Requests\CursosRequest;
use Clavel\Elearning\Models\Asignatura;
use Clavel\Elearning\Models\Curso;
use Clavel\Elearning\Models\CursoTranslation;

use App\Http\Controllers\AdminController;
use App\Services\GetLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;

class AdminCursosController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa fa-book"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-cursos';
    }

    public function index()
    {
        if (!Auth::user()->can('admin-cursos-list')) {
            app()->abort(403);
        }

        $page_title = trans("elearning::cursos/admin_lang.cursos");

        return view('elearning::cursos.admin_index', compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getData()
    {
        $locale = config('app.default_locale');

        $cursos = Curso::select(
            array(
                'cursos.id',
                'cursos.activo',
                'curso_translations.nombre',
                'curso_translations.url_amigable'
            )
        )->join('curso_translations', function ($join) use ($locale) {
            $join->on('curso_translations.curso_id', '=', 'cursos.id');
            $join->on('curso_translations.locale', '=', DB::raw("'" . $locale . "'"));
        });

        return Datatables::of($cursos)
            ->editColumn(
                'activo',
                '@if(Auth::user()->can("admin-cursos-update"))
                        @if($activo)
                            <button class="btn btn-success btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/cursos/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.descativa') . '" data-placement="right"
                            data-toggle="popover">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/cursos/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.activa') . '" data-placement="right"
                            data-toggle="popover">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </button>
                        @endif
                    @else
                        @if($activo)
                            <button class="btn btn-success btn-sm disabled" data-placement="right">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm disabled" data-placement="right">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @endif
                    @endif'
            )
            ->addColumn('actions', '
                @if(Auth::user()->can("admin-cursos-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/cursos/\'.$id.\'/edit\') }}\';"
                    data-content="' . trans('general/admin_lang.modificar') . '"
                    data-placement="right"
                    data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-cursos-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/cursos/\'.$id.\'/destroy\') }}\');"
                    data-content="' . trans('general/admin_lang.borrar') . '"
                    data-placement="left"
                    data-toggle="popover">
                    <i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->rawColumns(['activo', 'actions'])
            ->make();
    }

    public function create()
    {
        if (!Auth::user()->can('admin-cursos-create')) {
            app()->abort(403);
        }

        $curso = new Curso();
        $form_data = array(
            'route' => array('admin.cursos.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("elearning::cursos/admin_lang.nuevo_pages");

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($curso);

        // Asignaturas
        $asignaturas = Asignatura::active()->get();
        $certificados = Certificado::all();

        return view('elearning::cursos.admin_edit', compact(
            'page_title',
            'curso',
            'form_data',
            'a_trans',
            'asignaturas',
            'certificados'
        ));
    }

    public function store(CursosRequest $request)
    {
        if (!Auth::user()->can('admin-cursos-create')) {
            app()->abort(403);
        }

        $curso = new Curso();
        $this->saveCurso($request, $curso);
        $sel_asignaturas = $request->input('sel_asignaturas');
        $curso->asignaturaPivot()->detach();
        if (!is_null($request->input('sel_asignaturas'))) {
            $curso->asignaturaPivot()->sync($sel_asignaturas);
        }
        return redirect('admin/cursos/' . $curso->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function edit($id)
    {
        if (!Auth::user()->can('admin-cursos-update')) {
            app()->abort(403);
        }

        $curso = Curso::find($id);
        $form_data = array(
            'route' => array(
                'admin.cursos.update', $curso->id
            ),
            'method' => 'PATCH',
            'id' => 'formData', 'class' => 'form-horizontal'
        );
        $page_title = trans("elearning::cursos/admin_lang.modify_page");

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($curso);

        // Asignaturas
        $asignaturas = Asignatura::active()->get();
        $certificados = Certificado::all();

        return view('elearning::cursos.admin_edit', compact(
            'page_title',
            'curso',
            'form_data',
            'a_trans',
            'asignaturas',
            'certificados'
        ));
    }

    public function update(CursosRequest $request, $id)
    {
        if (!Auth::user()->can('admin-cursos-update')) {
            app()->abort(403);
        }

        $curso = Curso::find($id);
        if (empty($curso)) {
            abort(404);
        }
        $this->saveCurso($request, $curso);
        $sel_asignaturas = $request->input('sel_asignaturas');
        $curso->asignaturaPivot()->detach();
        if (!is_null($request->input('sel_asignaturas'))) {
            $curso->asignaturaPivot()->sync($sel_asignaturas);
        }
        return redirect('admin/cursos/' . $curso->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('admin-cursos-delete')) {
            app()->abort(403);
        }

        $curso = Curso::find($id);
        if (is_null($curso)) {
            abort(404);
        }
        $curso->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Grupo eliminada',
            'id' => $curso->id
        ));
    }

    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-cursos-update')) {
            app()->abort(403);
        }

        $curso = Curso::find($id);

        if (!is_null($curso)) {
            $curso->activo = !$curso->activo;
            return $curso->save() ? 1 : 0;
        }

        return 0;
    }

    private function saveCurso(Request $request, Curso $cursos)
    {
        $cursos->activo = $request->input("activo");
        if (!empty($request->input("certificado_id"))) {
            $cursos->certificado_id = $request->input("certificado_id");
        }
        $cursos->save();

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = CursoTranslation::findOrNew($value["id"]);

            $itemTrans->curso_id = $cursos->id;
            $itemTrans->locale = $key;
            $itemTrans->nombre = $value["nombre"];
            $itemTrans->url_amigable = str_slug($itemTrans->nombre);
            $itemTrans->save();
        }
    }
}
