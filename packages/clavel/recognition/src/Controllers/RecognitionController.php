<?php

namespace Clavel\Recognition\Controllers;

use Exception;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecognitionController extends Controller
{
    public function index(Request $request)
    {
        $page_title = trans("recognition::general/front_lang.title");

        return view(
            'recognition::recognition.front_index',
            compact(
                'page_title'
            )
        );
    }
}
