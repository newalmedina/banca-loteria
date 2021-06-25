<?php

namespace App\Modules\Newsletter\Controllers;

use App\Http\Controllers\AdminController;
use App\Modules\Newsletter\Requests\AdminTemplatesRequest;
use App\Modules\Newsletter\Models\NewsletterTemplates;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;

class AdminTemplatesController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-file-code-o" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-templates';
    }

    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-templates-list')) {
            abort(403);
        }

        $page_title = trans("Newsletter::admin_lang_template.templates");

        return view("Newsletter::templates.admin_index", compact('page_title'));
        //->with('page_title_icon', $this->page_title_icon);
    }

    public function getData()
    {
        $templates = NewsletterTemplates::select([
            'id',
            'active',
            'nombre',
            'slug',
            'created_at',
            'updated_at',
        ]);

        return Datatables::of($templates)
            ->editColumn('active', function ($data) {
                return '<button class="btn ' . ($data->active ? "btn-success" : "btn-danger") . ' btn-sm" ' .
                    (auth()->user()->can("admin-templates-update") ?
                        "onclick=\"javascript:changeStatus('" .
                        url('admin/templates/state/' . $data->id) . "');\"" : "") . '
                        data-content="' . ($data->active ? trans('general/admin_lang.descativa') :
                        trans('general/admin_lang.activa')) . '"
                        data-placement="right" data-toggle="popover">
                        <i class="fa ' . ($data->active ? "fa-eye" : "fa-eye-slash") . '" aria-hidden="true"></i>
                        </button>';
            })
            ->addColumn('actions', function ($data) {
                $actions = '';
                $actions .= '<button class="btn bg-navy btn-sm" onclick="javascript:showPreview(\'' .
                    url('admin/templates/' . $data->id . '/preview') . '\');" data-content="' .
                    trans('Newsletter::admin_lang_template.design_preview') . '"
                    data-placement="right" data-toggle="popover">
                        <i class="fa fa-search" aria-hidden="true"></i></button> ';
                if (auth()->user()->can("admin-templates-design")) {
                    $actions .= '<button class="btn bg-purple btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/templates/' . $data->id . '/design') . '\';" data-content="' .
                        trans('Newsletter::admin_lang_template.design') . '"
                        data-placement="right" data-toggle="popover">
                        <i class="fa fa-paint-brush" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-templates-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/templates/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-templates-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                        url('admin/templates/' . $data->id) . '\');" data-content="' .
                        trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-templates-create")) {
                    $actions .= '<button class="btn bg-maroon btn-sm" onclick="javascript:duplicateElement(\'' .
                        url('admin/templates/' . $data->id . '/duplicate') . '\');" data-content="' .
                        trans('general/admin_lang.duplicate') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-clone" aria-hidden="true"></i></button>';
                }
                return $actions;
            })
            ->removeColumn('id')
            ->rawColumns(['active', 'actions'])
            ->make();
    }

    public function create()
    {
        if (!auth()->user()->can('admin-templates-create')) {
            abort(403);
        }

        $page_title = trans("Newsletter::admin_lang_template.nuevo_templates");
        $form_data = array(
            'route' => array('templates.store'), 'method' => 'POST',
            'id' => 'formData', 'class' => 'form-horizontal', 'files' => true
        );
        $templates = new NewsletterTemplates();

        return view('Newsletter::templates.admin_edit', compact('page_title', 'templates', 'form_data'));
    }

    public function store(AdminTemplatesRequest $request)
    {
        if (!auth()->user()->can('admin-templates-create')) {
            abort(403);
        }

        $request->merge(array(
            'slug' => str_slug($request->input("nombre")),
            'background_content' => "#f1f1f1",
            'background_page' => "#ffffff",
            'font_color' => "#8e8e90",
            'title_font_color' => "#53545e",
        ));

        try {
            DB::beginTransaction();
            $templates = NewsletterTemplates::create($request->except('_token'));
            DB::commit();

            return redirect()->to('admin/templates/' . $templates->id . "/edit")
                ->with('success', trans('Newsletter::admin_lang_template.save_ok'));
        } catch (\PDOException $e) {
            DB::rollBack();

            return redirect()->to('admin/templates/create')
                ->with('error', trans('Newsletter::admin_lang_template.error_code_exists'));
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('admin-templates-update')) {
            app()->abort(403);
        }

        $templates = NewsletterTemplates::findOrFail($id);
        $form_data = array(
            'route' => array('templates.update', $templates->id),
            'method' => 'PATCH', 'id' => 'formData', 'class' => 'form-horizontal'
        );
        $page_title = trans("Newsletter::admin_lang_template.modify_banner");

        return view('Newsletter::templates.admin_edit', compact('page_title', 'templates', 'form_data'));
    }

    public function update(AdminTemplatesRequest $request, $id)
    {
        if (!auth()->user()->can('admin-templates-update')) {
            app()->abort(403);
        }

        $request->merge(array('slug' => str_slug($request->input("nombre"))));

        try {
            DB::beginTransaction();
            $templates = NewsletterTemplates::findOrFail($id);
            $templates->update($request->except('_token'));
            DB::commit();

            return redirect()->to('admin/templates/' . $templates->id . "/edit")
                ->with('success', trans('Newsletter::admin_lang_template.save_ok'));
        } catch (\PDOException $e) {
            DB::rollBack();

            return redirect()->to('admin/templates/' . $templates->id . '/edit')
                ->with('error', trans('Newsletter::admin_lang_template.error_code_exists'));
        }
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-templates-update')) {
            app()->abort(403);
        }
        $templates = NewsletterTemplates::findOrFail($id);
        $templates->active = !$templates->active;
        return $templates->save() ? 1 : 0;
    }

    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-templates-delete')) {
            app()->abort(403);
        }

        $templates = NewsletterTemplates::findOrFail($id);
        $templates->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Plantlla eliminada',
            'id' => $templates->id
        ));
    }

    public function duplicate($id)
    {
        if (!auth()->user()->can('admin-templates-create')) {
            app()->abort(403);
        }
        $template_selected = NewsletterTemplates::findOrFail($id);
        $template_new = $template_selected->replicate();
        $template_new->nombre = $template_new->nombre . " (cloned)";
        $template_new->slug = str_slug($template_new->nombre . " (cloned)");
        $template_new->save();
    }
}
