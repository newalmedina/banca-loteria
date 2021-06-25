<?php

namespace App\Modules\Poblacions\Controllers;

use App\Http\Controllers\AdminController;
use App\Models\Permission;
use App\Modules\Poblacions\Models\Poblacion;
use App\Modules\Pais\Models\Pais;
use App\Helpers\Clavel\ExcelHelper;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


use App\Modules\Poblacions\Requests\AdminPoblacionsRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class AdminPoblacionsController extends AdminController
{
    protected $page_title_icon = '<i class="fa far fa-flag" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();
        $this->access_permission = 'admin-poblacions';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-poblacions-list')) {
            app()->abort(403);
        }

        $page_title = trans("Poblacions::poblacions/admin_lang.poblacions");

        return view("Poblacions::admin_index", compact('page_title'))
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
        if (!auth()->user()->can('admin-poblacions-create')) {
            app()->abort(403);
        }

        $poblacion = new Poblacion();
        $form_data = array(
            'route' => array('poblacions.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
            
        );
        $page_title = trans("Poblacions::poblacions/admin_lang.nueva_poblacion");

        

        $pais = Pais::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view(
            'Poblacions::admin_edit',
            compact(
                'page_title',
                'poblacion',
                'form_data',
                'pais'
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
    public function store(AdminPoblacionsRequest $request)
    {
        if (!auth()->user()->can('admin-poblacions-create')) {
            app()->abort(403);
        }

        $poblacion = new Poblacion();
        if (!$this->savePoblacion($request, $poblacion)) {
            return redirect()->route('poblacions.create')
                ->with('error', trans('Poblacions::poblacions/admin_lang.save_ko'));
        }

        $saveReturn = $request->get('form_return', 0);
        if ($saveReturn == 1) {
            return redirect()->to('admin/poblacions/')
                ->with('success', trans('Poblacions::poblacions/admin_lang.save_ok'));
        }
        return redirect()->to('admin/poblacions/'.$poblacion->id."/edit")
            ->with('success', trans('Poblacions::poblacions/admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-poblacions-update')) {
            app()->abort(403);
        }

        $poblacion = Poblacion::find($id);
        if (empty($poblacion)) {
            app()->abort(404);
        }

        $form_data = array(
            'route' => array('poblacions.update', $poblacion->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
            
        );
        $page_title = trans("Poblacions::poblacions/admin_lang.editar_poblacion");

        

        $pais = Pais::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view(
            'Poblacions::admin_edit',
            compact(
                'page_title',
                'poblacion',
                'form_data',
                'pais'
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
    public function update(AdminPoblacionsRequest $request, $id)
    {
        if (!auth()->user()->can('admin-poblacions-update')) {
            app()->abort(403);
        }

        $poblacion = Poblacion::find($id);
        if (empty($poblacion)) {
            app()->abort(404);
        }

        if (!$this->savePoblacion($request, $poblacion)) {
            return redirect()->to('admin/poblacions/'.$poblacion->id."/edit")
                ->with('error', trans('Poblacions::poblacions/admin_lang.save_ko'));
        }

        $saveReturn = $request->get('form_return', 0);

        if ($saveReturn == 1) {
            return redirect()->to('admin/poblacions/')
                ->with('success', trans('Poblacions::poblacions/admin_lang.save_ok'));
        }

        return redirect()->to('admin/poblacions/'.$poblacion->id."/edit")
            ->with('success', trans('Poblacions::poblacions/admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-poblacions-delete')) {
            app()->abort(403);
        }

        $poblacion = Poblacion::find($id);
        if (empty($poblacion)) {
            app()->abort(404);
        }

        $poblacion->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("Poblacions::poblacions/admin_lang.deleted"),
            'id' => $poblacion->id
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
        if (!auth()->user()->can('admin-poblacions-delete')) {
            app()->abort(403);
        }

        $ids = explode(",", $request->get("ids", ""));

        foreach ($ids as $key => $value) {
            $poblacion = Poblacion::find($value);
            if (!empty($poblacion)) {
                $poblacion->delete();
            }
        }

        return response()->json(array(
            'success' => true,
            'msg' => trans("Poblacions::poblacions/admin_lang.deleted_records")
        ));
    }

    public function getData()
    {
        $query = DB::table('poblacions as c')

            ->select(
                array(
                    'c.id',
                'c.active',
                'c.name',
                'c.code'
                )
            )
            
            ;

        $table = Datatables::of($query);
        $table->editColumn('active', function ($data) {
            return '<button class="btn '.($data->active?"btn-success":"btn-danger").' btn-sm" '.
                    (auth()->user()->can("admin-poblacions-update")?"onclick=\"javascript:changeStatus('".
                        url('admin/poblacions/state/'.$data->id)."');\"":"").'
                        data-content="'.($data->active?
                        trans('general/admin_lang.descativa'):
                        trans('general/admin_lang.activa')).'"
                        data-placement="right" data-toggle="popover">
                        <i class="fa '.($data->active?"fa-eye":"fa-eye-slash").'" aria-hidden="true"></i>
                        </button>';
        });


        $table->editColumn('check', function ($row) {
            return '<input type="checkbox" name="selected_id[]" value="' . $row->id . '">';
        });

        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->can("admin-poblacions-update")) {
                $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/poblacions/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
            }
            if (auth()->user()->can("admin-poblacions-delete")) {
                $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\''.
                        url('admin/poblacions/'.$data->id).'\');" data-content="'.
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
        if (!auth()->user()->can('admin-poblacions-update')) {
            app()->abort(403);
        }

        $poblacion = Poblacion::find($id);

        if (!empty($poblacion)) {
            $poblacion -> active = !$poblacion -> active;
            return $poblacion -> save() ? 1 : 0 ;
        }

        return 0;
    }

    private function savePoblacion(Request $request, Poblacion $poblacion)
    {
        try {
            DB::beginTransaction();

            $poblacion->active = $request->input("active", false);
            $poblacion->name = $request->input("name", "");
            $poblacion->description = $request->input("description", "");
            $poblacion->code = $request->input("code", "");
            $poblacion->pais_id = $request->input("pais_id", null);
            $poblacion->save();

            

            

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return true;
    }

    public function viewImage($image)
    {
        $myServiceSPW = new StoragePathWork("poblacions");
        return $myServiceSPW->showFile($image, '/poblacions');
    }

    public function generateExcel()
    {
        ini_set('memory_limit', '300M');

        if (ob_get_contents()) {
            ob_end_clean();
        }
        set_time_limit(1000);

        $spreadsheet = new Spreadsheet();
        $spreadsheet
            ->getProperties()
            ->setCreator(config('app.name', ''))
            ->setCompany(config('app.name', ''))
            ->setLastModifiedBy(config('app.name', '')) // última vez modificado por
            ->setTitle(trans('Poblacions::poblacions/admin_lang.listado_data'))
            ->setSubject(trans('Poblacions::poblacions/admin_lang.listado_data'))
            ->setDescription(trans('Poblacions::poblacions/admin_lang.listado_data'))
            ->setKeywords(trans('Poblacions::poblacions/admin_lang.listado_data'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(substr(trans('Poblacions::poblacions/admin_lang.listado_data'), 0, 30));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader(trans('Poblacions::poblacions/admin_lang.listado_data'));
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        // Ponemos las cabeceras
        $cabeceras = array(
             trans('Poblacions::poblacions/admin_lang.fields.id'),
        trans('Poblacions::poblacions/admin_lang.fields.active'),
        trans('Poblacions::poblacions/admin_lang.fields.name'),
        trans('Poblacions::poblacions/admin_lang.fields.description'),
        trans('Poblacions::poblacions/admin_lang.fields.code'),
        trans('Poblacions::poblacions/admin_lang.fields.pais'),
        trans('Poblacions::poblacions/admin_lang.fields.created_at'),
        trans('Poblacions::poblacions/admin_lang.fields.updated_at')
        );

        $j=1;
        foreach ($cabeceras as $titulo) {
            $sheet->setCellValueByColumnAndRow($j++, $row, $titulo);
        }

        $columna_final = Coordinate::stringFromColumnIndex($j - 1);

        $sheet->getStyle('A'.$row.':'.$columna_final.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':'.$columna_final.$row)->getFont()->setSize(14);

        ExcelHelper::cellColor($sheet, 'A'.$row.':'.$columna_final.$row, 'ffc000');

        foreach (ExcelHelper::xrange('A', $columna_final) as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        $row++;

        // Ahora los registros
        $data = DB::table('poblacions')
        ->select(
            'poblacions.id',
            'poblacions.active',
            'poblacions.name',
            'poblacions.description',
            'poblacions.code',
            'poblacions.pais_id',
            'poblacions.created_at',
            'poblacions.updated_at'
        )
        ->orderBy('created_at', 'DESC')
        ->get();



        foreach ($data as $key => $value) {
            $valores = array(
               $value->id,
            $value->active,
            $value->name,
            $value->description,
            $value->code,
            $value->pais_id,
            $value->created_at,
            $value->updated_at
            );

            $j=1;
            foreach ($valores as $valor) {
                $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
            }
            $row++;
        }

        ExcelHelper::autoSizeCurrentRow($sheet);

        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);


        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $file_name = trans('Poblacions::poblacions/admin_lang.listado_data')."_".Carbon::now()->format('YmdHis');
        $outPath = storage_path("app/exports/");
        if (!file_exists($outPath)) {
            mkdir($outPath, 0777, true);
        }
        $writer->save($outPath.$file_name.'.xlsx');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name.'.xlsx' . '"');
        header('Cache-Control: max-age=0');


        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
