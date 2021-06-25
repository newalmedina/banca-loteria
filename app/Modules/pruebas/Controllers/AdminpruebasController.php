<?php

namespace App\Modules\pruebas\Controllers;

use App\Http\Controllers\AdminController;
use App\Models\Permission;
use App\Modules\pruebas\Models\prueba;


use App\Modules\pruebas\Requests\AdminpruebasRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class AdminpruebasController extends AdminController
{
    protected $page_title_icon = '';

    public function __construct()
    {
        parent::__construct();
        $this->access_permission = 'admin-pruebas';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-pruebas-list')) {
            app()->abort(403);
        }

        $page_title = trans("pruebas::pruebas/admin_lang.pruebas");

        return view("pruebas::admin_index", compact('page_title'))
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
        if (!auth()->user()->can('admin-pruebas-create')) {
            app()->abort(403);
        }

        $prueba = new prueba();
        $form_data = array(
            'route' => array('pruebas.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
            
        );
        $page_title = trans("pruebas::pruebas/admin_lang.nueva_prueba");

        

        

        return view(
            'pruebas::admin_edit',
            compact(
                'page_title',
                'prueba',
                'form_data'
                
                
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminpruebasRequest $request)
    {
        if (!auth()->user()->can('admin-pruebas-create')) {
            app()->abort(403);
        }

        $prueba = new prueba();
        if(!$this->saveprueba($request, $prueba)) {

            return redirect()->route('pruebas.create')
                ->with('error', trans('pruebas::pruebas/admin_lang.save_ko'));
        }

        $saveReturn = $request->get('form_return', 0);
        if($saveReturn == 1){
            return redirect()->to('admin/pruebas/')
                ->with('success', trans('pruebas::pruebas/admin_lang.save_ok'));
        }
        return redirect()->to('admin/pruebas/'.$prueba->id."/edit")
            ->with('success', trans('pruebas::pruebas/admin_lang.save_ok'));
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
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-pruebas-update')) {
            app()->abort(403);
        }

        $prueba = prueba::find($id);
        if(empty($prueba)) {
            app()->abort(404);
        }

        $form_data = array(
            'route' => array('pruebas.update', $prueba->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
            
        );
        $page_title = trans("pruebas::pruebas/admin_lang.editar_prueba");

        

        

        return view(
            'pruebas::admin_edit',
            compact(
                'page_title',
                'prueba',
                'form_data'
                
                
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminpruebasRequest $request, $id)
    {
        if (!auth()->user()->can('admin-pruebas-update')) {
            app()->abort(403);
        }

        $prueba = prueba::find($id);
        if (empty($prueba)) {
            app()->abort(404);
        }

        if(!$this->saveprueba($request, $prueba)) {
            return redirect()->to('admin/pruebas/'.$prueba->id."/edit")
                ->with('error', trans('pruebas::pruebas/admin_lang.save_ko'));
        }

        $saveReturn = $request->get('form_return', 0);

        if($saveReturn == 1){
            return redirect()->to('admin/pruebas/')
                ->with('success', trans('pruebas::pruebas/admin_lang.save_ok'));
        }

        return redirect()->to('admin/pruebas/'.$prueba->id."/edit")
            ->with('success', trans('pruebas::pruebas/admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-pruebas-delete')) {
            app()->abort(403);
        }

        $prueba = prueba::find($id);
        if (empty($prueba)) {
            app()->abort(404);
        }

        $prueba->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("pruebas::pruebas/admin_lang.deleted"),
            'id' => $prueba->id
        ));
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroySelected(Request $request)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-pruebas-delete')) {
            app()->abort(403);
        }

        $ids = explode(",", $request->get("ids", ""));

        foreach ($ids as $key => $value) {
            $prueba = prueba::find($value);
            if (!empty($prueba)) {
                $prueba->delete();
            }
        }

        return response()->json(array(
            'success' => true,
            'msg' => trans("pruebas::pruebas/admin_lang.deleted_records")
        ));
    }

    public function getData()
    {
                $query = DB::table('pruebas as c')

            ->select(
                array(
                    'c.id',
'c.active',
'c.name'
                )
            )
            
            ;

       $table = Datatables::of($query);
                        $table->editColumn('active', function ($data) {
                return '<button class="btn '.($data->active?"btn-success":"btn-danger").' btn-sm" '.
                    (auth()->user()->can("admin-pruebas-update")?"onclick=\"javascript:changeStatus('".
                        url('admin/pruebas/state/'.$data->id)."');\"":"").'
                        data-content="'.($data->active?
                        trans('general/admin_lang.descativa'):
                        trans('general/admin_lang.activa')).'"
                        data-placement="right" data-toggle="popover">
                        <i class="fa '.($data->active?"fa-eye":"fa-eye-slash").'" aria-hidden="true"></i>
                        </button>';
            });


        $table->editColumn('check', function($row) {
            return '<input type="checkbox" name="selected_id[]" value="' . $row->id . '">';
        });

       $table->editColumn('actions', function ($data) {
                $actions = '';
                if (auth()->user()->can("admin-pruebas-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/pruebas/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-pruebas-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\''.
                        url('admin/pruebas/'.$data->id).'\');" data-content="'.
                        trans('general/admin_lang.borrar').'" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }

                return $actions;
            });

            
            $table->removeColumn('id');
            $table->rawColumns(['check','active', 'actions']);
            return $table->make();
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-pruebas-update')) {
            app()->abort(403);
        }

        $prueba = prueba::find($id);

        if (!empty($prueba)) {
            $prueba -> active = !$prueba -> active;
            return $prueba -> save() ? 1 : 0 ;
        }

        return 0;
    }

    private function saveprueba(Request $request, prueba $prueba)
    {
        try {
            DB::beginTransaction();

            $prueba->active = $request->input("active", false);$prueba->name = $request->input("name", "");$prueba->description = $request->input("description", "");
            $prueba->save();

            

            

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return true;
    }

    public function viewImage($image)
    {
        $myServiceSPW = new StoragePathWork("pruebas");
        return $myServiceSPW->showFile($image, '/pruebas');
    }

    

}
