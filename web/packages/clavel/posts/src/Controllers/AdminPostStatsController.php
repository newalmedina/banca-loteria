<?php namespace Clavel\Posts\Controllers;

use App\Http\Controllers\AdminController;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Clavel\Posts\Models\Post;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use ExcelHelper;

class AdminPostStatsController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-bar-chart" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-posts-stats';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-posts-stats-list')) {
            app()->abort(403);
        }

        $page_title = trans("posts::admin_lang.stats_news");

        return view("posts::admin_stats_index", compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getData()
    {
        $locale = app()->getLocale();

        $news = DB::table('posts as p')
            ->join('post_translations as pt', function ($join) use ($locale) {
                $join->on('pt.post_id', '=', 'p.id');
                $join->on('pt.locale', '=', DB::raw("'".$locale."'"));
            })
            ->leftJoin('post_stats as s', function ($join) use ($locale) {
                $join->on('s.post_id', '=', 'p.id');
            })
            ->select(
                array(
                    'p.id',
                    'pt.title',
                    'pt.url_seo',
                    'p.date_post',
                    DB::raw("SUM(COALESCE(s.visits, 0)) AS visits")
                )
            )
            ->groupBy(
                'p.id',
                'pt.title',
                'pt.url_seo',
                'p.date_post'
            );

        return Datatables::of($news)
            ->editColumn('visits', function ($data) {
                return $data->visits;
            })
            ->editColumn('url_seo', function ($row) {
                return "<a href='/posts/post/".$row->url_seo."'
                target='_blank'>/posts/post/".$row->url_seo."</a>";
            })
            ->editColumn('date_post', function ($row) {
                $fecha = new Carbon($row->date_post);
                return $fecha->format('d/m/Y');
            })
            ->editColumn('actions', function ($data) {
                $actions = '';
                if (auth()->user()->can("admin-posts-stats-read")) {
                    $actions .= '<button class="btn bg-purple btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/posts/stats/' . $data->id . '/users') . '\';" data-content="' .
                        trans('posts::admin_lang.user_visits') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-area-chart"></i></button> ';
                }
                if (auth()->user()->can("admin-posts-stats-read")) {
                    $actions .= '<button class="btn bg-yellow btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/posts/stats/' . $data->id . '/time') . '\';" data-content="' .
                        trans('posts::admin_lang.time_visits') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-line-chart"></i></button> ';
                }
                return $actions;
            })
            ->removeColumn('id')
            ->rawColumns(['visits', 'actions', 'url_seo'])
            ->make();
    }

    public function getStatsUsers($id)
    {
        if (!auth()->user()->can('admin-posts-list')) {
            app()->abort(403);
        }

        $post = Post::find($id);
        if (empty($post)) {
            app()->abort(404);
        }

        $page_title = trans("posts::admin_lang.user_visits");
        $page_description = $post->title;

        return view(
            "posts::admin_stats_user_index",
            compact(
                'page_title',
                'page_description',
                'post'
            )
        )
            ->with('page_title_icon', '<i class="fa fa-area-chart"></i>');
    }


    public function getDataStatsUser($id)
    {
        $users = User::UserProfiles()
            ->select([
                'users.id',
                'user_profiles.first_name',
                'user_profiles.last_name',
                'users.email',
                'users.username',
                's.visits',
                's.created_at',
                's.updated_at'
            ])
            ->join('post_tracks as s', 'users.id', '=', 's.user_id')
            ->where('s.post_id', intval($id))
            ->getQuery();

        return Datatables::of($users)
            ->editColumn('visits', function ($data) {
                return $data->visits;
            })
            ->editColumn('first_name', function ($row) {
                return $row->first_name;
            })
            ->editColumn('last_name', function ($row) {
                return $row->last_name;
            })
            ->editColumn('created_at', function ($row) {
                $fecha = new Carbon($row->created_at);
                return $fecha->format('d/m/Y');
            })
            ->editColumn('updated_at', function ($row) {
                $fecha = new Carbon($row->updated_at);
                return $fecha->format('d/m/Y');
            })
            ->removeColumn('id')
            ->rawColumns(['visits'])
            ->make();
    }


    public function getStatsTime(Request $request, $id)
    {
        if (!auth()->user()->can('admin-posts-list')) {
            app()->abort(403);
        }

        $post = Post::find($id);
        if (empty($post)) {
            app()->abort(404);
        }

        $page_title = trans("posts::admin_lang.time_visits");
        $page_description = $post->title;

        $dt = $request->get("date_ini", "");

        if (empty($dt)) {
            $fecha = Carbon::today()->subMonths(1);
        } else {
            try {
                $fecha = Carbon::createFromFormat('d/m/Y', $dt, "Europe/Madrid")->startOfDay();
            } catch (\Exception $ex) {
                $fecha = Carbon::today()->subMonths(1);
            }
        }

        $date_ini = $fecha->format("d/m/Y");

        $sql = "SELECT visits, fecha
                FROM post_stats
                WHERE date(fecha)>=?
                AND post_id=?
                ORDER BY fecha ASC";

        $visitas = DB::select($sql, [$fecha,$post->id]);

        $dates = array();
        $today = Carbon::today();
        $period = CarbonPeriod::create($fecha, $today);
        // Iterate over the period
        foreach ($period as $date) {
            $dates[] = $date->format("Ymd");
        }

        $total = sizeof($dates);
        $data = array_fill(0, $total, 0);

        $max_y = 5;
        foreach ($visitas as $visita) {
            $f = Carbon::parse($visita->fecha)->startOfDay();
            $date_diff=$f->diffInDays($today);
            $data[$total-$date_diff-1] = $visita->visits;
            if ($visita->visits > $max_y) {
                $max_y = $visita->visits;
            }
        }

        $max_y = $this->roundUpToAny($max_y, 10);

        $registros['date'] = implode(',', $dates);
        $registros['data'] = implode(',', $data);

        return view(
            "posts::admin_stats_time_index",
            compact(
                'page_title',
                'page_description',
                'post',
                'date_ini',
                'registros',
                'max_y'
            )
        )
            ->with('page_title_icon', '<i class="fa fa-line-chart"></i>');
    }

    private function roundUpToAny($n, $x = 5)
    {
        return round(($n+$x/2)/$x)*$x;
    }


    public function generateExcelUsers($id)
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
            ->setLastModifiedBy(config('app.name', '')) // Última vez modificado por
            ->setTitle(trans('posts::admin_lang.exportar_usuarios'))
            ->setSubject(trans('posts::admin_lang.exportar_usuarios'))
            ->setDescription(trans('posts::admin_lang.exportar_usuarios'))
            ->setKeywords(trans('posts::admin_lang.exportar_usuarios'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('posts::admin_lang.exportar_usuarios'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader(trans('posts::admin_lang.exportar_usuarios'));
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('users/lang.username'),
            trans('users/lang.nombre_usuario'),
            trans('users/lang._APELLIDOS_USUARIO'),
            trans('users/lang.email_usuario') ,
            trans('posts::admin_lang.visits'),
            trans('posts::admin_lang.primer_acceso'),
            trans('posts::admin_lang.ultimo_acceso')
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');

        $row++;

        // Ahora los registros
        $users = User::UserProfiles()
            ->select([
                'users.id',
                'user_profiles.first_name',
                'user_profiles.last_name',
                'users.email',
                'users.username',
                's.visits',
                's.created_at',
                's.updated_at'
            ])
            ->join('post_tracks as s', 'users.id', '=', 's.user_id')
            ->where('s.post_id', intval($id))
            ->orderBy('users.username', 'DESC')
            ->get();
        foreach ($users as $key => $value) {
            $fecha_ini = (new Carbon($value->created_at))->format('d/m/Y');
            $fecha_fin = (new Carbon($value->updated_at))->format('d/m/Y');

            $valores = array(
                $value->username,
                $value->first_name,
                $value->last_name,
                $value->email,
                $value->visits,
                $fecha_ini,
                $fecha_fin
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
        $file_name = trans('posts::admin_lang.exportar_usuarios')."_".Carbon::now()->format('YmdHis');
        $outPath = storage_path("app/exports/");
        if (!file_exists($outPath)) {
            mkdir($outPath, 0777, true);
        }
        $writer->save($outPath.$file_name.'.xlsx');

        // Redirect output to a client's web browser (Xlsx)
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
}
