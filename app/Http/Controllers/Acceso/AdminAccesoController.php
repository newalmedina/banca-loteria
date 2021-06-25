<?php

namespace App\Http\Controllers\Acceso;

use Carbon\Carbon;
use App\Models\LogAccessFailed;


use App\Helpers\Clavel\ExcelHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class AdminAccesoController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa fa-key" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-control-acceso';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-control-acceso-list')) {
            app()->abort(403);
        }

        $page_title = trans("acceso/lang.acceso");

        return view("modules.acceso.admin_index", compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getData()
    {
        $acceso = LogAccessFailed::select(
            'logaccess_failed.user_id',
            'users.email',
            'user_profiles.first_name',
            'user_profiles.last_name',
            'logaccess_failed.username',
            'logaccess_failed.ip_address',
            'logaccess_failed.event',
            'logaccess_failed.password',
            'logaccess_failed.created_at'
        )
            ->leftJoin('users', 'users.id', '=', 'logaccess_failed.user_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id');

        return Datatables::of($acceso)
            ->editColumn('userCheck', function ($data) {
                $respuesta = "<span class='fa fa-user fa-2x text-success'></span>";

                if ($data->user_id > 0) {
                    if (auth()->user()->can("admin-users-update") && auth()->user()->can("admin-users-read")) {
                        $respuesta = "<a href='" . url('admin/users/' . $data->user_id . '/edit') .
                            "'><span class='fa fa-user fa-2x text-success'></span></a>";
                    }
                } else {
                    $respuesta = "<span class='fa fa-user fa-2x text-danger'></span>";
                }

                return $respuesta;
            })
            ->editColumn('creado', function ($data) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('d/m/Y H:i');
            })
            ->rawColumns(['userCheck', 'creado'])
            ->make();
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
            ->setTitle(trans('acceso/lang.listado_accesos'))
            ->setSubject(trans('acceso/lang.listado_accesos'))
            ->setDescription(trans('acceso/lang.listado_accesos'))
            ->setKeywords(trans('acceso/lang.listado_accesos'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('acceso/lang.listado_accesos'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader(trans('acceso/lang.listado_accesos'));
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('acceso/lang.user_id'),
            trans('acceso/lang.username'),
            trans('acceso/lang.nombre'),
            trans('acceso/lang.apellidos'),
            trans('acceso/lang.email'),
            trans('acceso/lang.ip'),
            trans('acceso/lang.evento'),
            trans('acceso/lang.password_failed'),
            trans('acceso/lang.fecha_intento')
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');

        $row++;

        // Ahora los registros
        $accesos = LogAccessFailed::select(
            'logaccess_failed.user_id',
            'users.email',
            'user_profiles.first_name',
            'user_profiles.last_name',
            'logaccess_failed.username',
            'logaccess_failed.ip_address',
            'logaccess_failed.event',
            'logaccess_failed.password',
            'logaccess_failed.created_at'
        )
            ->orderBy('created_at', 'DESC')
            ->leftJoin('users', 'users.id', '=', 'logaccess_failed.user_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->get();

        foreach ($accesos as $key => $value) {
            $valores = array(
                $value->user_id,
                $value->username,
                $value->first_name,
                $value->last_name,
                $value->email,
                $value->ip_address,
                $value->event,
                $value->password,
                Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->format('d/m/Y H:i')
            );

            $j = 1;
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
        $file_name = trans('acceso/lang.listado_accesos') . "_" . Carbon::now()->format('YmdHis');
        $outPath = storage_path("app/exports/");
        if (!file_exists($outPath)) {
            mkdir($outPath, 0777, true);
        }
        $writer->save($outPath . $file_name . '.xlsx');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx' . '"');
        header('Cache-Control: max-age=0');


        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
