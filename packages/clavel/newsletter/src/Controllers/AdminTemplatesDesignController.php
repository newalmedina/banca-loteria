<?php

namespace App\Modules\Newsletter\Controllers;

use App\Http\Controllers\AdminController;
use App\Models\Idioma;
use App\Modules\Newsletter\Models\NewsletterTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class AdminTemplatesDesignController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-paint-brush"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-templates-design';
    }

    public function index($id)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-templates-design')) {
            abort(403);
        }
        $page_title = trans("Newsletter::admin_lang_template.design");

        $template = NewsletterTemplates::findOrFail($id);
        $idiomas = Idioma::active()->get();
        $form_data = array('method' => 'POST', 'id' => 'formData', 'class' => 'form-horizontal');

        return view("Newsletter::templates.admin_design", compact("page_title", 'template', 'form_data', 'idiomas'));
        //->with('page_title_icon', $this->page_title_icon);
    }


    public function save(Request $request)
    {
        if (!auth()->user()->can('admin-templates-design')) {
            abort(403);
        }

        $template = NewsletterTemplates::findOrFail($request->input("id"));

        try {
            DB::beginTransaction();
            $template->update($request->except('_token'));
            DB::commit();

            return redirect()->to('admin/templates/'.$template->id."/design")
                ->with('success', trans('templates/admin_lang.save_ok'));
        } catch (\PDOException $e) {
            DB::rollBack();

            return redirect()->to('admin/templates/'.$template->id.'/design')
                ->with('error', trans('templates/admin_lang.error_code_exists'));
        }
    }

    public function preview($id)
    {
        $template = NewsletterTemplates::findOrFail($id);
        $idiomas = Idioma::active()->get();
        return view("Newsletter::templates.admin_preview", compact("template", 'idiomas'));
    }
}
