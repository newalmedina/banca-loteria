<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Redirect;

class AdminRolesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-users';
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
        if (!auth()->user()->can('admin-users-update') && !auth()->user()->can('admin-users-read')) {
            app()->abort(403);
        }
        $user = User::find($id);

        if (is_null($user)) {
            app()->abort(500);
        }

        $roles = Role::where("active", "=", 1)->get();

        return view('modules.users.admin_roles_form', compact('roles', 'user'));
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
        if (!auth()->user()->can('admin-users-update')) {
            app()->abort(403);
        }

        $idroles = explode(",", $request->input('results'));
        $iduser = $request->input("id");

        // Compruebo que el rol al que se quieren asignar datos existe
        $user = User::find($iduser);

        if (is_null($user)) {
            app()->abort(500);
        }

        try {
            DB::beginTransaction();

            $user->syncRoles($idroles);

            DB::commit();

            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect()->route('users.edit', array($iduser, '2'))
                    ->with('success', trans('users/lang.okUpdate_roles'))
                ->with('tab', "tab_2");
        } catch (\PDOException $e) {
            DB::rollBack();
            return redirect()->route('users.edit', array($iduser, '2'))
                ->with('error', trans('users/lang.errorediciion'))
                ->with('tab', "tab_2");
        }
    }
}
