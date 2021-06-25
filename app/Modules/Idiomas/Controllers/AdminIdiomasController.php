<?php

namespace App\Modules\Idiomas\Controllers;

use App\Models\Idioma;
use Illuminate\Http\Request;
use App\Models\IdiomaTranslation;
use App\Services\LanguageService;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\AdminController;
use App\Modules\Idiomas\Requests\AdminIdiomasRequest;

class AdminIdiomasController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-language" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();
        $this->access_permission = 'admin-idiomas';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-idiomas-list')) {
            app()->abort(403);
        }

        $page_title = trans("Idiomas::idiomas/admin_lang.idiomas");

        return view("Idiomas::admin_index", compact('page_title'))
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
        if (!auth()->user()->can('admin-idiomas-create')) {
            app()->abort(403);
        }

        $idioma = new Idioma();
        $form_data = array(
            'route' => array('idiomas.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Idiomas::idiomas/admin_lang.nueva_idioma");

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($idioma);



        return view(
            'Idiomas::admin_edit',
            compact(
                'page_title',
                'idioma',
                'form_data',
                'a_trans'
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
    public function store(AdminIdiomasRequest $request)
    {
        if (!auth()->user()->can('admin-idiomas-create')) {
            app()->abort(403);
        }

        $idioma = new Idioma();
        if (!$this->saveIdioma($request, $idioma)) {
            return redirect()->route('idiomas.create')
                ->with('error', trans('Idiomas::idiomas/admin_lang.save_ko'));
        }

        $saveReturn = $request->get('form_return', 0);
        if ($saveReturn == 1) {
            return redirect()->to('admin/idiomas/')
                ->with('success', trans('Idiomas::idiomas/admin_lang.save_ok'));
        }
        return redirect()->to('admin/idiomas/'.$idioma->id."/edit")
            ->with('success', trans('Idiomas::idiomas/admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-idiomas-update')) {
            app()->abort(403);
        }

        $idioma = Idioma::find($id);
        if (empty($idioma)) {
            app()->abort(404);
        }

        $form_data = array(
            'route' => array('idiomas.update', $idioma->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Idiomas::idiomas/admin_lang.editar_idioma");

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($idioma);



        return view(
            'Idiomas::admin_edit',
            compact(
                'page_title',
                'idioma',
                'form_data',
                'a_trans'
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
    public function update(AdminIdiomasRequest $request, $id)
    {
        if (!auth()->user()->can('admin-idiomas-update')) {
            app()->abort(403);
        }

        $idioma = Idioma::find($id);
        if (empty($idioma)) {
            app()->abort(404);
        }

        if (!$this->saveIdioma($request, $idioma)) {
            return redirect()->to('admin/idiomas/'.$idioma->id."/edit")
                ->with('error', trans('Idiomas::idiomas/admin_lang.save_ko'));
        }

        $saveReturn = $request->get('form_return', 0);

        if ($saveReturn == 1) {
            return redirect()->to('admin/idiomas/')
                ->with('success', trans('Idiomas::idiomas/admin_lang.save_ok'));
        }

        return redirect()->to('admin/idiomas/'.$idioma->id."/edit")
            ->with('success', trans('Idiomas::idiomas/admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-idiomas-delete')) {
            app()->abort(403);
        }

        $idioma = Idioma::find($id);
        if (empty($idioma)) {
            app()->abort(404);
        }

        $idioma->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("Idiomas::idiomas/admin_lang.deleted"),
            'id' => $idioma->id
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
        if (!auth()->user()->can('admin-idiomas-delete')) {
            app()->abort(403);
        }

        $ids = explode(",", $request->get("ids", ""));

        foreach ($ids as $key => $value) {
            $idioma = Idioma::find($value);
            if (!empty($idioma)) {
                $idioma->delete();
            }
        }

        return response()->json(array(
            'success' => true,
            'msg' => trans("Idiomas::idiomas/admin_lang.deleted_records")
        ));
    }

    public function getData()
    {
        $locale = app()->getLocale();
        $query = DB::table('idiomas as c')
            ->join('idioma_translations as ct', function ($join) use ($locale) {
                $join->on('ct.idioma_id', '=', 'c.id');
                $join->on('ct.locale', '=', DB::raw("'".$locale."'"));
            })
            ->select(
                array(
                    'c.id',
                'c.active',
                'c.code',
                'ct.name',
                'c.default'
                )
            )

            ;

        $table = Datatables::of($query);
        $table->editColumn('active', function ($data) {
            return '<button class="btn '.($data->active?"btn-success":"btn-danger").' btn-sm" '.
                    (auth()->user()->can("admin-idiomas-update")?"onclick=\"javascript:changeStatus('".
                        url('admin/idiomas/state/'.$data->id)."');\"":"").'
                        data-content="'.($data->active?
                        trans('general/admin_lang.descativa'):
                        trans('general/admin_lang.activa')).'"
                        data-placement="right" data-toggle="popover">
                        <i class="fa '.($data->active?"fa-eye":"fa-eye-slash").'" aria-hidden="true"></i>
                        </button>';
        });

        $table->editColumn('default', function ($data) {
            return '<button class="btn '.($data->default?"btn-success":"btn-danger").' btn-sm" '.
                    (auth()->user()->can("admin-idiomas-update")?"onclick=\"javascript:setDefault('".
                        url('admin/idiomas/default/'.$data->id)."');\"":"").'
                        data-content="'.($data->default?
                        trans('general/admin_lang.descativa'):
                        trans('general/admin_lang.activa')).'"
                        data-placement="right" data-toggle="popover">
                        <i class="fa fa-lightbulb-o aria-hidden="true"></i>
                        </button>';
        });


        $table->editColumn('check', function ($row) {
            return '<input type="checkbox" name="selected_id[]" value="' . $row->id . '">';
        });

        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->can("admin-idiomas-update")) {
                $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/idiomas/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
            }
            if (auth()->user()->can("admin-idiomas-delete")) {
                $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\''.
                        url('admin/idiomas/'.$data->id).'\');" data-content="'.
                        trans('general/admin_lang.borrar').'" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }

            return $actions;
        });


        $table->removeColumn('id');
        $table->rawColumns(['check','active', 'default', 'actions']);
        return $table->make();
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-idiomas-update')) {
            app()->abort(403);
        }

        $idioma = Idioma::find($id);

        if (!empty($idioma)) {
            $idioma -> active = !$idioma -> active;
            return $idioma -> save() ? 1 : 0 ;
        }

        return 0;
    }

    public function setDefaultState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-idiomas-update')) {
            app()->abort(403);
        }

        $idioma = Idioma::find($id);

        if (!empty($idioma)) {
            DB::table('idiomas')->update(array('default' => 0));
            $idioma -> default = true;
            return $idioma -> save() ? 1 : 0 ;
        }

        return 0;
    }

    private function saveIdioma(Request $request, Idioma $idioma)
    {
        try {
            DB::beginTransaction();

            // Si pasa a ser el idioma por defecto, desmarcamos el resto
            $default = $request->input("default", false);
            if ($default) {
                DB::table('idiomas')->update(array('default' => 0));
            }

            $idioma->active = $request->input("active", false);
            $idioma->code = $request->input("code", "");
            $idioma->default = $default;
            $idioma->save();


            foreach ($request->input('lang') as $key => $value) {
                $itemTrans = IdiomaTranslation::findOrNew(empty($value["id"])?0:$value["id"]);

                $itemTrans->idioma_id = $idioma->id;
                $itemTrans->locale = $key;
                $itemTrans->name = empty($value["name"])?"":$value["name"];
                $itemTrans->save();
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return true;
    }
}
