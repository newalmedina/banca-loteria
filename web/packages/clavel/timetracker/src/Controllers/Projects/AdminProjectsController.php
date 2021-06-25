<?php

namespace Clavel\TimeTracker\Controllers\Projects;

use App\Http\Controllers\AdminController;
use App\Models\User;
use Carbon\Carbon;
use Clavel\TimeTracker\Models\Config;
use Clavel\TimeTracker\Models\Customer;
use Clavel\TimeTracker\Models\InvoicedState;
use Clavel\TimeTracker\Models\Project;
use Clavel\TimeTracker\Models\ProjectState;
use Clavel\TimeTracker\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\Facades\DataTables;
use ExcelHelper;

class AdminProjectsController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-cubes" aria-hidden="true"></i>';

    private $activos;
    private $facturados;
    private $estados;
    private $tipos;
    private $responsables;
    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-projects';

        $this->middleware(function ($request, $next) {
            $this->activos =  ($request->session()->has('projects_activos')) ?
                ($request->session()->get('projects_activos')) : "";
            $this->facturados =  ($request->session()->has('projects_facturados')) ?
                ($request->session()->get('projects_facturados')) : array();
            $this->estados =  ($request->session()->has('projects_estados')) ?
                ($request->session()->get('projects_estados')) : array();
            $this->tipos =  ($request->session()->has('projects_tipos')) ?
                ($request->session()->get('projects_tipos')) : array();
            $this->responsables =  ($request->session()->has('projects_responsables')) ?
                ($request->session()->get('projects_responsables')) : array();

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth::user()->can('admin-projects-list')) {
            app()->abort(403);
        }

        $page_title = trans("timetracker::projects/admin_lang.title");
        $typesList = ProjectType::all();
        $show_historified = $request->session()->get("show_historified", false);
        $invoicedList = InvoicedState::all();
        $statesList = ProjectState::all();
        $responsableList = User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select(
                'users.id',
                DB::raw("CONCAT(first_name,' ',last_name) as nombre")
            )
            ->orderBy('nombre', 'ASC')
            ->get();


        return view('timetracker::projects.admin_index', compact('page_title', 'show_historified'))
            ->with([
                'page_title_icon' => $this->page_title_icon,
                'activos'=>$this->activos,
                'facturado'=>$this->facturados,
                'facturados' =>$invoicedList,
                'estado'=>$this->estados,
                'estados'=>$statesList,
                'tipo'=>$this->tipos,
                'tipos'=>$typesList,
                'responsable'=>$this->responsables,
                'responsables'=>$responsableList
            ]);
    }


    public function getData(Request $request)
    {
        $states = ProjectState::all();
        $types = ProjectType::all();
        $projects = Project::select(
            array(
                'projects.id',
                'projects.active',
                'projects.invoiced',
                'projects.historified',
                'projects.name',
                'projects.slug_state',
                'customers.id as customer_id',
                'customers.name as customer_name',
                'projects.order_number',
                'projects.customer_number',
                'projects.expire_at',
                'projects.project_type_id',
                'projects.responsable_id'
            )
        )
            ->join('customers', 'customers.id', '=', 'projects.customer_id')
            ->join('project_states', 'projects.slug_state', '=', 'project_states.slug');

        if ($this->activos != '') {
            $projects->where('projects.active', $this->activos);
        }
        if (count($this->estados)>0) {
            $projects->whereIn('projects.slug_state', $this->estados);
        }
        if (count($this->responsables)>0) {
            $projects->whereIn('projects.responsable_id', $this->responsables);
        }
        if (count($this->facturados)>0) {
            $projects->whereIn('projects.invoiced', $this->facturados);
        }
        if (count($this->tipos)>0) {
            $projects->whereIn('projects.project_type_id', $this->tipos);
        }
        $show_historified = $request->session()->get("show_historified", false);

        if (!$show_historified) {
            $projects = $projects->notHistorified();
        }


        return Datatables::of($projects)
            ->filterColumn('projects.expire_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(projects.expire_at, '%d/%m/%Y') like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('projects.order_number', function ($query, $keyword) {
                $query->whereRaw("projects.order_number like ?", ["%{$keyword}%"])
                    ->orWhereRaw("projects.customer_number like ?", ["%{$keyword}%"]);
            })
            /*->filter(function ($query) use ($request) {
                if(!empty($request->search) && !empty($request->search['value'])) {
                    //$query->whereRaw("projects.customer_number like ?", ["%{$request->search['value']}%"]);
                }
            })*/
            ->editColumn('active', function ($data) {
                return '<button class="btn '.($data->active?"btn-success":"btn-danger").' btn-sm" '.
                    (auth()->user()->can("admin-projects-update")?"onclick=\"javascript:changeStatus('".
                        url('admin/projects/state/'.$data->id)."');\"":"").'
                        data-content="'.($data->active?
                        trans('general/admin_lang.descativa'):
                        trans('general/admin_lang.activa')).'"
                        data-placement="right" data-toggle="popover">
                        <i class="fa '.($data->active?"fa-eye":"fa-eye-slash").'" aria-hidden="true"></i>
                        </button>';
            })
            ->editColumn('invoiced', function ($data) {
                switch ($data->invoiced) {
                    case 1:
                        $invoicedColor = "btn-success";
                        $invoicedText =  trans('timetracker::projects/admin_lang.pagado');
                        break;
                    case 2:
                        $invoicedColor = "btn-warning";
                        $invoicedText =  trans('timetracker::projects/admin_lang.parcialmente_pagado');
                        break;
                    default:
                        $invoicedColor = "btn-danger";
                        $invoicedText =  trans('timetracker::projects/admin_lang.no_pagado');
                }
                return '<button class="btn '.$invoicedColor.' btn-sm" '.
                    (auth()->user()->can("admin-projects-update")?"onclick=\"javascript:changeInvoiced('".
                        url('admin/projects/invoiced/'.$data->id)."');\"":"").'
                        data-content="'.$invoicedText.'"
                        data-placement="right" data-toggle="popover">
                        <i class="fa fa-eur" aria-hidden="true"></i>
                        </button>';
            })
            ->editColumn('customer_name', function ($data) {
                return '<a href="'.url('admin/customers/'.$data->customer_id).'/edit">'
                    .'<span class="label label-primary">'
                    .$data->customer_name
                    .'</span>'
                    .'</a>';
            })
            ->editColumn('responsable_id', function ($data) {
                if (!empty($data->responsable_id)) {
                    return $data->responsable->userProfile->fullName;
                }
                return "";
            })
            ->editColumn('expire_at', function ($data) {
                $color = "success";
                if ($data->expire_at!=null && $data->expire_at != '') {
                    $expire_at = new Carbon($data->expire_at);

                    if (Carbon::now()->startOfDay()>=$expire_at->startOfDay()) {
                        $color = "danger";
                    } elseif (Carbon::now()->startOfDay()->addDays(15) >=$expire_at->startOfDay()) {
                        $color = "warning";
                    }
                }


                return '<span class="label label-'.$color.'">'
                    .$data->ExpireAtFormatted
                    .'</span>';
            })
            ->editColumn('slug_state', function ($data) use ($states) {
                $selected_state = $states->first(function ($item) use ($data) {
                    return $item->slug == $data->slug_state;
                });

                return '<a href="javascript:openProjectState(\'' . $data->id . '\');">'
                    .'<span class="label label-primary"
                        style="background-color: '.$selected_state->color.' !important;">'
                    .$selected_state->name
                    .'</span>'
                    .'</a>';
            })
            ->editColumn('project_type_id', function ($data) use ($types) {
                $selected_type = $types->first(function ($item) use ($data) {
                    return $item->id == $data->project_type_id;
                });

                return '<a href="javascript:openProjectType(\'' . $data->id . '\');">'
                    .'<span class="label label-primary"
                        style="background-color: '.$selected_type->color.' !important;">'
                    .$selected_type->name
                    .'</span>'
                    .'</a>';
            })
            ->addColumn('alert', function ($data) {
                if ($data->slug_state == 'finalizado' && $data->invoiced!= 1) {
                    return true;
                }
                return false;
            })

            ->addColumn('actions', function ($data) {
                $actions = '';
                if (auth()->user()->can("admin-projects-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/projects/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';

                    // Solo si esta facturado y no activo y finalizado
                    if (($data->invoiced && !$data->active && $data->slug_state == "finalizado" &&
                            !$data->historified) ||
                        ($data->slug_state == "anulado" && !$data->historified)
                        ) {
                        $actions .= '<button class="btn btn-warning btn-sm" onclick="javascript:historifyElement(\''.
                            url('admin/projects/historify/'.$data->id).'\');" data-content="'.
                            trans('timetracker::projects/admin_lang.historify').'"
                            style="margin-right: 3px;"
                            data-placement="right" data-toggle="popover">
                            <i class="fa fa-history" aria-hidden="true"></i></button>';
                    }

                    if ($data->historified) {
                        $actions .= '<button class="btn bg-purple btn-sm" onclick="javascript:recoverElement(\''.
                            url('admin/projects/recover/'.$data->id).'\');" data-content="'.
                            trans('timetracker::projects/admin_lang.recover').'"
                            style="margin-right: 3px;"
                            data-placement="right" data-toggle="popover">
                            <i class="fa fa-retweet" aria-hidden="true"></i></button>';
                    }
                }
                if (auth()->user()->can("admin-projects-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\''.
                        url('admin/projects/'.$data->id).'\');" data-content="'.
                        trans('general/admin_lang.borrar').'" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                return $actions;
            })

            ->removeColumn('id', 'customer_id', 'customer_number')

            ->rawColumns(['active',
                'invoiced',
                'slug_state',
                'customer_name',
                'actions',
                'project_type_id',
                'expire_at'])
            ->make();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('admin-projects-create')) {
            app()->abort(403);
        }

        $project = new Project();
        $form_data = array('route' => array('admin.projects.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal');
        $page_title = trans("timetracker::projects/admin_lang.new");

        $customersList = Customer::pluck('name', 'id')->all();


        $statesList = ProjectState::all();
        $typesList = ProjectType::all();

        $invoicedList = InvoicedState::all();
        $responsableList = User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select(
                'users.id',
                DB::raw("CONCAT(first_name,' ',last_name) as nombre")
            )
            ->orderBy('nombre', 'ASC')
            ->pluck('nombre', 'users.id')->all();

        $project->budget_number = $this->proponerNumeroOferta();
        $project->order_number = $this->proponerNumeroProyecto();
        $project->vat = 21;


        $tiempo_estimado = "?";
        return view(
            'timetracker::projects.admin_edit',
            compact(
                'page_title',
                'project',
                'form_data',
                'customersList',
                'statesList',
                'invoicedList',
                'typesList',
                'tiempo_estimado',
                'responsableList'
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
    public function store(Request $request)
    {
        if (!Auth::user()->can('admin-projects-create')) {
            app()->abort(403);
        }

        $project = new Project();
        $this->saveData($project, $request);

        return redirect('admin/projects/'.$project->id."/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $project = Project::find($id);
        $form_data = array('route' => array('admin.projects.update', $project->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal');
        $page_title = trans("timetracker::projects/admin_lang.modify");

        $customersList = Customer::pluck('name', 'id')->all();
        $responsableList = User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select(
                'users.id',
                DB::raw("CONCAT(first_name,' ',last_name) as nombre")
            )
            ->orderBy('nombre', 'ASC')
            ->pluck('nombre', 'users.id')->all();

        $statesList = ProjectState::all();
        $typesList = ProjectType::all();
        $invoicedList = InvoicedState::all();
        $tiempo_estimado = "?";
        if (!empty($project->hourly_rate) && !empty($project->budget)) {
            $tiempo_estimado = $project->budget/$project->hourly_rate;
        }

        return view(
            'timetracker::projects.admin_edit',
            compact(
                'page_title',
                'project',
                'form_data',
                'customersList',
                'responsableList',
                'statesList',
                'invoicedList',
                'typesList',
                'tiempo_estimado'
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $project = Project::find($id);
        if (empty($project)) {
            abort(404);
        }
        $this->saveData($project, $request);

        return redirect('admin/projects/'.$project->id."/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('admin-projects-delete')) {
            app()->abort(403);
        }

        $project = Project::find($id);
        if (empty($project)) {
            abort(404);
        }
        $project->delete();


        return response()->json(array(
            'success' => true,
            'msg' => trans("timetracker::projects/admin_lang.deleted"),
            'id' => $project->id
        ));
    }


    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $project = Project::find($id);

        if (!empty($project)) {
            $project->active = !$project->active;
            return $project->save()?1:0;
        }

        return 0;
    }
    public function setInvoiceState($id)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $project = Project::find($id);

        if (!empty($project)) {
            $project->invoiced=($project->invoiced+1)%3;
            return $project->save() ? 1 : 0;
        }
        return 0;
    }

    public function setHistorify($id)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $project = Project::find($id);

        if (!empty($project)) {
            $project->historified = !$project->historified;
            return $project->save()?1:0;
        }

        return 0;
    }


    public function setRecovery($id)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $project = Project::find($id);

        if (!empty($project)) {
            $project->historified = !$project->historified;
            return $project->save()?1:0;
        }

        return 0;
    }



    private function saveData(Project $projects, Request $request)
    {
        $projects->name = $request->get("name", "");
        $projects->order_number = $request->get("order_number", "");
        $projects->budget_number = $request->get("budget_number", "");
        $projects->customer_number = $request->get("customer_number", "");
        $projects->invoice_number = $request->get("invoice_number", "");
        $projects->description = $request->get("description", "");
        $projects->bill_info = $request->get("bill_info", "");
        $projects->customer_id = $request->get("customer_id", "");
        $projects->customer_final_id = $request->get("customer_final_id", null);
        $projects->responsable_id = $request->get("responsable_id", null);
        $projects->color = $request->get("color", "");
        $projects->budget = $request->get("budget", "");
        $projects->vat = $request->get("vat", "");

        try {
            $budget = floatval($projects->budget);
            $vat = floatval($projects->vat) ;
            $projects->total = $budget + (($budget*$vat)/100);
        } catch (\Exception $ex) {
            $projects->total = null;
        }

        //$projects->fixed_rate = $request->get("fixed_rate", "");
        $projects->hourly_rate = $request->get("hourly_rate", "");
        $projects->work_hours = $request->get("work_hours", "");
        $projects->active = $request->get("active", false);
        $projects->invoiced = $request->get("invoiced", false);
        $projects->slug_state = $request->get("slug_state", '');
        $projects->project_type_id = $request->get("project_type_id", 0);
        $projects->expire_at = ($request->get("expire_at")!='') ?
            Carbon::createFromFormat('d/m/Y', $request->get("expire_at")) : null;
        $projects->closed_at = ($request->get("closed_at")!='') ?
            Carbon::createFromFormat('d/m/Y', $request->get("closed_at")) : null;

        $projects->save();
    }

    public function setShowHistorified(Request $request)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $show_historified = $request->session()->get("show_historified", false);

        $request->session()->put('show_historified', !$show_historified);

        return redirect('admin/projects');
    }

    public function generateExcel(Request $request, $q)
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
            ->setTitle(trans('timetracker::projects/admin_lang.list_export'))
            ->setSubject(trans('timetracker::projects/admin_lang.list_export'))
            ->setDescription(trans('timetracker::projects/admin_lang.list_export'))
            ->setKeywords(trans('timetracker::projects/admin_lang.list_export'))
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

        $sheet->getHeaderFooter()->setOddHeader('Registro de Aféresis');
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        /*
        // Ponemos algunos títulos a modo de ejemplo
        $sheet->setCellValueByColumnAndRow(1, $row, "Proyectos de:");
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        ExcelHelper::cellColor($sheet, 'A'.$row.':B'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, " Aduxia");
        $sheet->mergeCellsByColumnAndRow(3, $row, 8, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setSize(14);

        $row++;

        // Segunda linea de información
        $sheet->setCellValueByColumnAndRow(1, $row, "Fecha inicio:");
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);

        ExcelHelper::cellColor($sheet, 'A'.$row.':G'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, Carbon::now()->format("d/m/Y"));
        $sheet->mergeCellsByColumnAndRow(3, $row, 8, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setSize(14);

        $row++;


        //Styles
        $style = array(
            'font' => array('bold' => true,),
            'alignment' => array('horizontal' =>  Alignment::HORIZONTAL_CENTER,),
            'borders' => array(
                'top' => array(
                    'style' => Border::BORDER_THIN,
                ),
            ),
            'fill' => array(
                'type' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ),
            )
        );
        //Bolds
        $sheet
            ->getStyle('A'.$row.':G'.$row)
            ->applyFromArray($style);

        ExcelHelper::cellColor($sheet,'A'.$row, 'ffc000');

        $sheet->setCellValueByColumnAndRow(1, $row, "Filiación");
        $sheet->mergeCellsByColumnAndRow(1,$row,2,$row);
        ExcelHelper::cellColor($sheet,'B'.$row.':C'.$row, '92d050');

        $sheet->setCellValueByColumnAndRow(3, $row, "Datos basales");
        $sheet->mergeCellsByColumnAndRow(3,$row,6,$row);
        ExcelHelper::cellColor($sheet,'D'.$row.':G'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(7, $row, "Valoración basal");
        $sheet->mergeCellsByColumnAndRow(7,$row,17,$row);
        ExcelHelper::cellColor($sheet,'H'.$row.':R'.$row, '92d050');

        $sheet->setCellValueByColumnAndRow(18, $row, "Complicaciones");
        $sheet->mergeCellsByColumnAndRow(18,$row,27,$row);
        ExcelHelper::cellColor($sheet,'S'.$row.':AB'.$row, '00b050');

        $row++;
        */

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('timetracker::projects/admin_lang.id'),
            trans('timetracker::projects/admin_lang.name'),
            trans('timetracker::projects/admin_lang.type'),
            trans('timetracker::projects/admin_lang.active'),
            trans('timetracker::projects/admin_lang.order_number'),
            trans('timetracker::projects/admin_lang.customer_number'),
            trans('timetracker::projects/admin_lang.budget_number'),
            trans('timetracker::projects/admin_lang.invoice_number'),
            trans('timetracker::projects/admin_lang.description'),
            trans('timetracker::projects/admin_lang.bill_info'),
            trans('timetracker::projects/admin_lang.state'),
            trans('timetracker::projects/admin_lang.invoiced'),
            trans('timetracker::projects/admin_lang.customer'),
            trans('timetracker::projects/admin_lang.customer_final'),
            trans('timetracker::projects/admin_lang.hourly_rate'),
            trans('timetracker::projects/admin_lang.work_hours'),
            trans('timetracker::projects/admin_lang.budget'),
            trans('timetracker::projects/admin_lang.vat'),
            trans('timetracker::projects/admin_lang.total'),
            //trans('timetracker::projects/admin_lang.fixed_rate'),
            trans('timetracker::projects/admin_lang.historified'),
            trans('timetracker::projects/admin_lang.expire_at'),
            trans('timetracker::projects/admin_lang.closed_at'),
            trans('timetracker::projects/admin_lang.responsable'),

        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');

        $row++;

        // Ahora los registros
        $projects = Project::select(
            'projects.*',
            'user_profiles.first_name',
            DB::raw("CONCAT(first_name,' ',last_name) as nombre")
        )

            ->join('customers', 'customers.id', '=', 'projects.customer_id')
            ->join('project_states', 'projects.slug_state', '=', 'project_states.slug')
            ->leftJoin('users', 'users.id', '=', 'projects.responsable_id')
            ->leftjoin('user_profiles', 'user_profiles.user_id', '=', 'users.id');

        if ($this->activos != '') {
            $projects = $projects->where('projects.active', '=', $this->activos);
        }
        if (count($this->tipos) > 0) {
            $projects->whereIn('project_type_id', $this->tipos);
        }
        if (count($this->responsables) > 0) {
            $projects->whereIn('responsable_id', $this->responsables);
        }

        if (count($this->facturados) > 0) {
            $projects->whereIn('invoiced', $this->facturados);
        }

        if (count($this->estados) > 0) {
            $projects->whereIn('slug_state', $this->estados);
        }
        $show_historified = $request->session()->get("show_historified", false);

        if (!$show_historified) {
            $projects = $projects->notHistorified();
        }

        // Selects de la tabla
        $q = json_decode($q);
        if (!empty($q[0]) && !empty($q[0]->general)) {
            $paramsToSearch = array(
                'projects.id',
                'projects.active',
                'projects.invoiced',
                'projects.historified',
                'projects.name',
                'projects.slug_state',
                'customers.id',
                'customers.name',
                'projects.order_number',
                'projects.customer_number',
                'projects.expire_at',
                'projects.project_type_id',
                'projects.responsable_id'
            );

            $projects = $projects->where(function ($query) use ($q, $paramsToSearch) {
                foreach ($paramsToSearch as $param) {
                    $query->orWhere($param, 'like', '%' . $q[0]->general. '%');
                }
            });
        }

        if (!empty($q[1]) && !empty($q[1]->columns)) {
            $projects = $projects->where(function ($query) use ($q) {
                foreach ($q[1]->columns as $column) {
                    if (!empty($column->value)) {
                        $query->Where($column->name, 'like', '%' . $column->value. '%');
                    }
                }
            });
        }


        $projects = $projects
            ->orderBy('name', 'ASC')
            ->get();


        foreach ($projects as $key => $value) {
            $valores = array(
                $value->id,
                $value->name,
                $value->type->name,
                ($value->active=='1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no'),
                $value->order_number,
                $value->customer_number,
                $value->budget_number,
                $value->invoice_number,
                $value->description,
                $value->bill_info,
                $value->slug_state,
                $value->facturado->name,
                $value->customer_id,
                $value->customer_final_id,
                $value->hourly_rate,
                $value->work_hours,
                $value->budget,
                $value->vat,
                $value->total,
                //$value->fixed_rate,
                ($value->historified=='1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no'),
                $value->ExpireAtFormatted,
                $value->ClosedAtFormatted,
                $value->nombre,


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
        $file_name = trans('timetracker::projects/admin_lang.list_export')."_".Carbon::now()->format('YmdHis');
        $outPath = storage_path("app/exports/");
        if (!file_exists($outPath)) {
            mkdir($outPath, 0777, true);
        }

        $writer->save($outPath.$file_name.'.xlsx');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name.'.xlsx' . '"');
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

    public function getProjectStates(Request $request, $id)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $project = Project::find($id);
        $statesList = ProjectState::all();

        $form_data = array('route' => array('admin.projects.stateProject', $project->id),
            'method' => 'POST',
            'id' => 'frmDataStateProject',
            'class' => 'form-horizontal');


        return view('timetracker::projects.admin_change_state', compact('project', 'form_data', 'statesList'));
    }

    public function changeStateProject(Request $request, $id)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $project = Project::find($id);

        if (!empty($project)) {
            $project->slug_state = $request->get("slug_state", "");
            return $project->save()?1:0;
        }

        return 0;
    }

    public function getProjectTypes(Request $request, $id)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $project = Project::find($id);
        $typesList = ProjectType::all();

        $form_data = array('route' => array('admin.projects.typeProject', $project->id),
            'method' => 'POST',
            'id' => 'frmDataTypeProject',
            'class' => 'form-horizontal');

        return view('timetracker::projects.admin_change_type', compact('project', 'form_data', 'typesList'));
    }

    public function changeTypeProject(Request $request, $id)
    {
        if (!Auth::user()->can('admin-projects-update')) {
            app()->abort(403);
        }

        $project = Project::find($id);

        if (!empty($project)) {
            $project->project_type_id = $request->get("slug_type", "");
            return $project->save()?1:0;
        }
        return 0;
    }

    private function proponerNumeroOferta()
    {
        $config = Config::first();
        if (empty($config)) {
            return redirect('admin/timetracker-config');
        }

        $config->increment('budget_counter');

        $prefix = $config->budget_prefix;
        $counter = $config->budget_counter;
        $digits = $config->budget_digits;
        $numeroOferta = $prefix.str_pad($counter, $digits, "0", STR_PAD_LEFT);

        return $numeroOferta;
    }

    private function proponerNumeroProyecto()
    {
        $config = Config::first();
        if (empty($config)) {
            return redirect('admin/timetracker-config');
        }

        $config->increment('order_counter');

        $prefix = $config->order_prefix;
        $counter = $config->order_counter;
        $digits = $config->order_digits;
        $numeroProyecto = $prefix.str_pad($counter, $digits, "0", STR_PAD_LEFT);

        return $numeroProyecto;
    }

    public function getOrderNumber(Request $request, $id)
    {
        $orderNumber = $this->proponerNumeroProyecto();

        return response()->json(array(
            'success' => true,
            'order_number' => $orderNumber
        ));
    }
    public function getBudgetNumber(Request $request, $id)
    {
        $budgetNumber = $this->proponerNumeroOferta();

        return response()->json(array(
            'success' => true,
            'budget_number' => $budgetNumber
        ));
    }

    public function saveFilter(Request $request)
    {
        $request->session()->forget('projects_activos');
        if (!is_null($request->input('activos')) && $request->input('activos')!='') {
            $request->session()->put('projects_activos', $request->input("activos"));
        }
        $request->session()->forget('projects_facturados');
        if (!is_null($request->input('facturados')) && $request->input('facturados')!='') {
            $request->session()->put('projects_facturados', $request->input("facturados"));
        }

        $request->session()->forget('projects_estados');
        if (!is_null($request->input('estado')) && $request->input('estado')!='') {
            $request->session()->put('projects_estados', $request->input("estado"));
        }
        $request->session()->forget('projects_tipos');
        if (!is_null($request->input('tipo')) && $request->input('tipo')!='') {
            $request->session()->put('projects_tipos', $request->input("tipo"));
        }
        $request->session()->forget('projects_responsables');
        if (!is_null($request->input('responsable')) && $request->input('responsable')!='') {
            $request->session()->put('projects_responsables', $request->input("responsable"));
        }
        return redirect("admin/projects");
    }

    public function clearFilter(Request $request)
    {
        $request->session()->forget('projects_activos');
        $request->session()->forget('projects_facturados');
        $request->session()->forget('projects_estados');
        $request->session()->forget('projects_tipos');
        $request->session()->forget('projects_responsables');
        return redirect("admin/projects");
    }
}
