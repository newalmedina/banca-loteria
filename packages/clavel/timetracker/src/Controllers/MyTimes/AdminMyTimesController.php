<?php

namespace Clavel\TimeTracker\Controllers\MyTimes;

use DateTime;
use ExcelHelper;
use Carbon\Carbon;
use App\Models\User;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Clavel\TimeTracker\Models\Project;
use Clavel\TimeTracker\Models\Activity;
use Clavel\TimeTracker\Models\Customer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Clavel\TimeTracker\Models\TimeSheet;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Clavel\TimeTracker\Requests\MyTimesRequest;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Clavel\TimeTracker\Controllers\TimeSheet\AdminTimeSheetController;

class AdminMyTimesController extends AdminTimeSheetController
{
    protected $page_title_icon = '<i class="fa fa-puzzle-piece" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-mytimes';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('admin-mytimes-list')) {
            app()->abort(403);
        }

        $last_projects = DB::select('select distinct customer_id, customer_name, ' .
            ' project_id, project_name, activity_id, ' .
            ' activity_name, color from (' .
            ' SELECT max(timesheet.start_time) AS max_fecha,customers.id AS customer_id,' .
            ' customers.name AS customer_name, projects.id AS project_id, projects.name AS project_name,' .
            ' activities.id AS activity_id, activities.name AS activity_name, projects.color' .
            ' FROM timesheet' .
            ' INNER JOIN activities ON activities.id = timesheet.activity_id' .
            ' INNER JOIN projects ON projects.id = timesheet.project_id' .
            ' INNER JOIN customers ON customers.id = projects.customer_id' .
            ' WHERE user_id = ?' .
            ' GROUP BY customers.id, customers.NAME, projects.id, projects.NAME, activities.id,' .
            ' activities.NAME,projects.color' .
            ' ORDER BY max_fecha DESC' .
            ' LIMIT 8' .
            ') as data' .
            ' ', [auth()->user()->id]);

        $page_title = trans("timetracker::mytimes/admin_lang.title");

        return view('timetracker::mytimes.admin_index', compact('page_title', 'last_projects'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getData()
    {
        $timesheet = TimeSheet::select(
            array(
                'timesheet.id',
                'timesheet.start_time',
                'timesheet.end_time',
                'timesheet.duration',
                'customers.id as customer_id',
                'customers.name as customer_name',
                'projects.id as project_id',
                'projects.name as project_name',
                'activities.id as activity_id',
                'activities.name as activity_name'
            )
        )
            ->join('activities', 'activities.id', '=', 'timesheet.activity_id')
            ->join('projects', 'projects.id', '=', 'timesheet.project_id')
            ->join('customers', 'customers.id', '=', 'projects.customer_id')
            ->where("user_id", auth()->user()->id);

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
            ->addColumn('hour_activity_start', function ($data) {
                return Carbon::parse($data->start_time)->format("H:i");
            })
            ->addColumn('hour_activity_end', function ($data) {
                if (!empty($data->end_time)) {
                    return Carbon::parse($data->end_time)->format("H:i");
                } else {
                    return "-";
                }
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
                        . " " . trans("timetracker::mytimes/admin_lang.dias") . " %H:%I");
                } else {
                    return $dd->format("%H:%I");
                }
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
            ->editColumn('project_name', function ($data) {
                return '<a href="' . url('admin/projects/' . $data->project_id) . '/edit">'
                    . '<span class="label label-warning">'
                    . $data->project_name
                    . '</span>'
                    . '</a>';
            })
            ->addColumn('actions', function ($data) {
                $actions = '';
                if (auth()->user()->can("admin-mytimes-update") && empty($data->end_time)) {
                    $actions .= '<button class="btn btn-info btn-sm" onclick="javascript:openDescription(\'' .
                        $data->id . '\');">'
                        . ' <i class="fa fa-stop"></i></button> ';
                } else {
                    $actions .= '<button class="btn btn-info btn-sm" onclick="javascript:restartTimeSheet(\'' .
                        url('admin/mytimes/restart/' . $data->id) . '\');" data-content="' .
                        trans('timetracker::mytimes/admin_lang.restart') . '"
                        data-placement="right" data-toggle="popover">
                        <i class="fa fa-repeat" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-mytimes-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/mytimes/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-mytimes-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                        url('admin/mytimes/' . $data->id) . '\');" data-content="' .
                        trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                return $actions;
            })
            ->removeColumn('id', 'customer_id', 'project_id', 'activity_id', 'start_time', 'end_time')
            ->rawColumns(['customer_name', 'project_name', 'activity_name', 'date_activity', 'duration', 'actions'])
            ->make();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('admin-mytimes-create')) {
            app()->abort(403);
        }

        $timesheet = new TimeSheet();
        $timesheet->start_time = Carbon::now();
        $form_data = array('route' => array('admin.mytimes.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal');
        $page_title = trans("timetracker::mytimes/admin_lang.new");

        $customers_list = Customer::actives()->pluck('name', 'id')->all();
        $projects_list = [];

        $activities_list = Activity::actives()->pluck('name', 'id')->all();

        return view(
            'timetracker::mytimes.admin_edit',
            compact(
                'page_title',
                'timesheet',
                'form_data',
                'customers_list',
                'projects_list',
                'activities_list'
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Clavel\TimeTracker\Requests\MyTimesRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(MyTimesRequest $request)
    {
        if (!Auth::user()->can('admin-mytimes-create')) {
            app()->abort(403);
        }

        $timesheet = new TimeSheet();
        $this->saveData($request, $timesheet);

        return redirect('admin/mytimes/' . $timesheet->id . "/edit")
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
        if (!Auth::user()->can('admin-mytimes-update')) {
            app()->abort(403);
        }

        $timesheet = TimeSheet::find($id);
        $form_data = array('route' => array('admin.mytimes.update', $timesheet->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal');
        $page_title = trans("timetracker::mytimes/admin_lang.modify");

        $customers_list = Customer::actives()->pluck('name', 'id')->all();
        $projects_list = [];

        $project = Project::where("id", $timesheet->project_id)->first();
        if (!empty($project)) {
            $customer = Customer::where("id", $project->customer_id)->first();
            if (!empty($customer)) {
                $timesheet->customer_id = $customer->id;
                $projects_list = Project::where("customer_id", $project->customer_id)
                    ->actives()
                    ->pluck('name', 'id')
                    ->all();
            }
        }

        $activities_list = Activity::actives()->pluck('name', 'id')->all();

        return view(
            'timetracker::mytimes.admin_edit',
            compact(
                'page_title',
                'timesheet',
                'form_data',
                'customers_list',
                'projects_list',
                'activities_list'
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Clavel\TimeTracker\Requests\MyTimesRequest $request
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(MyTimesRequest $request, $id)
    {
        if (!Auth::user()->can('admin-mytimes-update')) {
            app()->abort(403);
        }

        $timesheet = TimeSheet::find($id);
        if (empty($timesheet)) {
            abort(404);
        }
        $this->saveData($request, $timesheet);

        return redirect('admin/mytimes/' . $timesheet->id . "/edit")
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
        if (!Auth::user()->can('admin-mytimes-delete')) {
            app()->abort(403);
        }

        $timesheet = TimeSheet::find($id);
        if (empty($timesheet)) {
            abort(404);
        }
        $timesheet->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("timetracker::mytimes/admin_lang.deleted"),
            'id' => $timesheet->id
        ));
    }

    private function saveData(Request $request, TimeSheet $timesheet)
    {
        $user_id = auth()->user()->id;
        $this->save($request, $timesheet, $user_id);
    }

    public function restartTimeSheet($id)
    {
        if (!Auth::user()->can('admin-mytimes-update')) {
            app()->abort(403);
        }

        $oldTimesheet = TimeSheet::find($id);

        $timesheet = new TimeSheet();
        $timesheet->start_time = Carbon::now();
        $timesheet->end_time = null;
        $timesheet->duration = null;
        $timesheet->description = '';

        $timesheet->project_id = $oldTimesheet->project_id;
        $timesheet->activity_id = $oldTimesheet->activity_id;
        $timesheet->user_id = $oldTimesheet->user_id;

        $timesheet->fixed_rate = $oldTimesheet->fixed_rate;
        $timesheet->hourly_rate = $oldTimesheet->hourly_rate;

        return $timesheet->save() ? 1 : 0;
    }

    public function restartTimeSheetActivity($customer_id, $project_id, $activity_id)
    {
        if (!Auth::user()->can('admin-mytimes-update')) {
            app()->abort(403);
        }

        $timesheet = new TimeSheet();
        $timesheet->start_time = Carbon::now();
        $timesheet->end_time = null;
        $timesheet->duration = null;
        $timesheet->description = '';

        $timesheet->project_id = $project_id;
        $timesheet->activity_id = $activity_id;
        $timesheet->user_id = auth()->user()->id;

        return $timesheet->save() ? 1 : 0;
    }

    public function getDescription(Request $request, $id)
    {
        if (!Auth::user()->can('admin-mytimes-update')) {
            app()->abort(403);
        }

        $timesheet = TimeSheet::find($id);

        $form_data = array('route' => array('admin.mytimes.description', $timesheet->id),
            'method' => 'POST',
            'id' => 'frmDataDescription',
            'class' => 'form-horizontal');

        return view(
            'timetracker::mytimes.admin_change_description',
            compact('timesheet', 'form_data', 'timesheet')
        );
    }

    public function changeDescription(Request $request, $id)
    {
        if (!Auth::user()->can('admin-mytimes-update')) {
            app()->abort(403);
        }

        $timesheet = TimeSheet::find($id);

        if (!empty($timesheet)) {
            $startDate = Carbon::parse($timesheet->start_time);
            $endDate = Carbon::now();
            $duration = $endDate->diffInSeconds($startDate);

            $timesheet->end_time = $endDate;
            $timesheet->duration = $duration;
            $timesheet->description = $request->get("description", "");
            return $timesheet->save() ? 1 : 0;
        }
        return 0;
    }

    public function generateExcel(Request $request)
    {
        $dt = $request->get("exportExcelRange", "");
        $dates = explode(' - ', $dt);

        try {
            $start_date = Carbon::createFromFormat('d/m/Y', $dates[0], "Europe/Madrid")->startOfDay();
            $end_date = Carbon::createFromFormat('d/m/Y', $dates[1], "Europe/Madrid")->startOfDay();
        } catch (\Exception $ex) {
            $start_date = Carbon::today()->subDays(7);
            $end_date = Carbon::today();
        }

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
            ->setTitle(trans('timetracker::mytimes/admin_lang.list'))
            ->setSubject(trans('timetracker::mytimes/admin_lang.list'))
            ->setDescription(trans('timetracker::mytimes/admin_lang.list'))
            ->setKeywords(trans('timetracker::mytimes/admin_lang.list'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('timetracker::projects/admin_lang.data_excel'));




        $sheet->getPageSetup()->setFitToWidth(1);

        // $sheet->getHeaderFooter()->setOddHeader('Registro de Aféresis');
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('timetracker::mytimes/admin_lang.start_time'),
            trans('timetracker::mytimes/admin_lang.end_time'),
            trans('timetracker::mytimes/admin_lang.duration'),
            trans('timetracker::mytimes/admin_lang.customer'),
            trans('timetracker::mytimes/admin_lang.project'),
            trans('timetracker::mytimes/admin_lang.activity'),
            trans('timetracker::mytimes/admin_lang.description'),
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');
        $row++;

        // Ahora los registros
        $timesheets = TimeSheet::where("user_id", auth()->user()->id)
            ->where('start_time', '>=', $start_date)
            ->where('end_time', '<=', $end_date)
            ->get();
        session()->all();

        foreach ($timesheets as $key => $timesheet) {
            $fecha_inicio = Carbon::createFromFormat("Y-m-d H:i:s", $timesheet->start_time)->format("d/m/Y");
            $fecha_fin = Carbon::createFromFormat("Y-m-d H:i:s", $timesheet->end_time)->format("d/m/Y");
            $duration = CarbonInterval::seconds($timesheet->duration)->cascade()->forHumans('Hm');
            $valores = array(
                $fecha_inicio,
                $fecha_fin,
                $duration,
                $timesheet->project->customer->name,
                $timesheet->project->name,
                $timesheet->activity->name,
                $timesheet->description,
            );

            $j = 1;
            foreach ($valores as $valor) {
                $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
            }
            $row++;
        }

        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $file_name = trans('timetracker::mytimes/admin_lang.list') . "_" . Carbon::now()->format('YmdHis');
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

    public function generateMyActivity(Request $request)
    {
        $dt = $request->get("exportExcelRange", "");
        $dates = explode(' - ', $dt);

        try {
            $start_date = Carbon::createFromFormat('d/m/Y', $dates[0], "Europe/Madrid")->startOfDay();
            $end_date = Carbon::createFromFormat('d/m/Y', $dates[1], "Europe/Madrid")->startOfDay();
        } catch (\Exception $ex) {
            $start_date = Carbon::today()->subDays(7);
            $end_date = Carbon::today();
        }


        if (ob_get_contents()) {
            ob_end_clean();
        }
        ini_set('memory_limit', '300M');

        set_time_limit(1000);

        $spreadsheet = new Spreadsheet();
        $spreadsheet
            ->getProperties()
            ->setCreator(config('app.name', ''))
            ->setLastModifiedBy(config('app.name', '')) // última vez modificado por
            ->setTitle(trans('timetracker::mytimes/admin_lang.list'))
            ->setSubject(trans('timetracker::mytimes/admin_lang.list'))
            ->setDescription(trans('timetracker::mytimes/admin_lang.list'))
            ->setKeywords(trans('timetracker::mytimes/admin_lang.list'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('timetracker::projects/admin_lang.data_excel'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        // $sheet->getHeaderFooter()->setOddHeader('Registro de Aféresis');
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');
        $row = 1;

        $project = User::where("user_id", auth()->user()->id)
            ->select(
                'users.*',
                'user_profiles.first_name',
                DB::raw("CONCAT(first_name,' ',last_name) as nombre")
            )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->get();

        foreach ($project as $key => $value) {
            $value->nombre;
        }

        $sheet->setCellValueByColumnAndRow(1, $row, trans('timetracker::mytimes/admin_lang.user_name'));
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

        ExcelHelper::cellColor($sheet, 'A' . $row . ':C' . $row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, $value->nombre);
        $sheet->mergeCellsByColumnAndRow(3, $row, 3, $row);
        ExcelHelper::cellColor($sheet, 'C' . $row . ':C' . $row, '92d050');

        $styleHeaderInfo = array(
            'font' => array(
                'size' => 14,
            ),
            'alignment' => array('horizontal' => Alignment::HORIZONTAL_LEFT),
        );
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($styleHeaderInfo);

        $row++;

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('timetracker::mytimes/admin_lang.customer'),
            trans('timetracker::mytimes/admin_lang.project'),
            trans('timetracker::mytimes/admin_lang.duration'),
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');
        $row++;

        // Ahora los registros
        $timesheets = TimeSheet::select('project_id', DB::raw("SUM(duration) as total"))
            ->where("user_id", auth()->user()->id)
            ->where('start_time', '>=', $start_date)
            ->where('end_time', '<=', $end_date)
            ->groupBy('project_id')
            ->get();
        session()->all();
        $tiempoTotal = 0;

        foreach ($timesheets as $key => $timesheet) {
            //con esta variable recojemos la suma de los tiempos de los users
            $tiempoTotal += $timesheet->total;
            $duration = CarbonInterval::seconds($timesheet->total)->cascade()->format("%H:%I");
            $valores = array(
                $timesheet->project->customer->name,
                $timesheet->project->name,
                $duration,
            );

            $j = 1;
            foreach ($valores as $valor) {
                $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
            }
            $row++;
        }
        $row++;

        $sheet->setCellValueByColumnAndRow(2, $row, "Tiempo total: ");
        $sheet->setCellValueByColumnAndRow(
            3,
            $row,
            $this->segAHms($tiempoTotal)
        );
        //Style de la hoja de Excel para MyTimes
        $style = $this->getStyle();

        $sheet
            ->getStyle('C' . $row)
            ->applyFromArray($style);


        $sheet->getStyle('B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('B' . $row)->getFont()->setSize(12);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $file_name = trans('timetracker::mytimes/admin_lang.list') . "_" . Carbon::now()->format('YmdHis');
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

    private function getStyle()
    {
        return array(
            'font' => array(
                'bold' => true,
                'size' => 14,
                'name' => 'Verdana',
                'color' => array('argb' => 'FF666666'),
            ),
            'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT),
            'borders' => array(
                'top' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['argb' => 'FF000000']
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['argb' => 'FF000000']
                ]
            ),
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF' . '92d050',
                ]
            ],
        );
    }

    public function generateMyDay(Request $request)
    {
        $dt = $request->get("exportExcelRange", "");
        $dates = explode(' - ', $dt);

        try {
            $start_date = Carbon::createFromFormat('d/m/Y', $dates[0], "Europe/Madrid")->startOfDay();
            $end_date = Carbon::createFromFormat('d/m/Y', $dates[1], "Europe/Madrid")->endOfDay();
        } catch (\Exception $ex) {
            $start_date = Carbon::today()->subDays(7);
            $end_date = Carbon::today();
        }
        $diferencia = $start_date->diffInDays($end_date);

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
            ->setTitle(trans('timetracker::mytimes/admin_lang.list'))
            ->setSubject(trans('timetracker::mytimes/admin_lang.list'))
            ->setDescription(trans('timetracker::mytimes/admin_lang.list'))
            ->setKeywords(trans('timetracker::mytimes/admin_lang.list'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->setTitle(trans('timetracker::projects/admin_lang.data_excel'));

        // $sheet->getHeaderFooter()->setOddHeader('Registro de Aféresis');
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');
        $row = 1;

        $project = User::where("user_id", auth()->user()->id)
            ->select(
                'users.*',
                'user_profiles.first_name',
                DB::raw("CONCAT(first_name,' ',last_name) as nombre")
            )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->get();

        foreach ($project as $key => $value) {
            $value->nombre;
        }

        $styleHeaderInfo = array(
            'alignment' => array('horizontal' => Alignment::HORIZONTAL_LEFT),
            'font' => array(
                'size' => 14,
            ),
        );

        $sheet->setCellValueByColumnAndRow(1, $row, trans('timetracker::mytimes/admin_lang.user_name'));
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

        ExcelHelper::cellColor($sheet, 'A' . $row . ':C' . $row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, $value->nombre);
        $sheet->mergeCellsByColumnAndRow(3, $row, 3, $row);
        ExcelHelper::cellColor($sheet, 'C' . $row . ':C' . $row, '92d050');

        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($styleHeaderInfo);
        $row++;
        $tiempoGlobal = 0;
        for ($i = 1; $i <= $diferencia; $i++) {
            $styleHeaderInfo = array(
                'font' => array(
                    'size' => 14,
                ),
                'alignment' => array('horizontal' => Alignment::HORIZONTAL_LEFT),
            );
            $start = Carbon::parse($start_date)->format("d/m/Y H:i");
            $sheet->setCellValueByColumnAndRow(1, $row, trans('timetracker::mytimes/admin_lang.day') . ' ' . $i);
            $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

            ExcelHelper::cellColor($sheet, 'A' . $row . ':C' . $row, '00b050');

            $sheet->setCellValueByColumnAndRow(3, $row, $start);
            $sheet->mergeCellsByColumnAndRow(3, $row, 3, $row);
            ExcelHelper::cellColor($sheet, 'C' . $row . ':C' . $row, '92d050');

            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($styleHeaderInfo);

            $row++;
            // Ponemos las cabeceras
            $cabeceras = array(
                trans('timetracker::mytimes/admin_lang.customer'),
                trans('timetracker::mytimes/admin_lang.project'),
                trans('timetracker::mytimes/admin_lang.duration'),
            );

            ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');

            $row++;

            // Ahora los registros
            $timesheets = TimeSheet::where("user_id", auth()->user()->id)
                ->whereBetween('start_time', [$start_date, $end_date])
                ->whereDate('start_time', $start_date)
                ->get();
            session()->all();
            $start_date->addDay(1);
            $tiempoTotal = 0;

            foreach ($timesheets as $key => $timesheet) {
                //con la variable $tiempoTotal recojemos la suma de los tiempos de los users
                $tiempoTotal += $timesheet->duration;
                //con la variable $tiempoGlobal recojemos la suma de los tiempos de los users
                $duration = CarbonInterval::seconds($timesheet->duration)->cascade()->format("%H:%I");
                $valores = array(
                    $timesheet->project->customer->name,
                    $timesheet->project->name,
                    $duration,
                );
                $j = 1;
                foreach ($valores as $valor) {
                    $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
                }
                $row++;
            }
            $tiempoGlobal += $tiempoTotal;
            $row++;
            $sheet->setCellValueByColumnAndRow(2, $row, "Tiempo: ");
            $sheet->setCellValueByColumnAndRow(
                3,
                $row,
                $this->segAHms($tiempoTotal)
            );
            //Style de la hoja de Excel
            $style = $this->getStyle();

            $sheet
                ->getStyle('C' . $row)
                ->applyFromArray($style);
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getFont()->setSize(14);
            $sheet->getPageSetup()->setHorizontalCentered(true);
            $sheet->getPageSetup()->setVerticalCentered(false);
            $row++;

            if ($i >= $diferencia) {
                $row += 2;
                $sheet->setCellValueByColumnAndRow(2, $row, "Tiempo total: ");
                $sheet->setCellValueByColumnAndRow(
                    3,
                    $row,
                    $this->segAHms($tiempoGlobal)
                );

                //Style de la hoja de Excel
                $style = array(
                    'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT),
                    'font' => array(
                        'bold' => true,
                        'size' => 20,
                        'name' => 'Verdana',
                        'color' => array('argb' => '#F44336'),
                    ),
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FF' . '92d050',
                        ]
                    ],
                    'borders' => array(
                        'top' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => 'FF000000']
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_DOUBLE,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ),
                );
                $sheet
                    ->getStyle('C' . $row)
                    ->applyFromArray($style);


                $sheet->getStyle('B' . $row)->getFont()->setBold(true);
                $sheet->getStyle('B' . $row)->getFont()->setSize(18);
                $sheet->getPageSetup()->setHorizontalCentered(true);
                $sheet->getPageSetup()->setVerticalCentered(false);
            }
        }

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $file_name = trans('timetracker::mytimes/admin_lang.list') . "_" . Carbon::now()->format('YmdHis');
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

    public function generateMyWeek(Request $request)
    {
        // JJ: Evidentemente si ves esto ya te digo que esta mierda no funciona
        // Lo hizo un inutil
        $dt = $request->get("exportExcelRange", "");
        $dates = explode(' - ', $dt);

        /*
        try {
            $start_date = Carbon::createFromFormat('d/m/Y', $dates[0], "Europe/Madrid")->startOfDay();
            $end_date = Carbon::createFromFormat('d/m/Y', $dates[1], "Europe/Madrid")->endOfDay();
        } catch (\Exception $ex) {
            $start_date = Carbon::today()->subDays(7);
            $end_date = Carbon::today();
        }

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
            ->setTitle(trans('timetracker::mytimes/admin_lang.list'))
            ->setSubject(trans('timetracker::mytimes/admin_lang.list'))
            ->setDescription(trans('timetracker::mytimes/admin_lang.list'))
            ->setKeywords(trans('timetracker::mytimes/admin_lang.list'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('timetracker::projects/admin_lang.data_excel'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        // $sheet->getHeaderFooter()->setOddHeader('Registro de Aféresis');
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');
        $row = 1;

        // Tremendo!!!
        $user=28;


        for ($e = 1; $e <= 9; $e++) {
            $project = User::where('users.id', '=', $user)
            ->select(
                'users.*',
                'user_profiles.first_name',
                DB::raw("CONCAT(first_name,' ',last_name) as nombre")
            )
                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->get();


            foreach ($project as $key => $value) {
                array(
                    $value->nombre,
                    $value->id,
                );
            }


            $styleHeaderInfo = array(
                'font' => array(
                    'size' => 14,
                ),
                'alignment' => array('horizontal' => Alignment::HORIZONTAL_LEFT),
            );

            $sheet->setCellValueByColumnAndRow(1, $row, trans('timetracker::mytimes/admin_lang.user_name'));
            $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

            ExcelHelper::cellColor($sheet, 'A' . $row . ':C' . $row, '00b050');

            $sheet->setCellValueByColumnAndRow(3, $row, $value->nombre.' '.$value->id);
            $sheet->mergeCellsByColumnAndRow(3, $row, 3, $row);

            ExcelHelper::cellColor($sheet, 'C' . $row . ':C' . $row, '92d050');
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($styleHeaderInfo);

            $row++;
            $tiempoGlobal = 0;
            $end_date = Carbon::createFromFormat('d/m/Y', $dates[1], "Europe/Madrid")->endOfDay();
            for ($i = 1; $i <= 7; $i++) {
                $styleHeaderInfo = array(
                    'font' => array(
                        'size' => 14,
                    ),
                    'alignment' => array('horizontal' => Alignment::HORIZONTAL_LEFT),
                );

                $end = Carbon::parse($end_date)->format("d/m/Y H:i");
                $sheet->setCellValueByColumnAndRow(1, $row, trans('timetracker::mytimes/admin_lang.day') . ' ' . $i);
                $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
                $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

                ExcelHelper::cellColor($sheet, 'A' . $row . ':C' . $row, '00b050');

                $sheet->setCellValueByColumnAndRow(3, $row, $end);
                $sheet->mergeCellsByColumnAndRow(3, $row, 3, $row);
                ExcelHelper::cellColor($sheet, 'C' . $row . ':C' . $row, '92d050');

                $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($styleHeaderInfo);

                $row++;
                // Ponemos las cabeceras
                $cabeceras = array(
                    trans('timetracker::mytimes/admin_lang.customer'),
                    trans('timetracker::mytimes/admin_lang.project'),
                    trans('timetracker::mytimes/admin_lang.duration'),
                );

                $j = 1;
                foreach ($cabeceras as $titulo) {
                    $sheet->setCellValueByColumnAndRow($j++, $row, $titulo);
                }
                $columna_final = Coordinate::stringFromColumnIndex($j - 1);
                $sheet->getStyle('A' . $row . ':' . $columna_final . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':' . $columna_final . $row)->getFont()->setSize(14);

                ExcelHelper::cellColor($sheet, 'A' . $row . ':' . $columna_final . $row, 'ffc000');

                foreach (ExcelHelper::xrange('A', $columna_final) as $columnID) {
                    $sheet->getColumnDimension($columnID)
                        ->setAutoSize(true);

                    $row++;
                }
                // Ahora los registros
                $timesheets = TimeSheet::where('user_id', '=', $user)
                    ->whereBetween('end_time', [$start_date, $end_date])
                    ->whereDate('end_time', $end_date)
                    ->get();
                session()->all();
                $tiempoTotal = 0;
                $end_date->subDay(1);

                foreach ($timesheets as $key => $timesheet) {
                    //con la variable $tiempoTotal recojemos la suma de los tiempos de los users
                    $tiempoTotal += $timesheet->duration;
                    $duration = CarbonInterval::seconds($timesheet->duration)->cascade()->format("%H:%I");
                    $valores = array(
                        $timesheet->project->customer->name,
                        $timesheet->project->name,
                        $duration,
                    );
                    $j = 1;
                    foreach ($valores as $valor) {
                        $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
                    }
                    $row++;
                }
                //con la variable $tiempoGlobal recojemos la suma de los tiempos de los users
                $tiempoGlobal += $tiempoTotal;
                $row++;
                $sheet->setCellValueByColumnAndRow(2, $row, "Tiempo: ");
                $sheet->setCellValueByColumnAndRow(
                    3,
                    $row,
                    $this->segAHms($tiempoTotal)
                );

                //Style de la hoja de Excel
                $style = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 14,
                        'name' => 'Verdana',
                        'color' => array('argb' => 'FF666666'),
                    ),
                    'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT),
                    'borders' => array(
                        'top' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => 'FF000000']
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_DOUBLE,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ),
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FF' . '92d050',
                        ]
                    ],
                );
                $sheet
                    ->getStyle('C' . $row)
                    ->applyFromArray($style);
                $sheet->getStyle('B' . $row)->getFont()->setBold(true);
                $sheet->getStyle('B' . $row)->getFont()->setSize(14);
                $sheet->getPageSetup()->setHorizontalCentered(true);
                $sheet->getPageSetup()->setVerticalCentered(false);

                $row++;

                if ($i >= 7) {
                    $user++;
                    $row += 3;
                    $sheet->setCellValueByColumnAndRow(2, $row, "Tiempo total: ");
                    $sheet->setCellValueByColumnAndRow(
                        3,
                        $row,
                        $this->segAHms($tiempoGlobal)
                    );
                    //Style de la hoja de Excel
                    $style = array(
                        'font' => array(
                            'bold' => true,
                            'size' => 20,
                            'name' => 'Verdana',
                            'color' => array('argb' => '#F44336'),
                        ),
                        'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT),
                        'borders' => array(
                            'top' => [
                                'borderStyle' => Border::BORDER_THICK,
                                'color' => ['argb' => 'FF000000']
                            ],
                            'bottom' => [
                                'borderStyle' => Border::BORDER_DOUBLE,
                                'color' => ['argb' => 'FF000000']
                            ]
                        ),
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'FF' . '92d050',
                            ]
                        ],
                    );
                    $sheet
                        ->getStyle('C' . $row)
                        ->applyFromArray($style);


                    $sheet->getStyle('B' . $row)->getFont()->setBold(true);
                    $sheet->getStyle('B' . $row)->getFont()->setSize(18);
                    $sheet->getPageSetup()->setHorizontalCentered(true);
                    $sheet->getPageSetup()->setVerticalCentered(false);
                    $row+=5;
                }
            }
        }

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $file_name = trans('timetracker::mytimes/admin_lang.list') . "_" . Carbon::now()->format('YmdHis');
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
        */
    }

    private function segAHms($seg)
    {
        $d = floor($seg / 86400);
        $h = floor(($seg - ($d * 86400)) / 3600);
        $m = floor(($seg - ($d * 86400) - ($h * 3600)) / 60);
        $s = $seg % 60;

        $hTotal = $h + $d * 24;
        return sprintf("%02d:%02d", $hTotal, $m);
    }
}
