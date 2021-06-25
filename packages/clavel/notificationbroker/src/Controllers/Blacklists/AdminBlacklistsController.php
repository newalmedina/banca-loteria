<?php

namespace Clavel\NotificationBroker\Controllers\Blacklists;

use App\Http\Controllers\AdminController;
use Clavel\NotificationBroker\Models\Blacklist;
use Clavel\NotificationBroker\Requests\AdminBlacklistsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdminBlacklistsController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-eye-slash"></i>';

    public function __construct()
    {
        parent::__construct();
        $this->access_permission = 'admin-blacklists';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-blacklists-list')) {
            app()->abort(403);
        }

        $page_title = trans("notificationbroker::blacklists/admin_lang.blacklists");

        return view("notificationbroker::blacklists/admin_index", compact('page_title'))
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
        if (!auth()->user()->can('admin-blacklists-create')) {
            app()->abort(403);
        }

        $blacklist = new Blacklist();
        $form_data = array(
            'route' => array('admin.blacklists.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("notificationbroker::blacklists/admin_lang.nueva_blacklist");


        return view(
            'notificationbroker::blacklists/admin_edit',
            compact(
                'page_title',
                'blacklist',
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
    public function store(AdminBlacklistsRequest $request)
    {
        if (!auth()->user()->can('admin-blacklists-create')) {
            app()->abort(403);
        }

        $blacklist = new Blacklist();
        if (!$this->saveBlacklist($request, $blacklist)) {
            return redirect()->route('admin.blacklists.create')
                ->with('error', trans('notificationbroker::blacklists/admin_lang.save_ko'));
        }

        return redirect()->to('admin/blacklists/' . $blacklist->id . "/edit")
            ->with('success', trans('notificationbroker::blacklists/admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-blacklists-update')) {
            app()->abort(403);
        }

        $blacklist = Blacklist::find($id);
        if (empty($blacklist)) {
            app()->abort(404);
        }

        $form_data = array(
            'route' => array('admin.blacklists.update', $blacklist->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("notificationbroker::blacklists/admin_lang.editar_blacklist");


        return view(
            'notificationbroker::blacklists/admin_edit',
            compact(
                'page_title',
                'blacklist',
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
    public function update(AdminBlacklistsRequest $request, $id)
    {
        if (!auth()->user()->can('admin-blacklists-update')) {
            app()->abort(403);
        }

        $blacklist = Blacklist::find($id);
        if (empty($blacklist)) {
            app()->abort(404);
        }

        if (!$this->saveBlacklist($request, $blacklist)) {
            return redirect()->to('admin/blacklists/' . $blacklist->id . "/edit")
                ->with('error', trans('notificationbroker::blacklists/admin_lang.save_ko'));
        }

        return redirect()->to('admin/blacklists/' . $blacklist->id . "/edit")
            ->with('success', trans('notificationbroker::blacklists/admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-blacklists-delete')) {
            app()->abort(403);
        }

        $blacklist = Blacklist::find($id);
        if (empty($blacklist)) {
            app()->abort(404);
        }

        $blacklist->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("notificationbroker::blacklists/admin_lang.deleted"),
            'id' => $blacklist->id
        ));
    }

    public function getData()
    {
        $query = DB::table('notifications_broker_blacklist as c')
            ->select(
                array(
                    'c.id',
                    'c.to',
                    'c.slug'
                )
            );

        $table = Datatables::of($query);

        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->can("admin-blacklists-update")) {
                $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                    url('admin/blacklists/' . $data->id . '/edit') . '\';" data-content="' .
                    trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
            }
            if (auth()->user()->can("admin-blacklists-delete")) {
                $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                    url('admin/blacklists/' . $data->id) . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }

            return $actions;
        });


        $table->editColumn('slug', function ($row) {
            return $row->slug ? Blacklist::SLUG_SELECT[$row->slug] : '';
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions']);
        return $table->make();
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-blacklists-update')) {
            app()->abort(403);
        }

        $blacklist = Blacklist::find($id);

        if (!empty($blacklist)) {
            $blacklist->active = !$blacklist->active;
            return $blacklist->save() ? 1 : 0;
        }

        return 0;
    }

    private function saveBlacklist(Request $request, Blacklist $blacklist)
    {
        try {
            DB::beginTransaction();

            $blacklist->to = $request->input("to", "");
            $blacklist->slug = $request->input("slug", "");
            $blacklist->save();


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return true;
    }
}
