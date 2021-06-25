<?php

namespace Clavel\CrudGenerator\Controllers;

use App\Http\Controllers\AdminController;
use Clavel\CrudGenerator\Models\Module;
use Clavel\CrudGenerator\Models\ModuleField;
use Clavel\CrudGenerator\Requests\ModuleRequest;
use Clavel\CrudGenerator\Services\CrudGenerator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CrudGeneratorController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-cube" aria-hidden="true"></i>';

    const ENTRIES_PER_PAGE = [
        '10' => '10',
        '25' => '25',
        '50' => '50',
        '100' => '100'
    ];


    const ORDER_DIRECTION = [
        'ASC' => 'ASC',
        'DESC' => 'DESC'
    ];


    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-modulos-crud';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-modulos-crud-list')) {
            app()->abort(403);
        }

        $page_title = trans("crud-generator::modules/admin_lang.title");

        return view("crud-generator::modules.admin_index", compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-modulos-crud-create')) {
            app()->abort(403);
        }

        $module = new Module();
        $form_data = array(
            'route' => array('crud-generator.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("crud-generator::modules/admin_lang.new");

        $entries_page_list = CrudGeneratorController::ENTRIES_PER_PAGE;
        $order_direction_list = CrudGeneratorController::ORDER_DIRECTION;

        /*
        $order_by_field_list = ModuleField::where('crud_module_id', $module->id)
            ->where('in_list', true)
            ->orderBy('order_list', 'ASC')
            ->pluck('column_title', 'column_name')
            ->toArray();
        */
        // En la creación forzamos a mínimo el ID ya que no tenemos campos
        $order_by_field_list = [
            "id" => "ID"
        ];

        return view(
            'crud-generator::modules.admin_edit',
            compact(
                'page_title',
                'module',
                'form_data',
                'entries_page_list',
                'order_direction_list',
                'order_by_field_list'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ModuleRequest $request)
    {
        if (!auth()->user()->can('admin-modulos-crud-create')) {
            app()->abort(403);
        }

        $module = new Module();
        $this->saveData($module, $request);

        return redirect()->to('admin/crud-generator/' . $module->id . "/edit")
            ->with('success', trans('crud-generator::modules/admin_lang.save_ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('admin-modulos-crud-update')) {
            app()->abort(403);
        }

        $module = Module::find($id);

        $form_data = array(
            'route' => array('crud-generator.update', $module->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("crud-generator::modules/admin_lang.modify");

        $entries_page_list = CrudGeneratorController::ENTRIES_PER_PAGE;
        $order_direction_list = CrudGeneratorController::ORDER_DIRECTION;
        $order_by_field_list = ModuleField::where('crud_module_id', $module->id)
            ->where('in_list', true)
            ->orderBy('order_list', 'ASC')
            ->pluck('column_title', 'column_name')
            ->toArray();
        if (empty($order_by_field_list)) {
            // En la creación forzamos a mínimo el ID ya que no tenemos campos
            $order_by_field_list = [
                "id" => "ID"
            ];
        }

        return view(
            'crud-generator::modules.admin_edit',
            compact(
                'page_title',
                'module',
                'form_data',
                'entries_page_list',
                'order_direction_list',
                'order_by_field_list'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ModuleRequest $request, $id)
    {
        if (!auth()->user()->can('admin-modulos-crud-update')) {
            app()->abort(403);
        }

        $module = Module::find($id);
        if (empty($module)) {
            app()->abort(500);
        }
        $this->saveData($module, $request);

        return redirect()->to('admin/crud-generator/' . $module->id . "/edit")
            ->with('success', trans('crud-generator::modules/admin_lang.save_ok'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-modulos-crud-delete')) {
            app()->abort(403);
        }

        $module = Module::find($id);

        if (empty($module)) {
            app()->abort(500);
        }

        $module->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans('crud-generator::modules/admin_lang.delete'),
            'id' => $module->id
        ));
    }

    public function getData()
    {
        $modules = Module::select(
            array(
                'id',
                'active',
                'title',
                'model'
            )
        );

        return Datatables::of($modules)
            ->editColumn('active', function ($data) {
                return '<button class="btn ' . ($data->active ? "btn-success" : "btn-danger") . ' btn-sm" ' .
                    (auth()->user()->can("admin-modulos-crud-update") ? "onclick=\"javascript:changeStatus('" .
                        url('admin/crud-generator/state/' . $data->id) . "');\"" : "") . '
                        data-content="' . ($data->active ?
                        trans('general/admin_lang.descativa') :
                        trans('general/admin_lang.activa')) . '"
                        data-placement="right" data-toggle="popover">
                        <i class="fa ' . ($data->active ? "fa-eye" : "fa-eye-slash") . '" aria-hidden="true"></i>
                        </button>';
            })
            ->addColumn('actions', function ($data) {
                $actions = '';
                if (auth()->user()->can("admin-modulos-crud-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/crud-generator/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-modulos-crud-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                        url('admin/crud-generator/' . $data->id) . '\');" data-content="' .
                        trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                if (auth()->user()->can("admin-modulos-crud-update")) {
                    $actions .= '<button class="btn bg-purple btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/crud-generator/' . $data->id . '/fields') . '\';" data-content="' .
                        trans('crud-generator::modules/admin_lang.fields') . '"
                        data-placement="right" data-toggle="popover" style="margin-left: 3px;">
                        <i class="fa fa-archive" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-modulos-crud-create")) {
                    $actions .= '<button class="btn btn-success btn-sm" onclick="javascript:doGenerate(' .
                        $data->id . ')"
                    data-content="' .
                        trans('crud-generator::modules/admin_lang.generate') . '"
                        data-placement="right" data-toggle="popover" >
                        <i class="fa fa-cog" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-modulos-crud-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:doClean(' . $data->id . ')"
                    data-content="' .
                        trans('crud-generator::modules/admin_lang.clean') . '"
                        data-placement="right" data-toggle="popover" >
                        <i class="fa fa-eraser" aria-hidden="true"></i></button> ';
                }

                return $actions;
            })
            ->removeColumn('id')
            ->rawColumns([
                'active',
                'actions'
            ])
            ->make();
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-modulos-crud-update')) {
            app()->abort(403);
        }

        $module = Module::find($id);

        if (!empty($module)) {
            $module->active = !$module->active;
            return $module->save() ? 1 : 0;
        }

        return 0;
    }

    private function saveData(Module $module, Request $request)
    {
        $module->title = $request->input("title", "");
        $module->model = $request->input("model", "");
        $module->model_plural = $request->input("model_plural", "");
        $module->table_name = $request->input("table_name", "");
        $module->active = $request->input("active", false);
        $module->icon = $request->input("icon", '');
        $module->has_soft_deletes = $request->input("has_soft_deletes", false);
        $module->has_api_crud = $request->input("has_api_crud", false);
        $module->has_api_crud_secure = $request->input("has_api_crud_secure", false);
        $module->has_create_form = $request->input("has_create_form", false);
        $module->has_edit_form = $request->input("has_edit_form", false);
        $module->has_show_form = $request->input("has_show_form", false);
        $module->has_delete_form = $request->input("has_delete_form", false);
        $module->has_exports = $request->input("has_exports", false);
        $module->has_fake_data = $request->input("has_fake_data", false);


        $module->order_by_field = (empty($request->input("order_by_field", ''))?
            'id':$request->input("order_by_field", ''));
        $module->order_direction = (empty($request->input("order_direction", ''))?
            'ASC':$request->input("order_direction", ''));
        $module->entries_page = (empty($request->input("entries_page", ''))?
            '10':$request->input("entries_page", ''));

        $module->save();

        // Segun si hemos marcado o no el tiene soft deletes añadimos o no el campo
        if ($module->fields->count() > 0) {
            $deletedAtField = $module->fields->where('column_name', 'deleted_at')->first();

            if ($module->hasSoftDeletes && empty($deletedAtField)) {
                // Lo creamos
                // Deleted At
                $field = new ModuleField();
                $field->order_list = 9992;
                $field->order_create = 9992;
                $field->crud_module_id = $module->id;
                $field->field_type_slug = 'datetime';
                $field->column_name = 'deleted_at';
                $field->column_title = 'Borrado el';
                $field->in_list = false;
                $field->in_create = false;
                $field->in_edit = false;
                $field->in_show = false;
                $field->is_required = true;
                $field->can_modify = false;
                $field->save();
            } elseif (!$module->hasSoftDeletes && !empty($deletedAtField)) {
                $deletedAtField->delete();
            }
        }
    }

    public function generate(Request $request)
    {
        $module = Module::find($request->input('module_id', 0));

        if (empty($module)) {
            app()->abort(500);
        }

        if ($module->fields()->count() == 0) {
            return [
                'status' => 'ko',
                'msg' => trans('crud-generator::modules/admin_lang.field_required')
            ];
        }


        $generator = new CrudGenerator();
        if (!$generator->generateAll($module, $request->all())) {
            return [
                'status' => 'ko',
                'msg' => trans('crud-generator::modules/admin_lang.generate_ko')
            ];
        };

        return [
            'status' => 'ok',
            'msg' => trans('crud-generator::modules/admin_lang.generate_ok')
        ];
    }


    public function clean(Request $request, $id)
    {
        $module = Module::find($id);
        if (empty($module)) {
            app()->abort(500);
        }

        $generator = new CrudGenerator();
        if (!$generator->cleanAll($module)) {
            return [
                'status' => 'ko',
                'msg' => trans('crud-generator::modules/admin_lang.clean_ko')
            ];
        }

        return [
            'status' => 'ok',
            'msg' => trans('crud-generator::modules/admin_lang.clean_ok')
        ];
    }
}
