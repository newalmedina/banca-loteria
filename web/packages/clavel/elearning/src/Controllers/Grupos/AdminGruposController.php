<?php

namespace Clavel\Elearning\Controllers\Grupos;

use Clavel\Elearning\Requests\GruposRequest;
use Clavel\Elearning\Models\Grupo;
use App\Models\User;

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;

class AdminGruposController extends AdminController
{
    protected $page_title_icon = '<i class="fa  fa-file-image-o"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-grupos';
    }

    public function index()
    {
        if (!Auth::user()->can('admin-grupos-list')) {
            abort(404);
        }

        $page_title = trans("elearning::grupos/admin_lang.grupos");

        return view('elearning::grupos.admin_index', compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getData()
    {
        $grupo = Grupo::select(
            array(
                'id',
                'activo',
                'nombre',
                'codigo'
            )
        );

        return Datatables::of($grupo)
            ->editColumn(
                'activo',
                '@if(Auth::user()->can("admin-grupos-update"))
                        @if($activo)
                            <button class="btn btn-success btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/grupos/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.descativa') . '"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/grupos/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.activa') . '"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </button>
                        @endif
                    @else
                        @if($activo)
                            <button class="btn btn-success btn-sm disabled"
                            data-placement="right">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm disabled"
                            data-placement="right">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @endif
                    @endif'
            )
            ->addColumn('actions', '
                @if(Auth::user()->can("admin-grupos-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/grupos/\'.$id.\'/edit\') }}\';"
                    data-content="' . trans('general/admin_lang.modificar') . '" data-placement="right"
                    data-toggle="popover"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-grupos-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement
                    (\'{{ url(\'admin/grupos/\'.$id.\'/destroy\') }}\');" data-content="' .
                trans('general/admin_lang.borrar') . '" data-placement="left"
                    data-toggle="popover"><i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->rawColumns(['activo', 'actions'])
            ->make();
    }

    public function create()
    {
        if (!Auth::user()->can('admin-grupos-create')) {
            abort(404);
        }
        $grupo = new Grupo();
        $permission_name = "usuario-front";
        $users = User::whereHas('roles', function ($q) use ($permission_name) {
            //$q->whereHas('permissions', function ($q) use ($permission_name) {
            $q->where('name', $permission_name);
            //});
        })->get();

        $permission_name = "admin";
        $profesores = User::whereHas('roles', function ($q) use ($permission_name) {
            $q->whereHas('permissions', function ($q) use ($permission_name) {
                $q->where('name', $permission_name);
            });
        })->get();

        $form_data = array(
            'route' => array(
                'admin.grupos.store'
            ),
            'method' => 'POST',
            'id' => 'formData', 'class' => 'form-horizontal'
        );
        $page_title = trans("elearning::grupos/admin_lang.nuevo_pages");
        return view('elearning::grupos.admin_edit', compact(
            'page_title',
            'grupo',
            'form_data',
            'users',
            'profesores'
        ));
    }

    public function store(GruposRequest $request)
    {
        if (!Auth::user()->can('admin-grupos-create')) {
            abort(404);
        }
        $grupo = Grupo::create($request->except("_token", "sel_users", 'sel_profesores'));
        $sel_users = $request->input('sel_users');
        $grupo->userPivot()->detach();
        if (!is_null($request->input('sel_users'))) {
            $grupo->userPivot()->sync($sel_users);
        }

        $sel_profesores = $request->input('sel_profesores');
        $grupo->profesorPivot()->detach();
        if (!is_null($request->input('sel_profesores'))) {
            $grupo->profesorPivot()->sync($sel_profesores);
        }

        return redirect('admin/grupos/' . $grupo->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function edit($id)
    {
        if (!Auth::user()->can('admin-grupos-update')) {
            abort(404);
        }

        $grupo = Grupo::findOrFail($id);
        $permission_name = "usuario-front";
        $users = User::whereHas('roles', function ($q) use ($permission_name) {
            //$q->whereHas('permissions', function ($q) use ($permission_name) {
            $q->where('name', $permission_name);
            //});
        })->get();


        $permission_name = "admin";
        $profesores = User::whereHas('roles', function ($q) use ($permission_name) {
            $q->whereHas('permissions', function ($q) use ($permission_name) {
                $q->where('name', $permission_name);
            });
        })->get();

        $form_data = array(
            'route' => array(
                'admin.grupos.update', $grupo->id
            ),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("elearning::grupos/admin_lang.modify_page");
        return view('elearning::grupos.admin_edit', compact(
            'page_title',
            'grupo',
            'form_data',
            'users',
            'profesores'
        ));
    }

    public function update(GruposRequest $request, $id)
    {
        if (!Auth::user()->can('admin-grupos-update')) {
            abort(404);
        }

        $grupo = Grupo::findOrFail($id);
        if (empty($grupo)) {
            abort(404);
        }
        $grupo->update($request->except("_token", "sel_users", "sel_profesores"));

        $sel_users = $request->input('sel_users');
        $grupo->userPivot()->detach();
        if (!is_null($request->input('sel_users'))) {
            $grupo->userPivot()->sync($sel_users);
        }

        $sel_profesores = $request->input('sel_profesores');
        $grupo->profesorPivot()->detach();
        if (!is_null($request->input('sel_profesores'))) {
            $grupo->profesorPivot()->sync($sel_profesores);
        }


        return redirect('admin/grupos/' . $grupo->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('admin-grupos-delete')) {
            abort(404);
        }

        $grupo = Grupo::findOrFail($id);
        if (is_null($grupo)) {
            abort(404);
        }
        $grupo->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Grupo eliminada',
            'id' => $grupo->id
        ));
    }

    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-grupos-update')) {
            abort(404);
        }

        $grupo = Grupo::findOrFail($id);

        if (!is_null($grupo)) {
            $grupo->activo = !$grupo->activo;
            return $grupo->save() ? 1 : 0;
        }

        return 0;
    }


    public function usuarios($id)
    {
        $grupo = Grupo::findOrFail($id);

        return view('elearning::grupos.admin_listado_usuarios', compact('grupo'));
    }
}
