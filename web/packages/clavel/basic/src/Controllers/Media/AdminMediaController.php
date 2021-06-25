<?php

namespace Clavel\Basic\Controllers\Media;

use Clavel\Basic\Requests\AdminMediaDirRequest;
use Clavel\Basic\Models\Media;
use App\Services\StoragePathWork;
use App\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use FFMpeg;

class AdminMediaController extends AdminController
{
    protected $page_title_icon = '<i class="fa  fa-file-image-o" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-media';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-media-list')) {
            app()->abort(403);
        }

        $myServiceSPW = new StoragePathWork("media");
        $subfolders = $myServiceSPW->readStorePath();

        $page_title = trans("basic::media/admin_lang.media");

        return view("basic::media.admin_index", compact('page_title', 'subfolders'))
        ->with('page_title_icon', $this->page_title_icon);
    }

    public function deleteDirectory($routedir)
    {
        $myServiceSPW = new StoragePathWork("media");

        if ($myServiceSPW->deleteDir(str_replace("|!|", "/", $routedir))) {
            return redirect("admin/media")
                ->with('success', trans('profile/front_lang.okGuardado'));
        } else {
            dd("error");
        }
    }

    public function createDirectory(AdminMediaDirRequest $request)
    {
        $myServiceSPW = new StoragePathWork("media");

        if ($myServiceSPW->createDir($request->input('act_folder')."/".$request->input('foldername'))) {
            if (!is_null($request->input('by_ajax')) && $request->input('by_ajax')=='1') {
                $subfolders = $myServiceSPW->readStorePath();
                return Response::json($subfolders);
            } else {
                return redirect("admin/media")
                    ->with('success', trans('profile/front_lang.okGuardado'));
            }
        } else {
            dd("error");
        }
    }

    public function uploadFiles(Request $request)
    {
        $myServiceSPW = new StoragePathWork("media");

        $files = $request->file();

        if (!is_null($files)) {
            foreach ($files as $file) {
                $filename = $myServiceSPW->saveFile($file, $request->input("foldertoUp"));

                $media = new Media();

                $media->filename = $filename;
                $media->mime = $file->getClientMimeType();
                $media->user_id = auth()->user()->id;
                $media->original_filename = $file->getClientOriginalName();
                $media->size = $file->getSize();
                $media->path = ($request->input("foldertoUp")=='/') ? '/media' : "/media".$request->input("foldertoUp");
                $media->save();
            }
        }
    }


    public function getData($path = 'root')
    {
        if ($path=='root') {
            $search = "/media";
        } else {
            $myServiceSPW = new StoragePathWork("media");
            $subfolders = $myServiceSPW->readStorePath();
            $search = "/media".$subfolders[$path];
        }

        $media = Media::select([
            "media.id",
            "media.original_filename",
            "up2.first_name",
            "media.path",
            "media.filename",
            "media.mime",
            "media.size"
        ])
            ->leftJoin('user_profiles as up2', 'up2.user_id', '=', 'media.user_id')
            ->where("path", "=", $search)->get();

        return Datatables::of($media)
            ->editColumn('path', function ($row) {
                if (strrpos($row->mime, "image/")!==false) {
                    return '<div style="width:100px; max-height: 100px; overflow: hidden;">
                        <a href="'.url("admin/media/file/".$row->id).'" target="_blank">
                            <img src="'.url("admin/media/file/".$row->id).'" style="width:100px;" alt="">
                        </a></div>';
                } else {
                    return '<div style="width:100px; text-align: center; max-height: 100px; overflow: hidden;">
                        <a href="'.url("admin/media/file/".$row->id).'" target="_blank">
                            <i class="fa fa-file" style="font-size:64px;" aria-hidden="true"></i>
                        </a></div>';
                }
            })
            ->editColumn('size', function ($row) {
                return $this->formatBytes($row->size);
            })
            ->addColumn('actions', function ($row) {
                $return = '';

                if (auth()->user()->can("admin-media-delete")) {
                    $return .= '<button class="btn btn-danger btn-sm"
                    onclick = "javascript:deleteElement(\''.url('admin/media/file/'.$row->id.'/destroy').'\');"
                    data - content = "'.trans('general/admin_lang.borrar').'"
                    data - placement = "left" data - toggle = "popover" >
                    <i class="fa fa-trash" aria-hidden="true"></i ></button>';
                }
                if (auth()->user()->can("admin-media-update") && $row->mime == "video/mp4") {
                    $return .= '&nbsp;<button class="btn btn-warning btn-sm"
                            onclick="javascript:optimizeVideo(\''.url('admin/media/file/'.$row->id.'/optimize').'\');"
                            data-content="'.trans('basic::media/admin_lang.optimize').'"
                            data-placement="left" data-toggle="popover">
                            <i class="fa fa-file-video-o" aria-hidden="true"></i></button>';
                }
                return $return;
            })
            ->removeColumn('id')
            ->rawColumns(['path', 'actions'])
            ->make();
    }

    /**
     * Format bytes to kb, mb, gb, tb
     *
     * @param  integer $size
     * @param  integer $precision
     * @return integer
     */
    public static function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }


    public function destroy($id)
    {
        $media = Media::find($id);

        if (is_null($media)) {
            app()->abort(500);
        }

        // Si no tiene permisos para borrar lo echamos
        if (!auth()->user()->can('admin-media-delete') && $media->user_id==auth()->user()->id) {
            app()->abort(404);
        }

        $myServiceSPW = new StoragePathWork("media");
        $myServiceSPW->deleteFile($media->filename, $media->path);

        $media->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Media eliminado',
            'id' => $media->id
        ));
    }

    public function getFile($id)
    {
        $media = Media::find($id);

        if (!empty($media)) {
            $myServiceSPW = new StoragePathWork("media");
            if (strpos($media->mime, "video") !== false) {
                return $myServiceSPW->showFileVideo($media->filename, $media->path);
            }
            return $myServiceSPW->showFile($media->filename, $media->path);
        }
    }

    public function optimize($id)
    {
        set_time_limit(3600);
        $media = Media::find($id);

        if (is_null($media)) {
            app()->abort(500);
        }

        // Si no tiene permisos para borrar lo echamos
        if (!auth()->user()->can('admin-media-update') && $media->user_id==auth()->user()->id) {
            app()->abort(404);
        }

        $video_path = storage_path('app/media'). "/" . $media->filename;
        $partes_ruta = pathinfo($video_path);
        $newName = $partes_ruta['filename'].'-x264.'.$partes_ruta['extension'];
        $newFileName = $partes_ruta['dirname'].'/'.$newName;


        if (!file_exists($video_path)) {
            app()->abort(404);
        }

        try {
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($video_path);
            $format = new FFMpeg\Format\Video\X264('aac');
            /*$format->on('progress', function ($video, $format, $percentage) {
                echo "$percentage % transcoded";
            });*/

            $video->save($format, $newFileName);

            $mediaNew = new Media();
            $mediaNew->filename = $newName;
            $mediaNew->mime = $media->mime;
            $mediaNew->user_id = auth()->user()->id;
            $mediaNew->original_filename = $media->original_filename;
            $mediaNew->size = filesize($newFileName);
            $mediaNew->path = $media->path;
            $mediaNew->save();


            return Response::json(array(
                'success' => true,
                'msg' => 'Media optimizado',
                'id' => $media->id
            ));
        } catch (\Exception $ex) {
        }
    }
}
