<?php

namespace Clavel\TimeTracker\Controllers\Dashboard;

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-tachometer" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-timetracker-dashboard';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('admin-timetracker-dashboard-list')) {
            app()->abort(403);
        }

        $page_title = trans("timetracker::dashboard/admin_lang.title");

        return view('timetracker::dashboard.admin_index', compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }
}
