<?php

namespace Clavel\Basic\Controllers\Media;

use App\Http\Controllers\AdminController;
use Clavel\Basic\Models\Media;
use App\Services\StoragePathWork;
use Illuminate\Support\Facades\Response;

class AdminMediaViewerController extends AdminController
{
    public function index($only_img = '0')
    {
        $myServiceSPW = new StoragePathWork("media");
        $subfolders = $myServiceSPW->readStorePath();

        $page_title = trans("basic::media/admin_lang.media");

        return view('basic::media.admin_viewer', compact('page_title', 'subfolders', 'only_img'));
    }

    public function loadImages()
    {
        if ($_REQUEST['folder'] == '/') {
            $search = "/media";
        } else {
            $search = "/media" . $_REQUEST['folder'];
        }

        $media = Media::where("path", "=", $search)->get();

        $a_response = [];
        foreach ($media as $key => $value) {
            $a_response[$value->id]["delete"] = ($value->user_id == auth()->user()->id ||
                auth()->user()->can('admin-media-delete')) ? '1' : '0';
            if ($_REQUEST["only_img"] == '1' && strrpos($value->mime, "image/") !== false) {
                $a_response[$value->id]["src"] = '<img src="' . url("media/getAnnex/" . $value->id) .
                    '" style="width:120px;" alt="">';
                $a_response[$value->id]["original_filename"] = $value->original_filename;
                $a_response[$value->id]["url"] = url("media/getAnnex/" . $value->id);
            } elseif ($_REQUEST["only_img"] == '0') {
                if (strrpos($value->mime, "image/") !== false) {
                    $a_response[$value->id]["src"] = '<img src="' . url("media/getAnnex/" . $value->id) .
                        '" style="width:120px; vertical-align: middle;" alt="">';
                    $a_response[$value->id]["original_filename"] = $value->original_filename;
                    $a_response[$value->id]["url"] = url("media/getAnnex/" . $value->id);
                } else {
                    $a_response[$value->id]["src"] =
                        '<i class="fa fa-file" style="font-size:64px;" aria-hidden="true"></i>';
                    $a_response[$value->id]["original_filename"] = $value->original_filename;
                    $a_response[$value->id]["url"] = url("media/getAnnex/" . $value->id);
                }
            }
        }

        return Response::json($a_response);
    }
}
