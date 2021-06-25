<?php

namespace Clavel\TimeTracker\Controllers\Config;

use App\Http\Controllers\AdminController;
use Clavel\TimeTracker\Models\Config;
use Clavel\TimeTracker\Models\Customer;
use Clavel\TimeTracker\Requests\ConfigRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminConfigController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-cogs" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-timetracker-config';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('admin-timetracker-config-list')) {
            app()->abort(403);
        }

        $page_title = trans("timetracker::config/admin_lang.title");

        $config = Config::first();
        $form_data = array('route' => array('admin.timetracker-config.update'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal');

        return view(
            'timetracker::config.admin_index',
            compact(
                'page_title',
                'config',
                'form_data'
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (!Auth::user()->can('admin-timetracker-config-update')) {
            app()->abort(403);
        }

        $config = Config::first();
        if (empty($config)) {
            $config = new Config();
        }

        $config->budget_prefix = $request->get("budget_prefix", "");
        $config->order_prefix = $request->get("order_prefix", "");
        $config->budget_counter = $request->get("budget_counter", 0);
        $config->order_counter = $request->get("order_counter", 0);
        $config->budget_digits = $request->get("budget_digits", 3);
        $config->order_digits = $request->get("order_digits", 3);

        $config->save();


        return redirect('admin/timetracker-config/')
            ->with('success', trans('general/admin_lang.save_ok'));
    }
}
