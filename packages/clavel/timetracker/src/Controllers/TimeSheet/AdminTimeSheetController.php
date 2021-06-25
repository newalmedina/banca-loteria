<?php

namespace Clavel\TimeTracker\Controllers\TimeSheet;

use App\Http\Controllers\AdminController;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Clavel\TimeTracker\Models\Activity;
use Clavel\TimeTracker\Models\TimeSheet;
use Clavel\TimeTracker\Models\Customer;
use Clavel\TimeTracker\Models\Project;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\Facades\DataTables;
use ExcelHelper;

class AdminTimeSheetController extends AdminTimeSheetController
{
    protected $page_title_icon = '<i class="fa  fa-clock-o" aria-hidden="true"></i>';
    private $proyectos;

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-timesheet';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('admin-timesheet-list')) {
            app()->abort(403);
        }

        $page_title = trans("timetracker::timesheet/admin_lang.title");
        $proyectoList = Project::actives()
            ->orderBy('name', 'ASC')
            ->get()
            ->pluck('name', 'id');

        return view('timetracker::timesheet.admin_index', compact('page_title'))
            ->with([
                'page_title_icon', $this->page_title_icon,
                'proyecto' => $this->proyectos,
                'proyectos' => $proyectoList
            ]);
    }

    public function getData()
    {
        $timesheet = TimeSheet::select(
            array(
                'timesheet.id',
                'timesheet.start_time',
                'timesheet.end_time',
                'timesheet.duration',
                'users.id as user_id',
                'user_profiles.first_name',
                'user_profiles.last_name',
                'customers.id as customer_id',
                'customers.name as customer_name',
                'projects.id as project_id',
                'projects.name as project_name',
                'activities.id as activity_id',
                'activities.name as activity_name'
            )
        )
            ->join('users', 'users.id', '=', 'timesheet.user_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('activities', 'activities.id', '=', 'timesheet.activity_id')
            ->join('projects', 'projects.id', '=', 'timesheet.project_id')
            ->join('customers', 'customers.id', '=', 'projects.customer_id')
            ->where('projects.slug_state', 'LIKE', 'en-curso');

        return Datatables::of($timesheet)
            ->addColumn('date_activity', function ($data) {
                $date_ini = Carbon::parse($data->start_time);
                if (!empty($data->end_time)) {
                    $date_fin = Carbon::parse($data->end_time);
                    if ($date_fin->startOfDay()->diffInDays($date_ini->startOfDay()) > 0) {
                        return $date_ini->format("d/m/Y") . "<br>" . $date_fin->format("d/m/Y");
                    }
                }
                return $date_ini->format("d/m/Y");
            })
            ->addColumn('hour_activity_end', function ($data) {
                if (!empty($data->end_time)) {
                    return Carbon::parse($data->end_time)->format("H:i");
                } else {
                    return "-";
                }
            })
            ->addColumn('hour_activity_start', function ($data) {
                return Carbon::parse($data->start_time)->format("H:i");
            })
            ->editColumn('duration', function ($data) {
                $date_ini = Carbon::parse($data->start_time);
                if (!empty($data->end_time)) {
                    $date_fin = Carbon::parse($data->end_time);
                } else {
                    $date_fin = Carbon::now();
                }
                $dd = $date_fin->diff($date_ini);
                if ($dd->d > 0) {
                    return $dd->format($date_fin->diffInDays($date_ini)
                        . " " . trans("timetracker::timesheet/admin_lang.dias") . " %H:%I");
                } else {
                    return $dd->format("%H:%I");
                }
            })
            ->addColumn('user_name', function ($data) {
                return '<a href="' . url('admin/users/' . $data->user_id) . '/edit">'
                    . '<span class="label label-success">'
                    . trim($data->first_name . " " . $data->last_name)
                    . '</span>'
                    . '</a>';
            })
            ->editColumn('project_name', function ($data) {
                return '<a href="' . url('admin/projects/' . $data->project_id) . '/edit">'
                    . '<span class="label label-warning">'
                    . $data->project_name
                    . '</span>'
                    . '</a>';
            })
            ->editColumn('activity_name', function ($data) {
                return '<a href="' . url('admin/activities/' . $data->activity_id) . '/edit">'
                    . '<span class="label label-info">'
                    . $data->activity_name
                    . '</span>'
                    . '</a>';
            })
            ->editColumn('customer_name', function ($data) {
                return '<a href="' . url('admin/customers/' . $data->customer_id) . '/edit">'
                    . '<span class="label label-primary">'
                    . $data->customer_name
                    . '</span>'
                    . '</a>';
            })
            ->addColumn('actions', function ($data) {
                $actions = '';
                if (auth()->user()->can("admin-timesheet-update") && empty($data->end_time)) {
                    $actions .= '<button class="btn btn-info btn-sm" onclick="javascript:changeStatus(\'' .
                        url('admin/timesheet/state/' . $data->id) . '\');" data-content="' .
                        trans('timetracker::timesheet/admin_lang.stop') . '"
                        data-placement="right" data-toggle="popover">
                        <i class="fa fa-stop"></i></button> ';
                }
                if (auth()->user()->can("admin-timesheet-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/timesheet/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '"
                        data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-timesheet-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                        url('admin/timesheet/' . $data->id) . '\');" data-content="' .
                        trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                return $actions;
            })
            ->removeColumn(
                'id',
                'customer_id',
                'project_id',
                'activity_id',
                'user_id',
                'first_name',
                'last_name',
                'start_time',
                'end_time'
            )
            ->rawColumns(['customer_name',
                'project_name',
                'activity_name',
                'user_name',
                'date_activity',
                'duration',
                'actions'])
            ->make();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('admin-timesheet-create')) {
            app()->abort(403);
        }

        $timesheet = new TimeSheet();
        $timesheet->start_time = Carbon::now();
        $form_data = array('route' => array('admin.timesheet.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal');
        $page_title = trans("timetracker::timesheet/admin_lang.new");

        $users_list = User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select(
                'users.id',
                DB::raw('CONCAT(user_profiles.first_name, \' \' ,user_profiles.last_name) as fullName')
            )
            ->pluck('fullName', 'id')->all();
        $customers_list = Customer::pluck('name', 'id')->all();
        $projects_list = [];

        $activities_list = Activity::pluck('name', 'id')->all();

        return view(
            'timetracker::timesheet.admin_edit',
            compact(
                'page_title',
                'timesheet',
                'form_data',
                'customers_list',
                'projects_list',
                'activities_list',
                'users_list'
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
    public function store(Request $request)
    {
        if (!Auth::user()->can('admin-timesheet-create')) {
            app()->abort(403);
        }

        $timesheet = new TimeSheet();
        $this->saveData($request, $timesheet);

        return redirect('admin/timesheet/' . $timesheet->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\TimeSheet $activity
     * @return \Illuminate\Http\Response
     */
    public function show(TimeSheet $activity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('admin-timesheet-update')) {
            app()->abort(403);
        }

        $timesheet = TimeSheet::find($id);
        $form_data = array('route' => array('admin.timesheet.update', $timesheet->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal');
        $page_title = trans("timetracker::timesheet/admin_lang.modify");

        $users_list = User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select(
                'users.id',
                DB::raw('CONCAT(user_profiles.first_name, \' \' ,user_profiles.last_name) as fullName')
            )
            ->pluck('fullName', 'id')->all();
        $customers_list = Customer::pluck('name', 'id')->all();
        $projects_list = [];
        $project = Project::where("id", $timesheet->project_id)->first();
        if (!empty($project)) {
            $customer = Customer::where("id", $project->customer_id)->first();
            if (!empty($customer)) {
                $timesheet->customer_id = $customer->id;
                $projects_list = Project::where("customer_id", $project->customer_id)->pluck('name', 'id')->all();
            }
        }

        $activities_list = Activity::pluck('name', 'id')->all();


        return view(
            'timetracker::timesheet.admin_edit',
            compact(
                'page_title',
                'timesheet',
                'form_data',
                'customers_list',
                'projects_list',
                'activities_list',
                'users_list'
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('admin-timesheet-update')) {
            app()->abort(403);
        }
        $timesheet = TimeSheet::find($id);
        if (empty($timesheet)) {
            abort(404);
        }
        $this->saveData($request, $timesheet);

        return redirect('admin/timesheet/' . $timesheet->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('admin-timesheet-delete')) {
            app()->abort(403);
        }

        $timesheet = TimeSheet::find($id);
        if (empty($timesheet)) {
            abort(404);
        }
        $timesheet->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("timetracker::timesheet/admin_lang.deleted"),
            'id' => $timesheet->id
        ));
    }


    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-timesheet-update')) {
            app()->abort(403);
        }

        $timesheet = TimeSheet::find($id);

        $startDate = Carbon::parse($timesheet->start_time);
        $endDate = Carbon::now();
        $duration = $endDate->diffInSeconds($startDate);

        $timesheet->end_time = $endDate;
        $timesheet->duration = $duration;

        return $timesheet->save() ? 1 : 0;
    }

    private function saveData(Request $request, TimeSheet $timesheet)
    {
        $user_id = $request->input("user_id", "");
        $this->save($request, $timesheet, $user_id);
    }

    public function generateExcel(Request $request)
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
            ->setLastModifiedBy(config('app.name', '')) // última vez modificado por
            ->setTitle(trans('timetracker::timesheet/admin_lang.list'))
            ->setSubject(trans('timetracker::timesheet/admin_lang.list'))
            ->setDescription(trans('timetracker::timesheet/admin_lang.list'))
            ->setKeywords(trans('timetracker::timesheet/admin_lang.list'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('timetracker::timesheet/admin_lang.list'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader(trans('timetracker::timesheet/admin_lang.list'));
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        // Cargamos los datos del proyecto
        $project_id = $request->input("proyecto_filtrado", 0);
        $project = Project::find($project_id);

        $dt = $request->get("fecha_filtrado", "");
        $dates = explode(' - ', $dt);

        try {
            $start_date = Carbon::createFromFormat('d/m/Y', $dates[0], "Europe/Madrid")->startOfDay();
            $end_date = Carbon::createFromFormat('d/m/Y', $dates[1], "Europe/Madrid")->startOfDay();
        } catch (\Exception $ex) {
            $start_date = Carbon::today()->subDays(7);
            $end_date = Carbon::today();
        }


        // Ponemos algunos títulos a modo de ejemplo
        $sheet->setCellValueByColumnAndRow(1, $row, trans('timetracker::timesheet/admin_lang.project'));
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        ExcelHelper::cellColor($sheet, 'A'.$row.':B'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, $project->name);
        $sheet->mergeCellsByColumnAndRow(3, $row, 7, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setSize(14);

        $row++;


        $styleHeaderInfo = array(
            'font' => array(
                'size' => 14,
            ),
            'alignment' => array('horizontal' =>  Alignment::HORIZONTAL_LEFT),
        );

        // Segunda linea de información
        $sheet->setCellValueByColumnAndRow(1, $row, trans('timetracker::timesheet/admin_lang.order_number'));
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);

        ExcelHelper::cellColor($sheet, 'A'.$row.':G'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, $project->order_number);
        $sheet->mergeCellsByColumnAndRow(3, $row, 7, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->applyFromArray($styleHeaderInfo);

        $row++;

        // 3 linea de información
        $sheet->setCellValueByColumnAndRow(1, $row, trans('timetracker::timesheet/admin_lang.customer_number'));
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);

        ExcelHelper::cellColor($sheet, 'A'.$row.':G'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, $project->customer_number);
        $sheet->mergeCellsByColumnAndRow(3, $row, 7, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->applyFromArray($styleHeaderInfo);

        $row++;

        if (!empty($project->work_hours)) {
            // 4 linea de información
            $sheet->setCellValueByColumnAndRow(1, $row, trans('timetracker::timesheet/admin_lang.work_hours'));
            $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
            $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);

            ExcelHelper::cellColor($sheet, 'A'.$row.':G'.$row, '00b050');

            $sheet->setCellValueByColumnAndRow(3, $row, $project->work_hours);
            $sheet->mergeCellsByColumnAndRow(3, $row, 7, $row);
            ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

            $sheet->getStyle('A'.$row.':D'.$row)->applyFromArray($styleHeaderInfo);

            $row++;
        }

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('timetracker::timesheet/admin_lang.customer'),
            trans('timetracker::timesheet/admin_lang.project'),
            trans('timetracker::timesheet/admin_lang.activity'),
            trans('timetracker::timesheet/admin_lang.user_name'),
            trans('timetracker::timesheet/admin_lang.start_time'),
            trans('timetracker::timesheet/admin_lang.end_time'),
            trans('timetracker::timesheet/admin_lang.duration'),
            trans('timetracker::timesheet/admin_lang.description'),
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');

        $row++;

        // Ahora los registros

        $timesheet = TimeSheet::select(
            array(
                'timesheet.id',
                'timesheet.start_time',
                'timesheet.end_time',
                'timesheet.duration',
                'timesheet.description',
                'users.id as user_id',
                'user_profiles.first_name',
                'user_profiles.last_name',
                'customers.id as customer_id',
                'customers.name as customer_name',
                'projects.id as project_id',
                'projects.name as project_name',
                'activities.id as activity_id',
                'activities.name as activity_name'
            )
        )
            ->join('users', 'users.id', '=', 'timesheet.user_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('activities', 'activities.id', '=', 'timesheet.activity_id')
            ->join('projects', 'projects.id', '=', 'timesheet.project_id')
            ->join('customers', 'customers.id', '=', 'projects.customer_id')
            ->where('timesheet.project_id', $project->id)
            ->where('timesheet.start_time', '>=', $start_date)
            ->where('timesheet.end_time', '<=', $end_date)
            ->orderBy('start_time', 'ASC')
            ->get();

        $tiempoTotal=0;

        foreach ($timesheet as $key => $value) {
            $tiempoTotal+=$value->duration;
            $date_ini = Carbon::parse($value->start_time);
            if (!empty($value->end_time)) {
                $date_fin = Carbon::parse($value->end_time);
            } else {
                $date_fin = Carbon::now();
            }
            $dd = $date_fin->diff($date_ini);
            if ($dd->d > 0) {
                $duration = $dd->format($date_fin->diffInDays($date_ini)
                    . " " . trans("timetracker::timesheet/admin_lang.dias") . " %H:%I");
            } else {
                $duration = $dd->format("%H:%I");
            }

            $date_ini = Carbon::parse($value->start_time)->format("d/m/Y H:i");
            $date_end = '';

            if (!empty($value->end_time)) {
                $date_end = Carbon::parse($value->end_time)->format("d/m/Y H:i");
            }

            $username = trim($value->first_name . " " . $value->last_name);

            $valores = array(
                $value->customer_name,
                $value->project_name,
                $value->activity_name,
                $username,
                $date_ini,
                $date_end,
                $duration,
                $value->description,
            );

            $j = 1;

            foreach ($valores as $valor) {
                $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
            }

            $row++;
        }


        $row++;

        $sheet->setCellValueByColumnAndRow(6, $row, "Tiempo total: ");
        $sheet->setCellValueByColumnAndRow(
            7,
            $row,
            $this->segAHms($tiempoTotal)
        );
        $sheet->setCellValueByColumnAndRow(
            8,
            $row,
            $this->segADhms($tiempoTotal)
        );

        //Styles
        $style = array(
            'alignment' => array('horizontal' =>  Alignment::HORIZONTAL_RIGHT),
            'font' => array(
                'bold' => true,
                'size' => 14,
                'name' => 'Verdana',
                'color' => array('argb' => 'FF666666'),
            ),
            'borders' => array(
                'top' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['argb' => 'FF000000']
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['argb' => 'FF000000']
                ]
                /*,
                'left' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF0000FF']
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF0000FF']
                ]*/
            ),
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF' . '92d050',
                ]
            ],
        );
        $sheet
            ->getStyle('G' . $row)
            ->applyFromArray($style);


        $sheet->getStyle('F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row)->getFont()->setSize(12);

        ExcelHelper::autoSizeCurrentRow($sheet);


        /*$sheet->getStyle('H' . $row)->getFont()->setBold(true);
        $sheet->getStyle('H' . $row)->getFont()->setSize(14);
        ExcelHelper::cellColor($sheet, 'H' . $row, '00b050');*/
        $row++;

        // Ponemos la diferencia en horas si hay
        if (!empty($project->work_hours)) {
            $segundosContratados = $project->work_hours * 60 * 60;
            $tiempoRestante = $segundosContratados-$tiempoTotal;
            $tiempoRestanteAbs = abs($tiempoRestante);
            $sheet->setCellValueByColumnAndRow(6, $row, "Tiempo restante: ");
            $sheet->setCellValueByColumnAndRow(
                7,
                $row,
                $this->segAHms($tiempoRestanteAbs)
            );
            $sheet->setCellValueByColumnAndRow(
                8,
                $row,
                $this->segADhms($tiempoRestanteAbs)
            );

            $color = '92d050';
            if ($tiempoRestante<0) {
                $color = 'fa4969';
            }

            $style = array(
                'font' => array(
                    'bold' => true,
                    'size' => 14,
                    'name' => 'Verdana',
                    'color' => array('argb' => 'FF666666'),
                ),
                'alignment' => array('horizontal' =>  Alignment::HORIZONTAL_RIGHT),
                'borders' => array(
                    'top' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['argb' => 'FF000000']
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['argb' => 'FF000000']
                    ]
                    /*,
                    'left' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FF0000FF']
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF0000FF']
                    ]*/
                ),
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FF' . $color,
                    ]
                ],
            );
            $sheet
                ->getStyle('G' . $row)
                ->applyFromArray($style);


            $sheet->getStyle('F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('F' . $row)->getFont()->setSize(12);


            foreach (range('A', 'AB') as $columnID) {
                $sheet->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }
            $row++;
        }



        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);


        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $file_name = trans('timetracker::timesheet/admin_lang.list') . "_" . Carbon::now()->format('YmdHis');
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

    private function segADhms($seg)
    {
        $d = floor($seg / 86400);
        $h = floor(($seg - ($d * 86400)) / 3600);
        $m = floor(($seg - ($d * 86400) - ($h * 3600)) / 60);
        $s = $seg % 60;
        return sprintf("Días: %03d, horas: %02d, minutos: %02d, segundos:%02d", $d, $h, $m, $s);
    }
    private function segAHms($seg)
    {
        $d = floor($seg / 86400);
        $h = floor(($seg - ($d * 86400)) / 3600);
        $m = floor(($seg - ($d * 86400) - ($h * 3600)) / 60);
        $s = $seg % 60;

        $hTotal = $h + $d*24;
        return sprintf("%02d:%02d", $hTotal, $m);
    }
}
