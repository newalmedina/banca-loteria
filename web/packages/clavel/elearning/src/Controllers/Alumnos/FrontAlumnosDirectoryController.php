<?php


namespace Clavel\Elearning\Controllers\Alumnos;

use App\Http\Controllers\FrontController;
use Clavel\Elearning\Models\Alumno;
use Clavel\Elearning\Models\SpatieMedia;
use Exception;

use Illuminate\Support\Facades\Session;

class FrontAlumnosDirectoryController extends FrontController
{
    public function index()
    {
        return $this->show();
    }
    public function show()
    {
        $user = auth()->user();
        if (empty($user)) {
            abort(404);
        }

        $alumno = Alumno::find($user->userProfile->id);
        if (empty($alumno)) {
            abort(404);
        }

        $mediaFiles = $alumno->getMedia();

        $page_title = trans('elearning::alumnos/front_lang.folder_alumno'). " - ".$user->userProfile->fullName;

        return view(
            "elearning::alumnos.front_index",
            compact(
                "page_title",
                'alumno',
                'mediaFiles'
            )
        );
    }

    public function changeDirectory(Request $request, $directory)
    {
        if (auth()->user() == null) {
            abort(404);
        }

        $user = auth()->user();
        if (empty($user)) {
            abort(404);
        }

        if ($directory == 'inbox') {
            Session::forget('alumnos_directorio_front');
        } else {
            Session::put('alumnos_directorio_front', '1');
        }

        return redirect('alumnos-directory/');
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
        if (auth()->user() == null) {
            abort(404);
        }
        $folder = $request->input("folder", '');

        $user = auth()->user();
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
                    $mediaItem->user_id = $user->id;
                    $mediaItem->save();
                }
            }

            return redirect('alumnos-directory/'.$user->id)
                ->with('success', trans('elearning::alumnos/front_lang.save_ok'));
        } catch (Exception $e) {
            return redirect('alumnos-directory/'.$user->id)
                ->with('error', trans('elearning::alumnos/front_lang.save_ko'));
        }
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(\Illuminate\Http\Request $request)
    {
        if (auth()->user() == null) {
            abort(404);
        }
        $media_id = $request->input("media_id", 0);

        $folder = $request->input("folder", '');

        $user = auth()->user();
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
            return redirect('alumnos-directory/'.$user->id)
                ->with('success', trans('elearning::alumnos/front_lang.save_ok'));
        } catch (Exception $e) {
            return redirect('alumnos-directory/'.$user->id)
                ->with('error', trans('elearning::alumnos/front_lang.save_ko'));
        }
    }

    public function getMedia(\Illuminate\Http\Request $request, $media_id)
    {
        if (auth()->user() == null) {
            abort(404);
        }

        $user = auth()->user();
        if (empty($user)) {
            abort(404);
        }

        $alumno = Alumno::find($user->userProfile->id);
        if (empty($alumno)) {
            abort(404);
        }

        try {
            $mediaFile = SpatieMedia::where('id', $media_id)
                ->where('model_type', 'Clavel\Elearning\Models\Alumno')
                ->where('model_id', $alumno->id)
            ->first();

            return response()->download($mediaFile->getPath(), $mediaFile->file_name);
        } catch (Exception $e) {
        }

        return false;
    }
}
