<?php

namespace Clavel\Basic\Controllers\Media;

use Clavel\Basic\Models\Media;
use App\Services\StoragePathWork;

use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAnnex($id)
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
}
