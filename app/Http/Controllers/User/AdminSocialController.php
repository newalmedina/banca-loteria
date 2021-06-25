<?php namespace App\Http\Controllers\User;

use App\Http\Controllers\AdminController;
use App\Http\Requests\AdminUsersSocialRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminSocialController extends AdminController
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

        $user = User::with('userProfile')->find($id);
        if (is_null($user)) {
            app()->abort(500);
        }

        $form_data = array('route' => array('users.social.update', $user->id), 'method' => 'PATCH',
            'id' => 'formData', 'class' => 'form-horizontal');

        return view('modules.users.admin_social_form', compact('id', 'user', 'form_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUsersSocialRequest $request, $id)
    {
        // Si no tiene permisos para modificar o visualizar lo echamos
        if (!auth()->user()->can('admin-users-update')) {
            app()->abort(403);
        }

        $iduser = $request->input("id");

        // Compruebo que el rol al que se quieren asignar datos existe
        $user = User::find($iduser);

        if (is_null($user)) {
            app()->abort(500);
        }

        try {
            // Si la data es valida se la asignamos al usuario
            $user->userProfile->facebook = $request->input('userProfile.facebook', '');
            $user->userProfile->twitter = $request->input('userProfile.twitter', '');
            $user->userProfile->linkedin = $request->input('userProfile.linkedin', '');
            $user->userProfile->youtube = $request->input('userProfile.youtube', '');
            $user->userProfile->bio = $request->input('userProfile.bio', '');

            DB::beginTransaction();

            // Guardamos el usuario
            $user->push();

            // Redirect to the new user page
            DB::commit();

            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect()->route('users.edit', array($iduser, '3'))
                ->with('success', trans('users/lang.okUpdate_social'))
                ->with('tab', "tab_3");
        } catch (\PDOException $e) {
            DB::rollBack();
            return redirect()->route('users.edit', array($iduser, '3'))
                ->with('error', trans('users/lang.errorediciion'))
                ->with('tab', "tab_3");
        }
    }
}
