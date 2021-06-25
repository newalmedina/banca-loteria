<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\FrontProfileRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\User;
use App\Services\UserCheckLoginPass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Session;
use App\Services\StoragePathWork;

class FrontProfileController extends FrontController
{


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //Obtengo la información del usuario para pasarsela al formulario
        $user = User::with('userProfile')->find(auth()->user()->getAuthIdentifier());

        $page_title = trans('profile/front_lang.mi_perfil');

        $form_data = array(
            'route' => array('front.profile'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal',
            'files'=>true,
        );

        return view(
            'modules.profile.front_index',
            compact(
                'page_title',
                'user',
                'form_data'
            )
        );
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FrontProfileRequest $request)
    {

        // Id actual
        $idprofile = auth()->user()->id;

        // Creamos un nuevo objeto para nuestro nuevo usuario
        $user = User::with('userProfile')->find($idprofile);

        // Si el usuario no existe entonces lanzamos un error 404 :(
        if (is_null($user)) {
            app()->abort(500);
        }

        $myServiceSPW = new StoragePathWork("users");

        // Si la data es valida se la asignamos al usuario
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        if (!empty($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->userProfile->first_name = $request->input('userProfile.first_name');
        $user->userProfile->last_name = $request->input('userProfile.last_name');
        $user->userProfile->gender = $request->input('userProfile.gender');
        $user->userProfile->user_lang = $request->input("userProfile.user_lang");
        if ($request->input("delete_photo")=='1') {
            $myServiceSPW->deleteFile($user->userProfile->photo, '');
            $user->userProfile->photo="";
        }

        $user->userProfile->confirmed = $request->input('terms');
        try {
            DB::beginTransaction();

            $files = $request->file('profile_image', []);

            foreach ($files as $file) {
                if (!is_null($file)) {
                    $filename = $myServiceSPW->saveFile($file, '');

                    $user->userProfile->photo = $filename;
                }
            }

            // Guardamos el usuario
            if (!$user->push()) {
                // En caso de error regresa a la acción create con los datos y los errores encontrados
                return redirect('profile')
                    ->withInput($request->except('password'))
                    ->withErrors($user->errors);
            }

            // Redirect to the new user page
            DB::commit();

            Session::put('lang', $user->userProfile->user_lang);
            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect('profile')
                ->with('success', trans('profile/front_lang.okGuardado'));
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            return redirect('profile')
                ->with('error', trans('profile/front_lang.errorediciion'));
        }
    }

    public function checkLoginExists(Request $request)
    {
        $check = new UserCheckLoginPass($request->get('user_id'), $request->get('login'));
        return $check->existUserLoginService();
    }

    public function getPhoto($photo)
    {
        $myServiceSPW = new StoragePathWork("users");
        return $myServiceSPW->showFile($photo, '/users');
    }
}
