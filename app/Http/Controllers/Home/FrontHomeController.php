<?php

namespace App\Http\Controllers\Home;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FrontHomeController extends Controller
{
    public function index()
    {
        if (config("general.only_backoffice", false)) {
            return redirect()->to('admin/');
        } else {
            $page_title = trans('home/front_lang.home');

            return view(
                'modules.home.front_index',
                compact(
                    'page_title'
                )
            );
        }
    }
}
