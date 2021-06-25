<?php

namespace Clavel\Basic\Controllers\Pages;

use App\Http\Controllers\Controller;
use Clavel\Basic\Models\Page;
use Illuminate\Http\Request;

class FrontPagesController extends Controller
{
    public function index(Request $request)
    {
        $page = Page::whereTranslation('url_seo', $request->slug)
            ->where("pages.active", "=", "1")
            ->first();

        if (is_null($page)) {
            app()->abort(404);
        }

        if ($page->permission=='1') {
            if (!auth()->check()) {
                return redirect()->guest('login');
            }

            if (!auth()->user()->can($page->permission_name)) {
                abort(403);
            }
        }

        $page_title = $page->title;

        //Meta Providers
        $a_metas_providers = $page->getArrayPageProviders();

        return view('basic::pages.front_index', compact('page_title', 'page', 'a_metas_providers'));
    }
}
