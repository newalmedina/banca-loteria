<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\AdminController;
use App\Modules\Settings\Models\Setting;
use App\Modules\Settings\Requests\AdminSettingsRequests;

class AdminSettingsController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-cog"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-settings';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-settings-update')) {
            app()->abort(403);
        }

        $page_title = trans("Settings::admin_lang.title");

        $settings = Setting::orderBy('order', 'ASC')->get();

        return view("Settings::admin_index", compact('page_title', 'settings'));
        //->with('page_title_icon', $this->page_title_icon);
    }

    public function update(AdminSettingsRequests $request)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-settings-update')) {
            app()->abort(403);
        }

        $settings = Setting::all();

        foreach ($settings as $setting) {
            $setting->value = $request->input($setting->key, "");
            $setting->save();
        }


        return redirect()->to('admin/settings')
            ->with('success', trans('Settings::admin_lang.save_ok'));
    }
}
