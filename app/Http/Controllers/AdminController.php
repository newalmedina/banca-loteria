<?php
namespace App\Http\Controllers;

class AdminController extends Controller
{
    protected $access_permission = null;

    public function __construct()
    {
        $this->middleware("admin");

        // Since verson 5.3 we can't acess Auth directly see next link
        // https://github.com/laravel/docs/blob/5.3/upgrade.md#session-in-the-constructor

        $this->middleware(function ($request, $next) {
            if (isset($this->access_permission)) {
                if (!auth()->user()->can($this->access_permission)) {
                    app()->abort(403);
                }
            }
            return $next($request);
        });
    }
}
