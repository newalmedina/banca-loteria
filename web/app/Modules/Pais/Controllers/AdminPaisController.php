<?php

namespace App\Modules\Pais\Controllers;

use App\Http\Controllers\AdminController;
use App\Models\Permission;
use App\Modules\Pais\Models\Pais;
use App\Helpers\Clavel\ExcelHelper;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


use App\Modules\Pais\Requests\AdminPaisRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class AdminPaisController extends AdminController
{
    protected $page_title_icon = '<i class="fa fas fa-globe-africa" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();
        $this->access_permission = 'admin-pais';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-pais-list')) {
            app()->abort(403);
        }

        $page_title = trans("Pais::pais/admin_lang.pais");

        return view("Pais::admin_index", compact('page_title'))
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
        if (!auth()->user()->can('admin-pais-create')) {
            app()->abort(403);
        }

        $pais = new Pais();
        $form_data = array(
            'route' => array('pais.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
            
        );
        $page_title = trans("Pais::pais/admin_lang.nueva_pais");

        

        

        return view(
            'Pais::admin_edit',
            compact(
                'page_title',
                'pais',
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
    public function store(AdminPaisRequest $request)
    {
        if (!auth()->user()->can('admin-pais-create')) {
            app()->abort(403);
        }

        $pais = new Pais();
        if (!$this->savePais($request, $pais)) {
            return redirect()->route('pais.create')
                ->with('error', trans('Pais::pais/admin_lang.save_ko'));
        }

        $saveReturn = $request->get('form_return', 0);
        if ($saveReturn == 1) {
            return redirect()->to('admin/pais/')
                ->with('success', trans('Pais::pais/admin_lang.save_ok'));
        }
        return redirect()->to('admin/pais/'.$pais->id."/edit")
            ->with('success', trans('Pais::pais/admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-pais-update')) {
            app()->abort(403);
        }

        $pais = Pais::find($id);
        if (empty($pais)) {
            app()->abort(404);
        }

        $form_data = array(
            'route' => array('pais.update', $pais->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
            
        );
        $page_title = trans("Pais::pais/admin_lang.editar_pais");

        

        

        return view(
            'Pais::admin_edit',
            compact(
                'page_title',
                'pais',
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
    public function update(AdminPaisRequest $request, $id)
    {
        if (!auth()->user()->can('admin-pais-update')) {
            app()->abort(403);
        }

        $pais = Pais::find($id);
        if (empty($pais)) {
            app()->abort(404);
        }

        if (!$this->savePais($request, $pais)) {
            return redirect()->to('admin/pais/'.$pais->id."/edit")
                ->with('error', trans('Pais::pais/admin_lang.save_ko'));
        }

        $saveReturn = $request->get('form_return', 0);

        if ($saveReturn == 1) {
            return redirect()->to('admin/pais/')
                ->with('success', trans('Pais::pais/admin_lang.save_ok'));
        }

        return redirect()->to('admin/pais/'.$pais->id."/edit")
            ->with('success', trans('Pais::pais/admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-pais-delete')) {
            app()->abort(403);
        }

        $pais = Pais::find($id);
        if (empty($pais)) {
            app()->abort(404);
        }

        $pais->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("Pais::pais/admin_lang.deleted"),
            'id' => $pais->id
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
        if (!auth()->user()->can('admin-pais-delete')) {
            app()->abort(403);
        }

        $ids = explode(",", $request->get("ids", ""));

        foreach ($ids as $key => $value) {
            $pais = Pais::find($value);
            if (!empty($pais)) {
                $pais->delete();
            }
        }

        return response()->json(array(
            'success' => true,
            'msg' => trans("Pais::pais/admin_lang.deleted_records")
        ));
    }

    public function getData()
    {
        $query = DB::table('pais as c')

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
                    (auth()->user()->can("admin-pais-update")?"onclick=\"javascript:changeStatus('".
                        url('admin/pais/state/'.$data->id)."');\"":"").'
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
            if (auth()->user()->can("admin-pais-update")) {
                $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/pais/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
            }
            if (auth()->user()->can("admin-pais-delete")) {
                $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\''.
                        url('admin/pais/'.$data->id).'\');" data-content="'.
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
        if (!auth()->user()->can('admin-pais-update')) {
            app()->abort(403);
        }

        $pais = Pais::find($id);

        if (!empty($pais)) {
            $pais -> active = !$pais -> active;
            return $pais -> save() ? 1 : 0 ;
        }

        return 0;
    }

    private function savePais(Request $request, Pais $pais)
    {
        try {
            DB::beginTransaction();

            $pais->active = $request->input("active", false);
            $pais->name = $request->input("name", "");
            $pais->description = $request->input("description", "");
            $pais->code = $request->input("code", "");
            $pais->save();

            

            

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return true;
    }

    public function viewImage($image)
    {
        $myServiceSPW = new StoragePathWork("pais");
        return $myServiceSPW->showFile($image, '/pais');
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
            ->setTitle(trans('Pais::pais/admin_lang.listado_data'))
            ->setSubject(trans('Pais::pais/admin_lang.listado_data'))
            ->setDescription(trans('Pais::pais/admin_lang.listado_data'))
            ->setKeywords(trans('Pais::pais/admin_lang.listado_data'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(substr(trans('Pais::pais/admin_lang.listado_data'), 0, 30));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader(trans('Pais::pais/admin_lang.listado_data'));
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        // Ponemos las cabeceras
        $cabeceras = array(
             trans('Pais::pais/admin_lang.fields.id'),
        trans('Pais::pais/admin_lang.fields.active'),
        trans('Pais::pais/admin_lang.fields.name'),
        trans('Pais::pais/admin_lang.fields.description'),
        trans('Pais::pais/admin_lang.fields.code'),
        trans('Pais::pais/admin_lang.fields.created_at'),
        trans('Pais::pais/admin_lang.fields.updated_at')
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
        $data = DB::table('pais')
        ->select(
            'pais.id',
            'pais.active',
            'pais.name',
            'pais.description',
            'pais.code',
            'pais.created_at',
            'pais.updated_at'
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
        $file_name = trans('Pais::pais/admin_lang.listado_data')."_".Carbon::now()->format('YmdHis');
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
