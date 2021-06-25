<?php

namespace Clavel\Elearning\Controllers\Codigos;

use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Services\StoragePathWork;
use Illuminate\Support\Facades\DB;
use App\Helpers\Clavel\ExcelHelper;
use Clavel\Elearning\Models\Codigo;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Clavel\Elearning\Models\Asignatura;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpSpreadsheet\Shared\Drawing;
use Clavel\Elearning\Requests\CodigosRequest;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Clavel\Elearning\Requests\CodigosMassiveRequest;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class AdminCodigosController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-cc" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-codigos';
    }

    public function index()
    {
        if (!Auth::user()->can('admin-codigos-list')) {
            abort(404);
        }

        $page_title = trans("elearning::codigos/admin_lang.codigos");

        return view('elearning::codigos.admin_index', compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getData()
    {
        $codigos = Codigo::select(
            array(
                'id',
                'active',
                'ilimitado',
                'codigo'
            )
        );

        return Datatables::of($codigos)
            ->editColumn(
                'active',
                '<div style="text-align: center;">
                    @if(Auth::user()->can("admin-codigos-update"))
                        @if($active)
                            <button class="btn btn-success btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/codigos/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.descativa') . '"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/codigos/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.activa') . '"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </button>
                        @endif
                    @else
                        @if($active)
                            <button class="btn btn-success btn-sm disabled" data-placement="right">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm disabled" data-placement="right">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </button>
                        @endif
                    @endif
                </div>'
            )
            ->editColumn(
                'ilimitado',
                '<div style="text-align: center;">
                    @if(Auth::user()->can("admin-codigos-update"))
                        @if($ilimitado)
                            <button class="btn btn-success btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/codigos/cambiar_ilimitado/\'.$id.\'\') }}\');"
                            data-content="' . trans('elearning::codigos/admin_lang.descativa') . '"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-check-circle" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/codigos/cambiar_ilimitado/\'.$id.\'\') }}\');"
                            data-content="' . trans('elearning::codigos/admin_lang.activa') . '"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-ban" aria-hidden="true"></i>
                            </button>
                        @endif
                    @else
                        @if($ilimitado)
                            <button class="btn btn-success btn-sm disabled" data-placement="right">
                                <i class="fa fa-check-circle" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm disabled" data-placement="right">
                                <i class="fa fa-ban" aria-hidden="true"></i>
                            </button>
                        @endif
                    @endif
                </div>'
            )
            ->addColumn('status', function ($row) {
                if ($row->users()->count() > 0 || $row->usuariosAsignatura()->count()) {
                    return '<span class="label label-success">' .
                        trans("elearning::codigos/admin_lang.codigo_en_uso") . '</span>';
                }
                return '<span class="label label-warning">' .
                    trans("elearning::codigos/admin_lang.codigo_sin_uso") . '</span>';
            })
            ->addColumn('actions', '
                @if(Auth::user()->can("admin-codigos-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/codigos/\'.$id.\'/edit\') }}\';"
                    data-content="' . trans('general/admin_lang.modificar') . '" data-placement="right"
                    data-toggle="popover"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-codigos-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/codigos/\'.$id.\'/destroy\') }}\');"
                    data-content="' . trans('general/admin_lang.borrar') . '" data-placement="left"
                    data-toggle="popover"><i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                <button class="btn btn-success btn-sm"
                onclick="javascript:showQrCode(\'{{ url(\'admin/codigos/\'.$id.\'/qrcode\') }}\');"
                data-content="' . trans('general/admin_lang.qrcode') . '" data-placement="right"
                data-toggle="popover"><i class="fa fa-qrcode"></i></button>
                ')
            ->removeColumn('id')
            ->rawColumns(['active', 'ilimitado', 'status', 'actions'])
            ->make();
    }

    public function create()
    {
        if (!Auth::user()->can('admin-codigos-create')) {
            abort(404);
        }
        $codigos = new Codigo();
        $form_data = array(
            'route' => array('admin.codigos.store'), 'method' => 'POST',
            'id' => 'formData', 'class' => 'form-horizontal'
        );
        $page_title = trans("elearning::codigos/admin_lang.nuevo_codigos");
        $roles = Role::get();
        $asignaturas = Asignatura::get();
        return view(
            'elearning::codigos.admin_edit',
            compact('page_title', 'codigos', 'form_data', 'roles', 'asignaturas')
        );
    }

    public function createMassive()
    {
        if (!Auth::user()->can('admin-codigos-create')) {
            abort(404);
        }
        $codigos = new Codigo();
        $form_data = array(
            'route' => array('admin.codigos.store_massive'),
            'method' => 'POST', 'id' => 'formData', 'class' => 'form-horizontal'
        );
        $page_title = trans("elearning::codigos/admin_lang.nuevo_codigo_masivo");
        $roles = Role::get();
        $asignaturas = Asignatura::get();
        return view(
            'elearning::codigos.admin_edit_massive',
            compact('page_title', 'codigos', 'form_data', 'roles', 'asignaturas')
        );
    }

    public function store(CodigosRequest $request)
    {
        if (!Auth::user()->can('admin-grupos-create')) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            $codigo = Codigo::create($request->except("_token", "multi", 'sel_roles', 'sel_asignaturas'));

            $this->saveRolyAsignatura($codigo, $request->input("sel_roles"), $request->input("sel_asignaturas"));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('admin/codigos')
                ->with('error', trans('general/admin_lang.saveko'));
        }

        return redirect('admin/codigos/' . $codigo->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function storeMassive(CodigosMassiveRequest $request)
    {
        if (!Auth::user()->can('admin-grupos-create')) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            $len = strlen($request->input("multi.n_final"));
            for ($nX = $request->input("multi.n_inicio"); $nX <= $request->input("multi.n_final"); $nX++) {
                $codeNum = str_pad($nX, $len, "0", STR_PAD_LEFT);
                $genCode = $request->input("multi.prefix") . $codeNum . $request->input("multi.sufijo");
                $request->merge(array(
                    'codigo' => $genCode
                ));

                $codigo = Codigo::create($request->except("_token", "multi", 'sel_roles', 'sel_asignaturas'));
                $this->saveRolyAsignatura($codigo, $request->input("sel_roles"), $request->input("sel_asignaturas"));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('admin/codigos/create_massive')
                ->with('error', trans('general/admin_lang.saveko'));
        }

        return redirect('admin/codigos')
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function edit($id)
    {
        if (!Auth::user()->can('admin-codigos-update')) {
            abort(404);
        }
        $codigos = Codigo::findOrFail($id);
        $form_data = array(
            'route' => array('admin.codigos.update', $codigos->id),
            'method' => 'PATCH', 'id' => 'formData', 'class' => 'form-horizontal'
        );
        $page_title = trans("elearning::codigos/admin_lang.modify_page");
        $roles = Role::get();
        $asignaturas = Asignatura::get();
        return view(
            'elearning::codigos.admin_edit',
            compact(
                'page_title',
                'codigos',
                'form_data',
                'roles',
                'asignaturas'
            )
        );
    }

    public function update(CodigosRequest $request, $id)
    {
        if (!Auth::user()->can('admin-grupos-update')) {
            abort(404);
        }

        $codigos = Codigo::findOrFail($id);
        if (empty($codigos)) {
            abort(404);
        }
        $codigos->update($request->except("_token", "multi", 'sel_roles', 'sel_asignaturas'));

        $this->saveRolyAsignatura($codigos, $request->input("sel_roles"), $request->input("sel_asignaturas"));

        return redirect('admin/codigos/' . $codigos->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    private function saveRolyAsignatura($codigo, $roles, $asignaturas)
    {
        $codigo->roles()->detach();
        if (!is_null($roles)) {
            $codigo->roles()->sync($roles);
        }

        $codigo->asignaturas()->detach();
        if (!is_null($asignaturas)) {
            $codigo->asignaturas()->sync($asignaturas);
        }
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('admin-codigos-delete')) {
            abort(404);
        }

        $codigo = Codigo::findOrFail($id);
        if (is_null($codigo)) {
            abort(404);
        }
        $codigo->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Código eliminada',
            'id' => $codigo->id
        ));
    }

    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-codigos-update')) {
            abort(404);
        }

        $codigo = Codigo::findOrFail($id);

        if (!is_null($codigo)) {
            $codigo->active = !$codigo->active;
            return $codigo->save() ? 1 : 0;
        }

        return 0;
    }

    public function setChangeIlimitado($id)
    {
        if (!Auth::user()->can('admin-codigos-update')) {
            abort(404);
        }

        $codigo = Codigo::findOrFail($id);

        if (!is_null($codigo)) {
            $codigo->ilimitado = !$codigo->ilimitado;
            return $codigo->save() ? 1 : 0;
        }

        return 0;
    }

    public function generateExcelPlantilla()
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
            ->setTitle(trans('elearning::codigos/admin_lang.template'))
            ->setSubject(trans('elearning::codigos/admin_lang.template'))
            ->setDescription(trans('elearning::codigos/admin_lang.template'))
            ->setKeywords(trans('elearning::codigos/admin_lang.template'))
            ->setCategory('template');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('elearning::codigos/admin_lang.template'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader(trans('elearning::codigos/admin_lang.template'));
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('elearning::codigos/admin_lang.codigo'),
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');
        $row++;

        // Ahora los registros

        $valores =  array(

            'Cod_1',
            'Cod_2',
            'Cod_3',
            'Cod_4',

        );

        foreach ($valores as $valor) {
            $sheet->setCellValueByColumnAndRow(1, $row, $valor);
            $row++;
        }


        ExcelHelper::autoSizeCurrentRow($sheet);

        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);


        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $file_name = trans('elearning::codigos/admin_lang.template') . "_" . Carbon::now()->format('YmdHis');
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

    public function generateExcelQrCode()
    {
        $this->generateExcel(true);
    }

    public function generateExcel($exportQr = false)
    {
        ini_set('memory_limit', '1000M');

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
            ->setTitle(trans('elearning::codigos/admin_lang.listado_codigos'))
            ->setSubject(trans('elearning::codigos/admin_lang.listado_codigos'))
            ->setDescription(trans('elearning::codigos/admin_lang.listado_codigos'))
            ->setKeywords(trans('elearning::codigos/admin_lang.listado_codigos'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('elearning::codigos/admin_lang.listado_codigos'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader(trans('elearning::codigos/admin_lang.listado_codigos'));
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('elearning::codigos/admin_lang.identificador'),
            trans('elearning::codigos/admin_lang.codigo'),
            trans('elearning::codigos/admin_lang.active'),
            trans('elearning::codigos/admin_lang.ilimitado'),
            trans('elearning::codigos/admin_lang.status'),
            trans('elearning::codigos/admin_lang.nombre_usuario'),
            trans('elearning::codigos/admin_lang.apellidos_usuario'),
            trans('elearning::codigos/admin_lang.email')

        );

        if ($exportQr) {
            $cabeceras[] = trans('elearning::codigos/admin_lang.qrcode');
        }

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');
        $row++;

        // Ahora los registros
        $codigos = Codigo::orderBy("id")->get();

        foreach ($codigos as $key => $value) {
            // Vemos si un codigo lo tiene mas de un usaurio
            $numUsuarios = $value->users()->count();
            $numUsuariosAsignatura = $value->usuariosAsignatura()->count();
            if (($numUsuarios > 0) || $numUsuariosAsignatura > 0) {
                $nLoop = 1;
                if ($numUsuarios > 0) {
                    foreach ($value->users as $key2 => $users) {
                        if ($nLoop == 1) {
                            $valores = array(
                                $value->id,
                                $value->codigo,
                                ($value->active == '1') ?
                                    trans('general/admin_lang.yes') :
                                    trans('general/admin_lang.no'),
                                ($value->ilimitado == '1') ?
                                    trans('general/admin_lang.yes') :
                                    trans('general/admin_lang.no'),
                                trans("elearning::codigos/admin_lang.codigo_en_uso"),
                                $users->userProfile->first_name,
                                $users->userProfile->last_name,
                                $users->email
                            );
                        } else {
                            $valores = array(
                                null,
                                null,
                                null,
                                null,
                                null,
                                $users->userProfile->first_name,
                                $users->userProfile->last_name,
                                $users->email
                            );
                        }

                        $j = 1;
                        foreach ($valores as $valor) {
                            $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
                        }

                        if ($exportQr) {
                            $img = $this->getQRCodeImage($value->codigo);
                            $this->drawImage($spreadsheet, $sheet, $row, $j, $img);
                        }


                        $row++;
                        $nLoop++;
                    }
                }

                if ($numUsuariosAsignatura > 0) {
                    foreach ($value->usuariosAsignatura as $key2 => $usersAsignatura) {
                        if ($nLoop == 1) {
                            $valores = array(
                                $value->id,
                                $value->codigo,
                                ($value->active == '1') ?
                                    trans('general/admin_lang.yes') :
                                    trans('general/admin_lang.no'),
                                ($value->ilimitado == '1') ?
                                    trans('general/admin_lang.yes') :
                                    trans('general/admin_lang.no'),
                                trans("elearning::codigos/admin_lang.codigo_en_uso"),
                                $usersAsignatura->user->userProfile->first_name,
                                $usersAsignatura->user->userProfile->last_name,
                                $usersAsignatura->user->email
                            );
                        } else {
                            $valores = array(
                                null,
                                null,
                                null,
                                null,
                                null,
                                $usersAsignatura->user->userProfile->first_name,
                                $usersAsignatura->user->userProfile->last_name,
                                $usersAsignatura->user->email
                            );
                        }


                        $j = 1;
                        foreach ($valores as $valor) {
                            $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
                        }

                        if ($exportQr) {
                            $img = $this->getQRCodeImage($value->codigo);
                            $this->drawImage($spreadsheet, $sheet, $row, $j, $img);
                        }

                        $row++;
                        $nLoop++;
                    }
                }
            } else {
                $valores = array(
                    $value->id,
                    $value->codigo,
                    ($value->active == '1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no'),
                    ($value->ilimitado == '1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no'),
                    trans("elearning::codigos/admin_lang.codigo_sin_uso"),
                    null,
                    null,
                    null
                );

                $j = 1;
                foreach ($valores as $valor) {
                    $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
                }

                if ($exportQr) {
                    $img = $this->getQRCodeImage($value->codigo);
                    $this->drawImage($spreadsheet, $sheet, $row, $j, $img);
                }

                $row++;
            }
        }

        ExcelHelper::autoSizeCurrentRow($sheet);

        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);


        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $file_name = trans('elearning::codigos/admin_lang.listado_codigos') . "_" . Carbon::now()->format('YmdHis');
        $outPath = storage_path("app/exports/");
        if (!file_exists($outPath)) {
            mkdir($outPath, 0777, true);
        }
        $writer->save($outPath . $file_name . '.xlsx');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx' . '"');
        header('Cache-Control: max-age=0');

        /*
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        */

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function importCodigos(Request $request)
    {
        $myServiceSPW = new StoragePathWork("codigos");
        $file = $request->file('plantilla');
        $res = ["result" => false, "existentes" => []];

        if (!empty($file)) {
            $filename = $myServiceSPW->saveFile($file, '');

            $fileData = $myServiceSPW->getFile($filename, '');
            /**  Identify the type of $inputFileName  **/
            $inputFileType = IOFactory::identify($fileData);
            /**  Create a new Reader of the type that has been identified  **/
            $reader = IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            /**  Load $inputFileName to a Spreadsheet Object  **/
            $spreadsheet = $reader->load($fileData);

            $role_id = Role::where("name", "=", "usuario-front")->first()->id;

            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            if (!empty($sheetData) && sizeof($sheetData) > 1) {
                for ($i = 1; $i < sizeof($sheetData); $i++) {
                    $line = $sheetData[$i];
                    if (!empty($line[0])) {
                        $codigo = Codigo::where("codigo", $line[0])->first();
                        if (empty($codigo)) {
                            $codigo = new Codigo();
                            $codigo->codigo = $line[0];
                            $codigo->ilimitado = false;
                            $codigo->active = true;
                            $codigo->save();
                            $codigo->roles()->attach($role_id);
                        } else {
                            $res["existentes"][] = $codigo->codigo;
                        }
                    }
                }
            }

            $res["result"] = true;

            $myServiceSPW->deleteFile($file, '');
        }

        return response()->json($res);
    }

    public function importCodigosOld(Request $request)
    {
        $myServiceSPW = new StoragePathWork("codigos");
        $file = $request->file('plantilla');
        $res = ["result" => false, "existentes" => []];

        if (!empty($file)) {
            $filename = $myServiceSPW->saveFile($file, '');
            Excel::load($myServiceSPW->getFile($filename, ''), function ($reader) use (&$res) {
                $result = $reader->toArray();
                try {
                    $role_id = Role::where("name", "=", "usuario-front")->first()->id;
                    foreach ($result as $line) {
                        if (!empty($line['codigos'])) {
                            $codigo = Codigo::where("codigo", $line["codigos"])->first();
                            if (empty($codigo)) {
                                $codigo = new Codigo();
                                $codigo->codigo = $line["codigos"];
                                $codigo->ilimitado = false;
                                $codigo->active = true;
                                $codigo->save();
                                $codigo->roles()->attach($role_id);
                            } else {
                                $res["existentes"][] = $codigo->codigo;
                            }
                        }
                    }
                    $res["result"] = true;
                } catch (\Exception $e) {
                    throw $e;
                }
            });

            $myServiceSPW->deleteFile($file, '');
        }

        return response()->json($res);
    }

    protected function drawImage($spreadsheet, $sheet, $row, $col, $img)
    {
        $drawing = new MemoryDrawing();
        $drawing->setName('QrCode');
        $drawing->setDescription('QrCode');
        $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getCoordinate();
        $drawing->setCoordinates($cellValue);

        $image_height = imageSY($img);
        $image_width  = imageSX($img);

        $image_height_pt = Drawing::pixelsToPoints($image_height);
        //$image_width_pt  = Drawing::pixelsToCellDimension($image_width,$default_font);

        $drawing->setHeight($image_height);
        $sheet->getRowDimension($row)->setRowHeight($image_height_pt);
        //$sheet->getColumnDimension($j)->setWidth($image_width_pt);

        $drawing->setImageResource($img);

        $drawing->setRenderingFunction(
            MemoryDrawing::RENDERING_PNG
        );
        $drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
    }

    protected function getQRCodeImage($codigo)
    {
        // Ahora la imagen
        $qrcode = QrCode::size(300)
            //->color(255,0,255)
            //->backgroundColor(255,255,0)
            //->margin(100)
            ->format('png')
            ->generate(url('usuarios/registro?codigo=' . $codigo));
        $gdImage = imagecreatefromstring($qrcode);

        $image_height = imageSY($gdImage);
        $image_width  = imageSX($gdImage);

        $percent = 10;

        $newwidth = $image_height * $percent;
        $newheight = $image_width * $percent;

        $img = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresized($img, $gdImage, 0, 0, 0, 0, $newwidth, $newheight, $image_width, $image_height);
        ob_start();
        imagepng($img);
        $image_data = ob_get_contents();
        imagedestroy($img);
        ob_end_clean();
        $imgFinal = imagecreatefromstring($image_data);

        return $imgFinal;
    }

    public function getQR($id)
    {
        $codigo = Codigo::findOrFail($id);

        if (empty($codigo)) {
            return;
        }

        return QrCode::size(300)
            //->color(255,0,255)
            //->backgroundColor(255,255,0)
            //->margin(100)
            ->generate(url('usuarios/registro?codigo=' . $codigo->codigo));
    }
}
