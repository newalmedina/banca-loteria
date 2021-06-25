<?php

namespace Clavel\Basic\Controllers\Menu;

use Illuminate\Support\Str;
use Clavel\Basic\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\AdminController;
use Clavel\Basic\Requests\AdminMenuRequest;

class AdminMenuController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-bars" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-menu';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-menu-list')) {
            app()->abort(403);
        }

        $page_title = trans("basic::menu/admin_lang.menu");

        return view("basic::menu.admin_index", compact('page_title'))
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
        if (!auth()->user()->can('admin-menu-create')) {
            app()->abort(403);
        }

        $menu = new Menu();

        $form_data = array(
            'route' => array('menu.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );

        $page_title = trans("basic::menu/admin_lang.nuevo_menu");

        return view('basic::menu.admin_edit', compact('page_title', 'menu', 'form_data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminMenuRequest $request)
    {
        $menu = new Menu();

        // Obtenemos la data enviada por el usuario
        $menu->slug = Str::slug($request->input('name'));
        $menu->name = $request->input('name');
        $menu->primary = 0;

        try {
            DB::beginTransaction();

            $menu->save();

            DB::commit();

            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect()->route('menu.edit', array($menu->id))
                ->with('success', trans('basic::menu/admin_lang.okGuardado'));
        } catch (\PDOException $e) {
            DB::rollBack();
            return redirect()->route('menu.create')
                ->with('error', trans('basic::menu/admin_lang.errorediciion'));
        }
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
        // Si no tiene permisos para modificar o visualizar lo echamos
        if (!auth()->user()->can('admin-menu-update')) {
            app()->abort(403);
        }

        $menu = Menu::findOrNew($id);

        $form_data = array(
            'route' => array('menu.update', $menu->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );

        $page_title = trans("basic::menu/admin_lang.modify_menu");

        return view('basic::menu.admin_edit', compact('page_title', 'menu', 'form_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminMenuRequest $request, $id)
    {

        // Creamos un nuevo objeto para nuestro nuevo usuario
        $menu = Menu::find($id);

        // Si el rol no existe entonces lanzamos un error 404 :(
        if (is_null($menu)) {
            app()->abort(404);
        }

        // Obtenemos la data enviada por el usuario
        $menu->slug = Str::slug($request->input('name'));
        $menu->name = $request->input('name');

        try {
            DB::beginTransaction();

            $menu->save();

            DB::commit();

            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect()->route('menu.edit', array($menu->id))
                ->with('success', trans('basic::menu/admin_lang.okUpdate'));
        } catch (\PDOException $e) {
            DB::rollBack();
            return redirect()->route('menu.edit', array($menu->id))
                ->with('error', trans('basic::menu/admin_lang.errorediciion'));
        }
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
        if (!auth()->user()->can('admin-menu-delete')) {
            app()->abort(403);
        }

        $menu = Menu::find($id);

        if (is_null($menu)) {
            app()->abort(404);
        }

        $menu->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Menu eliminado',
            'id' => $menu->id
        ));
    }

    public function getData()
    {
        $menu = Menu::select(array('id', 'primary', 'name'));

        return Datatables::of($menu)
            ->editColumn(
                'primary',
                '@if($primary) <div style="text-align: center;"><i class="fa fa-check text-primary"></i></div> @endif'
            )
            ->addColumn('actions', '
                @if(auth()->user()->can("admin-menu-update"))
                    <button class="btn bg-purple btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/menu/structure/\'.$id.\'\') }}\';"
                    data-content="'.trans('basic::menu/admin_lang.structure').'"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-share-alt" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/menu/\'.$id.\'/edit\') }}\';"
                    data-content="'.trans('general/admin_lang.modificar').'"
                    data-placement="right"
                    data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                    </button>
                @endif
                @if(auth()->user()->can("admin-menu-delete"))
                    @if(!$primary)
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:deleteElement(\'{{ url(\'admin/menu/\'.$id.\'\') }}\');"
                        data-content="'.trans('general/admin_lang.borrar').'"
                        data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm disabled" data-placement="left">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    @endif
                @endif
                ')
            ->removeColumn('id')
            ->rawColumns(['primary', 'actions'])
            ->make();
    }
}
