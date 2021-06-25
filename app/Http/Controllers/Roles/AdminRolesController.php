<?php

namespace App\Http\Controllers\Roles;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AdminRolesRequest;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\AdminController;

class AdminRolesController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa fa-key" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-roles';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-roles-list')) {
            app()->abort(403);
        }

        $page_title = trans("roles/lang.roles");

        return view("modules.roles.admin_index", compact('page_title'))
        ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-roles-create')) {
            app()->abort(403);
        }

        $page_title = trans("roles/lang.crear_roles");

        $id = 0;

        // Mostramos la página
        return view('modules.roles.admin_edit', compact('id', 'page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminRolesRequest $request)
    {
        // Creamos un nuevo objeto para nuestro nuevo usuario y su relación
        $roles = new Role();

        // Obtenemos la data enviada por el usuario
        $roles->name = Str::slug($request->input('display_name'));
        $roles->display_name = $request->input('display_name');
        $roles->description = $request->input('description');
        $roles->active = $request->input('active', '0');
        $roles->fixed = 0;

        try {
            DB::beginTransaction();

            $roles->save();

            DB::commit();

            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect()->route('roles.edit', array($roles->id))
                ->with('success', trans('roles/lang.okGuardado'))
                ->with('tab', "tab_1");
        } catch (\PDOException $e) {
            DB::rollBack();
            return redirect()->route('roles.create')
                ->with('error', trans('roles/lang.errorediciion'))
                ->with('tab', "tab_1");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Si no tiene permisos para modificar o visualizar lo echamos
        if (!auth()->user()->can('admin-roles-update') && !auth()->user()->can('admin-roles-read')) {
            app()->abort(403);
        }

        $page_title = trans("roles/lang.modificar_roles");

        // Mostramos la página
        return view('modules.roles.admin_edit', compact('id', 'page_title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminRolesRequest $request, $id)
    {
        // Creamos un nuevo objeto para nuestro nuevo usuario
        $roles = Role::find($id);

        // Si el rol no existe entonces lanzamos un error 404 :(
        if (is_null($roles)) {
            app()->abort(500);
        }

        // Obtenemos la data enviada por el usuario
        $roles->name = Str::slug($request->input('display_name'));
        $roles->display_name = $request->input('display_name');
        $roles->description = $request->input('description');
        $roles->active = $request->input('active', '0');

        try {
            DB::beginTransaction();

            // Guardamos el rol
            $roles->save();

            DB::commit();

            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect()->route('roles.edit', array($roles->id))
                ->with('success', trans('roles/lang.okUpdate'))
                ->with('tab', "tab_1");
        } catch (\PDOException $e) {
            DB::rollBack();
            return redirect()->route('roles.edit', array($roles->id))
                ->with('error', trans('roles/lang.errorediciion'))
                ->with('tab', "tab_1");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Si no tiene permisos para borrar lo echamos
        if (!auth()->user()->can('admin-users-delete')) {
            app()->abort(403);
        }

        $role = Role::find($id);

        if (is_null($role)) {
            app()->abort(500);
        }

        $role->delete();

        DB::table('permission_role')->where("role_id", "=", $id)->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Rol eliminado',
            'id' => $role->id
        ));
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-roles-update')) {
            app()->abort(403);
        }

        $role = Role::find($id);

        if (!is_null($role)) {
            $role -> active = !$role -> active;
            return $role -> save() ? 1 : 0 ;
        }

        return 0;
    }

    public function getData()
    {
        $roles = Role::select([
                'roles.id',
                'roles.active',
                'roles.display_name',
                'roles.description',
                'roles.fixed'
            ]);

        return Datatables::of($roles)
            ->editColumn('active', function ($data) {
                return '<button class="btn '.($data->active?"btn-success":"btn-danger").' btn-sm" '.
                    (auth()->user()->can("admin-roles-update")?
                        "onclick=\"javascript:changeStatus('".url('admin/roles/state/'.$data->id)."');\"":"").'
                        data-content="'.($data->active?trans('general/admin_lang.descativa'):
                        trans('general/admin_lang.activa')).'"
                        data-placement="right" data-toggle="popover">
                        <i class="fa '.($data->active?"fa-eye":"fa-eye-slash").'" aria-hidden="true"></i>
                        </button>';
            })
            ->editColumn('actions', function ($data) {
                $actions = '';
                if (!auth()->user()->can("admin-roles-update") && auth()->user()->can("admin-roles-read")) {
                    $actions .= '<button class="btn bg-purple btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/roles/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.ver') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-search" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-roles-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/roles/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-roles-delete") && !$data->fixed) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\''.
                        url('admin/roles/'.$data->id).'\');" data-content="'.
                        trans('general/admin_lang.borrar').'" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                return $actions;
            })
            ->removeColumn('id')
            ->removeColumn('fixed')
            ->rawColumns(['active', 'actions'])
            ->make();
    }

    public function getRoleForm($id)
    {
        // Si nos viene un iduser lo buscamos y si no creamos uno nuevo para el formulario.
        if ($id==0) {
            $role = new Role();
            $form_data = array('route' => array('roles.store'), 'method' => 'POST',
                'id' => 'formData', 'class' => 'form-horizontal');
        } else {
            $role = Role::find($id);
            $form_data = array('route' => array('roles.update', $role->id), 'method' => 'PATCH',
                'id' => 'formData', 'class' => 'form-horizontal');
        }

        // Si el user no se ha cargado correctamente, devolvemos un error
        if (is_null($role)) {
            app()->abort(500);
        }

        return view('modules.roles.admin_edit_form', compact('role', 'form_data', 'id'));
    }
}
