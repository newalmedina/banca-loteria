<?php

namespace Clavel\NotificationBroker\Controllers\BouncedEmails;

use App\Helpers\Clavel\ExcelHelper;
use App\Http\Controllers\AdminController;
use Carbon\Carbon;
use Clavel\NotificationBroker\Models\BouncedEmail;
use Clavel\NotificationBroker\Models\BounceType;
use Clavel\NotificationBroker\Requests\AdminBouncedEmailsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\Facades\DataTables;

class AdminBouncedEmailsController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-envelope-open" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();
        $this->access_permission = 'admin-bouncedemails';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-bouncedemails-list')) {
            app()->abort(403);
        }

        $page_title = trans("notificationbroker::bouncedemails/admin_lang.bouncedemails");

        return view("notificationbroker::bouncedemails/admin_index", compact('page_title'))
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
        if (!auth()->user()->can('admin-bouncedemails-create')) {
            app()->abort(403);
        }

        $bouncedemail = new BouncedEmail();
        $form_data = array(
            'route' => array('bouncedemails.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("notificationbroker::bouncedemails/admin_lang.nueva_bouncedemail");


        $bounce_types = BounceType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view(
            'notificationbroker::bouncedemails/admin_edit',
            compact(
                'page_title',
                'bouncedemail',
                'form_data',
                'bounce_types'
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
    public function store(AdminBouncedEmailsRequest $request)
    {
        if (!auth()->user()->can('admin-bouncedemails-create')) {
            app()->abort(403);
        }

        $bouncedemail = new BouncedEmail();
        if (!$this->saveBouncedEmail($request, $bouncedemail)) {
            return redirect()->route('bouncedemails.create')
                ->with('error', trans('notificationbroker::bouncedemails/admin_lang.save_ko'));
        }

        return redirect()->to('admin/bouncedemails/' . $bouncedemail->id . "/edit")
            ->with('success', trans('notificationbroker::bouncedemails/admin_lang.save_ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-bouncedemails-update')) {
            app()->abort(403);
        }

        $bouncedemail = BouncedEmail::find($id);
        if (empty($bouncedemail)) {
            app()->abort(404);
        }

        $form_data = array(
            'route' => array('bouncedemails.update', $bouncedemail->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("notificationbroker::bouncedemails/admin_lang.editar_bouncedemail");


        $bounce_types = BounceType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view(
            'notificationbroker::bouncedemails/admin_edit',
            compact(
                'page_title',
                'bouncedemail',
                'form_data',
                'bounce_types'
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminBouncedEmailsRequest $request, $id)
    {
        if (!auth()->user()->can('admin-bouncedemails-update')) {
            app()->abort(403);
        }

        $bouncedemail = BouncedEmail::find($id);
        if (empty($bouncedemail)) {
            app()->abort(404);
        }

        if (!$this->saveBouncedEmail($request, $bouncedemail)) {
            return redirect()->to('admin/bouncedemails/' . $bouncedemail->id . "/edit")
                ->with('error', trans('notificationbroker::bouncedemails/admin_lang.save_ko'));
        }

        return redirect()->to('admin/bouncedemails/' . $bouncedemail->id . "/edit")
            ->with('success', trans('notificationbroker::bouncedemails/admin_lang.save_ok'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-bouncedemails-delete')) {
            app()->abort(403);
        }

        $bouncedemail = BouncedEmail::find($id);
        if (empty($bouncedemail)) {
            app()->abort(404);
        }

        $bouncedemail->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("notificationbroker::bouncedemails/admin_lang.deleted"),
            'id' => $bouncedemail->id
        ));
    }

    public function getData()
    {
        $query = DB::table('bouncedemails as c')
            ->select(
                array(
                    'c.id',
                    'c.active',
                    'c.email',
                    'c.bounce_code',
                    'bouncetypes.name'
                )
            )
            ->join('bouncetypes', 'bouncetypes.id', '=', 'c.bounce_type_id');

        $table = Datatables::of($query);
        $table->editColumn('active', function ($data) {
            return '<button class="btn ' . ($data->active ? "btn-success" : "btn-danger") . ' btn-sm" ' .
                (auth()->user()->can("admin-bouncedemails-update") ? "onclick=\"javascript:changeStatus('" .
                    url('admin/bouncedemails/state/' . $data->id) . "');\"" : "") . '
                        data-content="' . ($data->active ?
                    trans('general/admin_lang.descativa') :
                    trans('general/admin_lang.activa')) . '"
                        data-placement="right" data-toggle="popover">
                        <i class="fa ' . ($data->active ? "fa-eye" : "fa-eye-slash") . '"></i>
                        </button>';
        });
        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->can("admin-bouncedemails-update")) {
                $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                    url('admin/bouncedemails/' . $data->id . '/edit') . '\';" data-content="' .
                    trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
            }
            /*
            if (auth()->user()->can("admin-bouncedemails-delete")) {
                $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                    url('admin/bouncedemails/' . $data->id) . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }
            */
            return $actions;
        });


        $table->removeColumn('id');
        $table->rawColumns(['active', 'actions']);
        return $table->make();
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-bouncedemails-update')) {
            app()->abort(403);
        }

        $bouncedemail = BouncedEmail::find($id);

        if (!empty($bouncedemail)) {
            $bouncedemail->active = !$bouncedemail->active;
            return $bouncedemail->save() ? 1 : 0;
        }

        return 0;
    }

    private function saveBouncedEmail(Request $request, BouncedEmail $bouncedemail)
    {
        try {
            DB::beginTransaction();

            $bouncedemail->active = $request->input("active", false);
            $bouncedemail->email = $request->input("email", "");
            $bouncedemail->description = $request->input("description", "");
            $bouncedemail->bounce_code = $request->input("bounce_code", "");
            $bouncedemail->bounce_type_id = $request->input("bounce_type_id", null);
            $bouncedemail->save();


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return true;
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
            ->setTitle(trans('notificationbroker::bouncedemails/admin_lang.listado_data'))
            ->setSubject(trans('notificationbroker::bouncedemails/admin_lang.listado_data'))
            ->setDescription(trans('notificationbroker::bouncedemails/admin_lang.listado_data'))
            ->setKeywords(trans('notificationbroker::bouncedemails/admin_lang.listado_data'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('notificationbroker::bouncedemails/admin_lang.listado_data'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader(trans('notificationbroker::bouncedemails/admin_lang.listado_data'));
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('notificationbroker::bouncedemails/admin_lang.fields.id'),
            trans('notificationbroker::bouncedemails/admin_lang.fields.active'),
            trans('notificationbroker::bouncedemails/admin_lang.fields.email'),
            trans('notificationbroker::bouncedemails/admin_lang.fields.description'),
            trans('notificationbroker::bouncedemails/admin_lang.fields.bounce_code'),
            trans('notificationbroker::bouncedemails/admin_lang.fields.bounce_type'),
            trans('notificationbroker::bouncedemails/admin_lang.fields.created_at'),
            trans('notificationbroker::bouncedemails/admin_lang.fields.updated_at')
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');
        $row++;

        // Ahora los registros
        $data = DB::table('bouncedemails')
            ->select(
                'bouncedemails.id',
                'bouncedemails.active',
                'bouncedemails.email',
                'bouncedemails.description',
                'bouncedemails.bounce_code',
                'bouncedemails.bounce_type_id',
                'bouncedemails.created_at',
                'bouncedemails.updated_at'
            )
            ->orderBy('created_at', 'DESC')
            ->get();


        foreach ($data as $key => $value) {
            $valores = array(
                $value->id,
                $value->active,
                $value->email,
                $value->description,
                $value->bounce_code,
                $value->bounce_type_id,
                $value->created_at,
                $value->updated_at
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
        $file_name = trans('notificationbroker::bouncedemails/admin_lang.listado_data') .
            "_" . Carbon::now()->format('YmdHis');
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
