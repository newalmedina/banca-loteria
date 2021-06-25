<?php


namespace Clavel\FileTransfer\Controllers;

use App\Http\Controllers\Controller;
use Clavel\FileTransfer\Services\Upload;
use Illuminate\Http\Request;

class FrontFileTransferController extends Controller
{
    public function index(Request $request)
    {
        if (Upload::canUpload($request->ip()) !== true) {
            $u = $request->get('u', '');
            $page_title = trans("file-transfer::front_lang.cannotupload");

            return view(
                'file-transfer::file-transfer.front_cannotupload',
                compact(
                    'page_title',
                    'u'
                )
            );
        } else {
            return redirect()->route('file-transfer.upload.create');
        }
    }
}
