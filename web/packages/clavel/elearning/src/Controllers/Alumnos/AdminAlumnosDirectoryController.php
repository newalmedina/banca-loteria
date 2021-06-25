<?php

namespace Clavel\Elearning\Controllers\Alumnos;

use App\Http\Controllers\AdminController;
use App\Models\User;
use Clavel\Elearning\Models\Alumno;
use Clavel\Elearning\Models\SpatieMedia;
use Exception;

use Illuminate\Support\Facades\Session;

class AdminAlumnosDirectoryController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-folder" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-alumnos';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if (auth()->user() != null && (!auth()->user()->can('admin-alumnos'))) {
            abort(404);
        }

        $user = User::where('id', $id)->first();
        if (empty($user)) {
            abort(404);
        }

        $alumno = Alumno::find($user->userProfile->id);
        if (empty($alumno)) {
            abort(404);
        }

        $mediaFiles = $alumno->getMedia();

        $page_title = trans('elearning::alumnos/admin_lang.folder_alumno'). " - ".$user->userProfile->fullName;


        return view("elearning::alumnos.admin_directory_index", compact(
            'page_title',
            'alumno',
            'mediaFiles'
        ))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function changeDirectory(Request $request, $id, $directory)
    {
        if (auth()->user() != null && (!auth()->user()->can('admin-alumnos'))) {
            abort(404);
        }

        $user = User::where('id', $id)->first();
        if (empty($user)) {
            abort(404);
        }


        if ($directory == 'inbox') {
            Session::forget('alumnos_directorio_admin');
        } else {
            Session::put('alumnos_directorio_admin', '1');
        }

        return redirect('admin/alumnos-directory/'.$user->id);
    }

    /**
     * Upload Avatar resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function upload(\Illuminate\Http\Request $request)
    {
        if (auth()->user() != null && (!auth()->user()->can('admin-alumnos'))) {
            abort(404);
        }
        $user_id = $request->input("user_id", 0);
        $folder = $request->input("folder", '');

        $user = User::where('id', $user_id)->first();
        if (empty($user)) {
            abort(404);
        }

        $alumno = Alumno::find($user->userProfile->id);
        if (empty($alumno)) {
            abort(404);
        }

        try {
            if (sizeof($request->alumnos_ficheros)>0) {
                foreach ($request->alumnos_ficheros as $fichero) {
                    $mediaItem = $alumno->addMedia($fichero)
                        ->withCustomProperties(['folder' => $folder])
                        ->toMediaCollection();
                    $mediaItem->user_id = $user_id;
                    $mediaItem->save();
                }
            }

            return redirect('admin/alumnos-directory/'.$user->id)
                ->with('success', trans('elearning::alumnos/admin_lang.save_ok'));
        } catch (Exception $e) {
            return redirect('admin/alumnos-directory/'.$user->id)
                ->with('error', trans('elearning::alumnos/admin_lang.save_ko'));
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(\Illuminate\Http\Request $request)
    {
        if (auth()->user() != null && (!auth()->user()->can('admin-alumnos'))) {
            abort(404);
        }
        $media_id = $request->input("media_id", 0);
        $user_id = $request->input("user_id", 0);
        $folder = $request->input("folder", '');

        $user = User::where('id', $user_id)->first();
        if (empty($user)) {
            abort(404);
        }

        $alumno = Alumno::find($user->userProfile->id);
        if (empty($alumno)) {
            abort(404);
        }

        try {
            $mediaFile = SpatieMedia::find($media_id);
            $mediaFile->delete();
            return redirect('admin/alumnos-directory/'.$user->id)
                ->with('success', trans('elearning::alumnos/admin_lang.save_ok'));
        } catch (Exception $e) {
            return redirect('admin/alumnos-directory/'.$user->id)
                ->with('error', trans('elearning::alumnos/admin_lang.save_ko'));
        }
    }

    public function getMedia(\Illuminate\Http\Request $request, $media_id)
    {
        if (auth()->user() != null && (!auth()->user()->can('admin-alumnos'))) {
            abort(404);
        }

        try {
            $mediaFile = SpatieMedia::find($media_id);

            return response()->download($mediaFile->getPath(), $mediaFile->file_name);
        } catch (Exception $e) {
        }

        return false;
    }
}
