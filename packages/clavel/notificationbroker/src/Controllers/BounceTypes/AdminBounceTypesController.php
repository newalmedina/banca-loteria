<?php

namespace Clavel\NotificationBroker\Controllers\BounceTypes;

use App\Http\Controllers\AdminController;
use Clavel\NotificationBroker\Models\BounceType;
use Clavel\NotificationBroker\Requests\AdminBounceTypesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdminBounceTypesController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-bug" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();
        $this->access_permission = 'admin-bouncetypes';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-bouncetypes-list')) {
            app()->abort(403);
        }

        $page_title = trans("notificationbroker::bouncetypes/admin_lang.bouncetypes");

        return view("notificationbroker::bouncetypes/admin_index", compact('page_title'))
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
        if (!auth()->user()->can('admin-bouncetypes-create')) {
            app()->abort(403);
        }

        $bouncetype = new BounceType();
        $form_data = array(
            'route' => array('bouncetypes.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("notificationbroker::bouncetypes/admin_lang.nueva_bouncetype");


        return view(
            'notificationbroker::bouncetypes/admin_edit',
            compact(
                'page_title',
                'bouncetype',
                'form_data'
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminBounceTypesRequest $request)
    {
        if (!auth()->user()->can('admin-bouncetypes-create')) {
            app()->abort(403);
        }

        $bouncetype = new BounceType();
        if (!$this->saveBounceType($request, $bouncetype)) {
            return redirect()->route('bouncetypes.create')
                ->with('error', trans('notificationbroker::bouncetypes/admin_lang.save_ko'));
        }

        return redirect()->to('admin/bouncetypes/' . $bouncetype->id . "/edit")
            ->with('success', trans('notificationbroker::bouncetypes/admin_lang.save_ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-bouncetypes-update')) {
            app()->abort(403);
        }

        $bouncetype = BounceType::find($id);
        if (empty($bouncetype)) {
            app()->abort(404);
        }

        $form_data = array(
            'route' => array('bouncetypes.update', $bouncetype->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("notificationbroker::bouncetypes/admin_lang.editar_bouncetype");


        return view(
            'notificationbroker::bouncetypes/admin_edit',
            compact(
                'page_title',
                'bouncetype',
                'form_data'
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminBounceTypesRequest $request, $id)
    {
        if (!auth()->user()->can('admin-bouncetypes-update')) {
            app()->abort(403);
        }

        $bouncetype = BounceType::find($id);
        if (empty($bouncetype)) {
            app()->abort(404);
        }

        if (!$this->saveBounceType($request, $bouncetype)) {
            return redirect()->to('admin/bouncetypes/' . $bouncetype->id . "/edit")
                ->with('error', trans('notificationbroker::bouncetypes/admin_lang.save_ko'));
        }

        return redirect()->to('admin/bouncetypes/' . $bouncetype->id . "/edit")
            ->with('success', trans('notificationbroker::bouncetypes/admin_lang.save_ok'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-bouncetypes-delete')) {
            app()->abort(403);
        }

        $bouncetype = BounceType::find($id);
        if (empty($bouncetype)) {
            app()->abort(404);
        }

        $bouncetype->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("notificationbroker::bouncetypes/admin_lang.deleted"),
            'id' => $bouncetype->id
        ));
    }

    public function getData()
    {
        $query = DB::table('bouncetypes as c')
            ->select(
                array(
                    'c.id',
                    'c.active',
                    'c.name'
                )
            )
            ->whereNull('c.deleted_at');

        $table = Datatables::of($query);
        $table->editColumn('active', function ($data) {
            return '<button class="btn ' . ($data->active ? "btn-success" : "btn-danger") . ' btn-sm" ' .
                (auth()->user()->can("admin-bouncetypes-update") ? "onclick=\"javascript:changeStatus('" .
                    url('admin/bouncetypes/state/' . $data->id) . "');\"" : "") . '
                        data-content="' . ($data->active ?
                    trans('general/admin_lang.descativa') :
                    trans('general/admin_lang.activa')) . '"
                        data-placement="right" data-toggle="popover">
                        <i class="fa ' . ($data->active ? "fa-eye" : "fa-eye-slash") . '"></i>
                        </button>';
        });
        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->can("admin-bouncetypes-update")) {
                $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                    url('admin/bouncetypes/' . $data->id . '/edit') . '\';" data-content="' .
                    trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
            }
            /*
            if (auth()->user()->can("admin-bouncetypes-delete")) {
                $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                    url('admin/bouncetypes/' . $data->id) . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }
            */

            return $actions;
        });


        $table->removeColumn('id');
        $table->rawColumns(['active', 'actions']);
        return $table->make();
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-bouncetypes-update')) {
            app()->abort(403);
        }

        $bouncetype = BounceType::find($id);

        if (!empty($bouncetype)) {
            $bouncetype->active = !$bouncetype->active;
            return $bouncetype->save() ? 1 : 0;
        }

        return 0;
    }

    private function saveBounceType(Request $request, BounceType $bouncetype)
    {
        try {
            DB::beginTransaction();

            $bouncetype->active = $request->input("active", false);
            $bouncetype->name = $request->input("name", "");
            $bouncetype->description = $request->input("description", "");
            $bouncetype->save();


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return true;
    }
}
