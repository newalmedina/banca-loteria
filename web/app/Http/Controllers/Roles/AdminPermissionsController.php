<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\AdminController;
use App\Models\PermissionsTree;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Redirect;

class AdminPermissionsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-roles';
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

        $permissionsTree = PermissionsTree::withDepth()->with('permission')->get()->sortBy('_lft');

        $role = Role::find($id);
        $a_arrayPermisos = $role->getArrayPermissions();

        if (is_null($role)) {
            app()->abort(500);
        }


        return view('modules.roles.admin_permissions_form', compact(
            'permissionsTree',
            'id',
            'role',
            'a_arrayPermisos'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Si no tiene permisos para modificar o visualizar lo echamos
        if (!auth()->user()->can('admin-roles-update')) {
            app()->abort(403);
        }

        $idpermissions = explode(",", $request->input('results'));
        $idrole = $request->input("id");

        // Compruebo que el rol al que se quieren asignar datos existe
        $role = Role::find($idrole);

        if (is_null($role)) {
            app()->abort(500);
        }

        try {
            DB::beginTransaction();

            // Asigno el array de permisos al rol
            $role->syncPermissions($idpermissions);

            DB::commit();

            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect()->route('roles.edit', array($idrole, '2'))
                ->with('success', trans('roles/lang.okUpdate_permission'))
                ->with('tab', "tab_2");
        } catch (\PDOException $e) {
            DB::rollBack();
            return redirect()->route('roles.edit', array($idrole, '2'))
                ->with('error', trans('roles/lang.errorediciion'))
                ->with('tab', "tab_2");
        }
    }
}
