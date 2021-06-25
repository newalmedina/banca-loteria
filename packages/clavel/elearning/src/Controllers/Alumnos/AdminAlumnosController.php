<?php

namespace Clavel\Elearning\Controllers\Alumnos;

use App\Http\Controllers\AdminController;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\StoragePathWork;
use Carbon\Carbon;
use Clavel\Elearning\Models\Alumno;
use Clavel\Elearning\Models\Grupo;
use Clavel\Elearning\Requests\AdminAlumnoCreateRequest;
use Clavel\Elearning\Requests\AdminAlumnoUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ExcelHelper;

class AdminAlumnosController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-address-book-o" aria-hidden="true"></i>';
    private $grupos;

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-alumnos';

        $this->middleware(function ($request, $next) {
            $this->grupos =  ($request->session()->has('altas_grupos')) ?
                ($request->session()->get('altas_grupos')) :
                "";
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = trans('elearning::alumnos/admin_lang.alumnos');

        if (auth()->user() != null && (!auth()->user()->can('admin-alumnos'))) {
            abort(404);
        }

        $grupos = Grupo::active()->select('grupos.id', 'grupos.nombre');

        // Cargamos los grupos a los que pertenezco para realizar la importación
        if (auth()->user()->can("admin-alumnos-all")) {
            $grupos = $grupos->get();
        } else {
            $grupos = $grupos->join("grupo_profesor", "grupos.id", "=", "grupo_profesor.grupo_id")
                ->where("grupo_profesor.user_id", "=", auth()->user()->id)->get();
        }

        return view("elearning::alumnos.admin_index", compact(
            'page_title',
            'grupos'
        ))
            ->with('page_title_icon', $this->page_title_icon)
            ->with([
                'grupos_seleccionados' => ($this->grupos != '') ? $this->grupos : array()
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-alumnos-create')) {
            app()->abort(403);
        }

        $page_title = trans('elearning::alumnos/admin_lang.crear');

        $user = new User();
        $form_data = array(
            'route' => array(
                'admin.alumnos.store'
            ),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );

        $grupos = $this->getGruposAuthUser();

        return view("elearning::alumnos.admin_edit", compact(
            'user',
            'page_title',
            'form_data',
            'grupos'
        ))
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminAlumnoCreateRequest $request)
    {
        if (!Auth::user()->can('admin-alumnos-create')) {
            abort(404);
        }

        // Creamos un nuevo objeto para nuestro nuevo usuario y su relación
        $user = new User;

        // Obtenemos la data enviada por el usuario
        $user->username = $request->input('username', '');
        $user->email = $request->input('email', '');
        $user->password = Hash::make($request->input('password'));
        $user->confirmed = true;
        $user->active = $request->input('active', false);

        try {
            DB::beginTransaction();
            // Guardamos el usuario
            $user->push();

            if ($user->id) {
                $userProfile = new UserProfile;

                $userProfile->first_name = $request->input('userProfile.first_name', '');
                $userProfile->last_name = $request->input('userProfile.last_name', '');

                $birthdate = $request->input('userProfile.birthdate', '');
                $birt = Carbon::createFromFormat("d/m/Y", $birthdate)->format('Y-m-d');
                $userProfile->birthdate = $birt;

                $userProfile->gender = $request->input('userProfile.gender', 'male');
                $userProfile->user_lang = $request->input('userProfile.user_lang', '');

                $user->userProfile()->save($userProfile);

                // Le asignamos el role de usuario de front = alumno
                $role = Role::where('name', 'usuario-front')->first();
                $user->syncRoles([$role->id]);

                DB::commit();

                $sel_grupos = $request->input('sel_grupos');
                $user->grupo_pivot()->detach();
                if (!is_null($request->input('sel_grupos'))) {
                    $user->grupo_pivot()->sync($sel_grupos);
                }

                // Y Devolvemos una redirección a la acción show para mostrar el usuario
                return redirect('admin/alumnos/' . $user->id . "/edit")
                    ->with('success', trans('general/admin_lang.save_ok'));
            } else {
                DB::rollBack();
                // En caso de error regresa a la acción create con los datos y los errores encontrados
                return redirect('admin/alumnos/create')
                    ->withInput($request->except('password'))
                    ->withErrors($user->errors);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('admin/alumnos/create')
                ->with('error', trans('users/lang.errorediciion'));
        }
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
        if (!Auth::user()->can('admin-alumnos-update')) {
            abort(404);
        }

        $user = User::findOrFail($id);

        $form_data = array(
            'route' => array('admin.alumnos.update', $user->id),
            'method' => 'PATCH', 'id' => 'formData', 'class' => 'form-horizontal'
        );
        $page_title = trans("elearning::alumnos/admin_lang.editar");

        $grupos = $this->getGruposAuthUser();

        return view('elearning::alumnos.admin_edit', compact(
            'page_title',
            'user',
            'form_data',
            'grupos'
        ))
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminAlumnoUpdateRequest $request, $id)
    {
        if (!Auth::user()->can('admin-alumnos-update')) {
            abort(404);
        }

        // Creamos un nuevo objeto para nuestro nuevo usuario y su relación
        $user = User::with('userProfile')->find($id);

        // Si el usuario no existe entonces lanzamos un error 404 :(
        if (is_null($user)) {
            app()->abort(404);
        }

        try {
            // Obtenemos la data enviada por el usuario
            $user->username = $request->input('username', '');
            $user->email = $request->input('email', '');
            if (!empty($request->input('password'))) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->confirmed = true;
            $user->active = $request->input('active', false);

            $user->userProfile->first_name = $request->input('userProfile.first_name', '');
            $user->userProfile->last_name = $request->input('userProfile.last_name', '');
            $birthdate = $request->input('userProfile.birthdate', '');
            $birt = Carbon::createFromFormat("d/m/Y", $birthdate)->format('Y-m-d');
            $user->userProfile->birthdate = $birt;

            $user->userProfile->gender = $request->input('userProfile.gender', 'male');
            $user->userProfile->user_lang = $request->input('userProfile.user_lang', '');

            DB::beginTransaction();
            // Guardamos el usuario
            $user->push();

            DB::commit();

            $sel_grupos = $request->input('sel_grupos');
            $user->grupo_pivot()->detach();
            if (!is_null($request->input('sel_grupos'))) {
                $user->grupo_pivot()->sync($sel_grupos);
            }

            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect('admin/alumnos/' . $user->id . "/edit")
                ->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('admin/alumnos/' . $user->id . '/edit')
                ->with('error', trans('users/lang.errorediciion'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('admin-alumnos-delete')) {
            abort(404);
        }

        $user = User::findOrFail($id);
        if (is_null($user)) {
            abort(404);
        }
        $user->delete();

        return Response::json(array(
            'success' => true,
            'msg' => trans('elearning::alumnos/admin_lang.registro_borrado'),
            'id' => $user->id
        ));
    }

    public function getData()
    {
        $misAlumnosSession = (Session::has('todo_los_alumnos')) ? true : false;

        // Seleccionamos los alumnos. Tienen el role 'usuario-front'
        $users = User::UserProfiles()
            ->distinct()
            ->select([
                'users.id',
                'users.active',
                'user_profiles.first_name',
                'user_profiles.last_name',
                'users.email',
                'users.username',
                'users.confirmed'
            ])
            ->withRole('usuario-front');

        if (!auth()->user()->can("admin-alumnos-all") || !$misAlumnosSession) {
            $users->join('grupo_users', 'grupo_users.user_id', "=", "users.id")
                ->join('grupo_profesor', "grupo_profesor.grupo_id", "=", "grupo_users.grupo_id")
                ->where("grupo_profesor.user_id", auth()->user()->id);
        }

        if ($this->grupos != '') {
            $users
                ->join('grupo_users', 'grupo_users.user_id', "=", "users.id")
                ->whereIn("grupo_users.grupo_id", $this->grupos);
        }

        return Datatables::of($users)
            ->editColumn('active', function ($data) {
                return '<button class="btn ' . ($data->active ? "btn-success" : "btn-danger") . ' btn-sm" ' .
                    (auth()->user()->can("admin-alumnos-update") ? "onclick=\"javascript:changeStatus('" .
                        url('admin/users/state/' . $data->id) . "');\"" : "") . '
                        data-content="' . ($data->active ?
                        trans('general/admin_lang.descativa') :
                        trans('general/admin_lang.activa')) . '"
                        data-placement="right" data-toggle="popover">
                        <i class="fa ' . ($data->active ? "fa-eye" : "fa-eye-slash") . '" aria-hidden="true"></i>
                        </button>';
            })
            ->editColumn('first_name', function ($row) {
                return $row->first_name;
            })
            ->editColumn('last_name', function ($row) {
                return $row->last_name;
            })
            ->editColumn('grupos', function ($row) {
                $grupo = new Grupo();
                $grupos_usuario = $grupo->userGrupos($row->id)->pluck('nombre');
                $mostrar_grupos = '';
                foreach ($grupos_usuario as $nombre_grupo) {
                    $mostrar_grupos .= '<span class="text-center" style="padding:3px;"><a href="javascript:void(0);"
                    data-id="' .  $row->id . '"
                    class="acciones_grupos">
                    <span class="label label-primary">' . $nombre_grupo . '
                    </span></span>';
                }
                return $mostrar_grupos;
            })
            ->editColumn('actions', function ($data) {
                $actions = '';
                $actions .= '<button class="btn bg-yellow btn-sm"
                    onclick="javascript:window.location=\'' . url('admin/profesor/user-stats/' . $data->id) . '\';"
                    data-content="' . trans('elearning::profesor/admin_lang.stats') . '"
                    style="margin-right: 3px;"
                    data-placement="left"
                    data-toggle="popover">
                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                    </button>';

                $actions .= '<button class="btn bg-aqua btn-sm"
                    onclick="javascript:window.location=\'' . url('admin/alumnos-directory/' . $data->id) . '\';"
                    data-content="' . trans('elearning::alumnos/admin_lang.folder') . '"
                    style="margin-right: 3px;"
                    data-placement="left"
                    data-toggle="popover">
                    <i class="fa fa-folder" aria-hidden="true"></i>
                    </button>';

                if (!auth()->user()->can("admin-alumnos-update") && auth()->user()->can("admin-alumnos-read")) {
                    $actions .= '<button class="btn bg-purple btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/alumnos/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.ver') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-search" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-alumnos-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/alumnos/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-alumnos-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                        url('admin/alumnos/' . $data->id) . '\');" data-content="' .
                        trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                return $actions;
            })
            ->removeColumn('id')
            ->removeColumn('confirmed')
            ->rawColumns(['active', 'actions', 'grupos'])
            ->make();
    }

    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-alumnos-update')) {
            abort(404);
        }

        $user = User::findOrFail($id);

        if (!is_null($user)) {
            $user->activo = !$user->activo;
            return $user->save() ? 1 : 0;
        }

        return 0;
    }

    public function setListado()
    {
        if (Session::has('todo_los_alumnos')) {
            Session::forget('todo_los_alumnos');
        } else {
            Session::put('todo_los_alumnos', '1');
        }

        echo "ok";
    }

    public function importAlumnos(Request $request)
    {
        $myServiceSPW = new StoragePathWork("alumnos");
        $file = $request->file('plantilla');
        $grupo_id = $request->input('group_id', '');
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


            $role = Role::where('name', 'usuario-front')->first();
            $grupo = Grupo::where('id', $grupo_id)->first();

            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            if (!empty($sheetData) && sizeof($sheetData) > 1) {
                for ($i = 1; $i < sizeof($sheetData); $i++) {
                    $line = $sheetData[$i];
                    $nombre = $line[0];
                    $apellidos = $line[1];
                    $email = $line[2];
                    $genero = $line[3];
                    $username = $line[4];

                    try {
                        $user = User::where("username", $username)
                            ->orWhere("email", $email)
                            ->first();
                        if (empty($user)) {
                            // Creamos un nuevo objeto para nuestro nuevo usuario y su relación
                            DB::beginTransaction();
                            $user = new User;

                            // Obtenemos la data enviada por el usuario
                            $user->username = $username;
                            $user->email = $email;
                            $user->password = Hash::make($email);
                            $user->confirmed = true;
                            $user->active = true;
                            // Guardamos el usuario
                            $user->push();

                            if ($user->id) {
                                $userProfile = new UserProfile;

                                $userProfile->first_name = $nombre;
                                $userProfile->last_name = $apellidos;

                                $gender = array("hombre", "home", "man", "male", "h");
                                $userProfile->gender = in_array(strtolower($genero), $gender) ? 'male' : 'female';
                                $userProfile->user_lang = App::getLocale();

                                $user->userProfile()->save($userProfile);

                                // Le asignamos el role de usuario de front = alumno
                                $user->syncRoles([$role->id]);

                                // Ahora lo incluimos en el grupo que toca
                                if (!empty($grupo)) {
                                    $grupo->userPivot()->syncWithoutDetaching($user->id);
                                }


                                DB::commit();
                            }
                        } else {
                            $res["existentes"][] = $nombre . " " . $apellidos . " - " . $username . "(" . $email . ")";
                        }
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }
            }
            $res["result"] = true;

            $myServiceSPW->deleteFile($file, '');
        }

        return response()->json($res);
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
            ->setTitle(trans('elearning::alumnos/admin_lang.listado_alumnos'))
            ->setSubject(trans('elearning::alumnos/admin_lang.listado_alumnos'))
            ->setDescription(trans('elearning::alumnos/admin_lang.listado_alumnos'))
            ->setKeywords(trans('elearning::alumnos/admin_lang.listado_alumnos'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('users/lang.usuarios_excel'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader(trans('elearning::alumnos/admin_lang.listado_alumnos'));
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
            trans('users/lang.identificador'),
            trans('users/lang._NOMBRE_USUARIO'),
            trans('users/lang._APELLIDOS_USUARIO'),
            trans('users/lang.edad'),
            trans('users/lang._genero_sexusal'),
            trans('users/lang._EMAIL_USUARIO'),
            trans('users/lang.usuario'),
            trans('users/lang._ACTIVAR_USUARIO_USUARIO_ESTA'),
            trans('users/lang._CONFIRMAR_USUARIO_USUARIO_ESTA'),
            trans('elearning::alumnos/admin_lang.grupos')
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');
        $row++;

        // Ahora los registros
        $misAlumnosSession = (Session::has('todo_los_alumnos')) ? true : false;

        $users = User::UserProfiles()
            ->distinct()
            ->select([
                'users.id',
                'user_profiles.first_name',
                'user_profiles.last_name',
                'users.email',
                'users.username',
                'users.confirmed',
                'user_profiles.birthdate',
                'user_profiles.gender',
                'users.active'
            ])
            ->withRole('usuario-front');

        if (!auth()->user()->can("admin-alumnos-all") || !$misAlumnosSession) {
            $users->join('grupo_users', 'grupo_users.user_id', "=", "users.id")
                ->join('grupo_profesor', "grupo_profesor.grupo_id", "=", "grupo_users.grupo_id")
                ->where("grupo_profesor.user_id", auth()->user()->id);
        }

        if ($this->grupos != '') {
            $users->join('grupo_users', 'grupo_users.user_id', "=", "users.id")
                ->whereIn("grupo_users.grupo_id", $this->grupos);
        }

        $grupo = new Grupo();

        foreach ($users->get() as $key => $value) {
            $edad = !empty($value->birthdate) ?
                Carbon::createFromFormat('Y-m-d', $value->userProfile->birthdate)->age :
                '';
            $grupos_usuario = $grupo->userGrupos($value->id)->pluck('nombre')->toArray();

            $valores = array(
                $value->id,
                $value->first_name,
                $value->last_name,
                $edad,
                ($value->gender == 'male') ? trans('users/lang.hombre') : trans('users/lang.mujer'),
                $value->email,
                $value->username,
                ($value->active == '1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no'),
                ($value->confirmed == '1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no'),
                implode(", ", $grupos_usuario)
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

        $file_name = trans('users/lang.listado_de_usuarios') . "_" . Carbon::now()->format('YmdHis');
        $outPath = storage_path("app/exports/");
        ExcelHelper::downloadFile($spreadsheet, $file_name, $outPath);
    }

    public function getGrupos(Request $request)
    {
        $user_id = $request->orden;
        $grupos = Grupo::active()->select('grupos.id', 'grupos.nombre');

        // Cargamos los grupos a los que pertenezco para realizar la importación
        if (auth()->user()->can("admin-alumnos-all")) {
            $grupos = $grupos->get();
        } else {
            $grupos = $grupos->join("grupo_profesor", "grupos.id", "=", "grupo_profesor.grupo_id")
                ->where("grupo_profesor.user_id", "=", auth()->user()->id)->get();
        }

        $alumno = User::findOrFail($user_id)->userProfile()->first();

        return view('elearning::alumnos.admin_partials.admin_grupos', compact(
            'grupos',
            'alumno'
        ))->with('user_id', $user_id);
    }

    public function storeGrupos(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        $sel_grupos = $request->input('sel_grupos');

        $user->grupo_pivot()->detach();
        if (!is_null($request->input('sel_grupos'))) {
            $user->grupo_pivot()->sync($sel_grupos);
        }

        return 'OK';
    }

    private function getGruposAuthUser()
    {
        $grupos = Grupo::active()->select('grupos.id', 'grupos.nombre');
        // Cargamos los grupos a los que pertenezco para realizar la importación
        if (auth()->user()->can("admin-alumnos-all")) {
            $grupos = $grupos->get();
        } else {
            $grupos = $grupos->join("grupo_profesor", "grupos.id", "=", "grupo_profesor.grupo_id")
                ->where("grupo_profesor.user_id", "=", auth()->user()->id)->get();
        }
        return $grupos;
    }

    public function saveFilter(Request $request)
    {
        $request->session()->forget('altas_grupos');
        if (!is_null($request->input('grupos')) && $request->input('grupos') != '') {
            $request->session()->put('altas_grupos', $request->input("grupos"));
        }
        return redirect("admin/alumnos");
    }

    public function clearFilter(Request $request)
    {
        $request->session()->forget('altas_grupos');
        return redirect("admin/alumnos");
    }
}
