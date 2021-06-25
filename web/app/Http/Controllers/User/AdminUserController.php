<?php

namespace App\Http\Controllers\User;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Clavel\ExcelHelper;
use App\Services\UserCheckLoginPass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Requests\AdminUsersRequest;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Notification;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Notifications\UserCreationNotification;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class AdminUserController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-user" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-users';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-users-list')) {
            app()->abort(403);
        }

        $page_title = trans("users/lang.usuarios");

        return view("modules.users.admin_index", compact('page_title'))
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
        if (!auth()->user()->can('admin-users-create')) {
            app()->abort(403);
        }

        $page_title = trans("users/lang.crear_usuarios");

        $id = 0;

        // Mostramos la página
        return view('modules.users.admin_edit', compact('id', 'page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUsersRequest $request)
    {
        if (!Auth::user()->can('admin-users-create')) {
            abort(404);
        }

        // Creamos un nuevo objeto para nuestro nuevo usuario y su relación
        $user = new User;

        // Obtenemos la data enviada por el usuario
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        if ($request->input('confirmed')) {
            $user->confirmed = $request->input('confirmed');
        }
        if ($request->input('active')) {
            $user->active = $request->input('active');
        }

        try {
            DB::beginTransaction();
            // Guardamos el usuario
            $user->push();

            if ($user->id) {
                $userProfile = new UserProfile;

                $userProfile->first_name = $request->input('userProfile.first_name');
                $userProfile->last_name = $request->input('userProfile.last_name');
                $userProfile->gender = $request->input('userProfile.gender');
                $userProfile->user_lang = $request->input('userProfile.user_lang');

                $user->userProfile()->save($userProfile);

                $this->notificarAdminitradores($user);

                DB::commit();

                // Y Devolvemos una redirección a la acción show para mostrar el usuario
                return redirect()->route('users.edit', array($user->id))
                    ->with('success', trans('users/lang.okGuardado'));
            } else {
                DB::rollBack();
                // En caso de error regresa a la acción create con los datos y los errores encontrados
                return redirect()->route('users.create')
                    ->withInput($request->except('password'))
                    ->withErrors($user->errors);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('users.create')
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
        // Si no tiene permisos para modificar o visualizar lo echamos
        if (!auth()->user()->can('admin-users-update') && !auth()->user()->can('admin-users-read')) {
            app()->abort(403);
        }

        $page_title = trans("users/lang.modificar_usuarios");

        // Mostramos la página
        return view('modules.users.admin_edit', compact('id', 'page_title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUsersRequest $request, $id)
    {
        // Creamos un nuevo objeto para nuestro nuevo usuario
        $user = User::with('userProfile')->find($id);

        // Si el usuario no existe entonces lanzamos un error 404 :(
        if (is_null($user)) {
            app()->abort(500);
        }

        // Si la data es valida se la asignamos al usuario
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        if ($request->input('confirmed')) {
            $user->confirmed = $request->input('confirmed');
        }
        if ($request->input('active')) {
            $user->active = $request->input('active');
        }
        if (!empty($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->userProfile->first_name = $request->input('userProfile.first_name');
        $user->userProfile->last_name = $request->input('userProfile.last_name');
        $user->userProfile->gender = $request->input('userProfile.gender');
        $user->userProfile->user_lang = $request->input('userProfile.user_lang');

        try {
            DB::beginTransaction();

            // Guardamos el usuario
            if ($user->push()) {
                // Save roles. Handles updating.
                // $user->saveRoles($request->input('roles'));
            } else {
                // En caso de error regresa a la acción create con los datos y los errores encontrados
                return redirect()->route('admin.users.create')
                    ->withInput($request->except('password'))
                    ->withErrors($user->errors);
            }

            // Redirect to the new user page
            DB::commit();

            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect()->route('users.edit', array($user->id))
                ->with('success', trans('users/lang.okGuardado'));
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            return redirect()->route('users.edit')
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
        // Si no tiene permisos para borrar lo echamos
        if (!auth()->user()->can('admin-users-delete')) {
            app()->abort(403);
        }

        $user = User::find($id);

        if (is_null($user)) {
            app()->abort(500);
        }

        // Check if we are not trying to delete ourselves
        if ($user->id === auth()->user()->id) {
            // Redirect to the user management page
            return Response::json(array(
                'success' => false,
                'msg' => trans('users/lang.deleteimpossible'),
                'id' => $user->id
            ));
        }

        $user->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Usuario eliminado',
            'id' => $user->id
        ));
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-users-update')) {
            app()->abort(403);
        }

        $user = User::find($id);

        if (!is_null($user)) {
            $user->active = !$user->active;
            return $user->save() ? 1 : 0;
        }

        return 0;
    }

    public function getData()
    {
        $users = User::UserProfiles()
            ->select([
                'users.id',
                'users.active',
                'user_profiles.first_name',
                'user_profiles.last_name',
                'users.email',
                'users.username',
                'users.confirmed',
                'users.last_online_at',
            ])->getQuery();

        return Datatables::of($users)
            ->editColumn('active', function ($data) {
                return '<button class="btn ' . ($data->active ? "btn-success" : "btn-danger") . ' btn-sm" ' .
                    (auth()->user()->can("admin-users-update") ? "onclick=\"javascript:changeStatus('" .
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
            ->addColumn('online', function ($row) {
                $online = false;
                try {
                    $online = ($row->last_online_at > Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'));
                } catch (\Exception $ex) {
                }

                return '<div class="text-center">
                    <i class="fa fa-circle ' . ($online ? 'text-success' : 'text-danger') . '" aria-hidden="true"></i>
                    </div>';
            })
            ->addColumn('actions', function ($data) {
                $actions = '';
                if (!auth()->user()->can("admin-users-update") && auth()->user()->can("admin-users-read")) {
                    $actions .= '<button class="btn bg-purple btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/users/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.ver') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-search" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-users-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/users/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-users-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                        url('admin/users/' . $data->id) . '\');" data-content="' .
                        trans('general/admin_lang.borrar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                if (auth()->user()->can("admin-users-suplantar")) {
                    $actions .= '&nbsp;<button class="btn bg-aqua btn-sm" onclick="javascript:suplantarElement(\'' .
                        url('admin/users/suplantar/' . $data->id) . '\');" data-content="' .
                        trans('users/lang.suplantar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-user-secret" aria-hidden="true"></i></button>';
                }
                return $actions;
            })
            ->removeColumn('id')
            ->removeColumn('confirmed')
            ->removeColumn('last_online_at')
            ->rawColumns(['active', 'actions', 'online'])
            ->make();
    }

    public function getUserForm($id)
    {
        // Si nos viene un iduser lo buscamos y si no creamos uno nuevo para el formulario.
        if ($id == 0) {
            $user = new User;
            $userProfiles = new UserProfile();
            $user->setRelation('userProfile', $userProfiles);
            $form_data = array(
                'route' => array('users.store'), 'method' => 'POST',
                'id' => 'formData', 'class' => 'form-horizontal'
            );
        } else {
            $user = User::with('userProfile')->find($id);
            $form_data = array(
                'route' => array('users.update', $user->id), 'method' => 'PATCH',
                'id' => 'formData', 'class' => 'form-horizontal'
            );
        }

        // Si el user no se ha cargado correctamente, devolvemos un error
        if (is_null($user)) {
            app()->abort(500);
        }

        return view('modules.users.admin_edit_form', compact('id', 'user', 'form_data'));
    }

    public function checkLoginExists(Request $request)
    {
        $check = new UserCheckLoginPass($request->get('user_id'), $request->get('login'));
        return $check->existUserLoginService();
    }

    public function generatePassword()
    {
        $check = new UserCheckLoginPass();
        return $check->generatePassword();
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
            ->setTitle(trans('users/lang.listado_de_usuarios'))
            ->setSubject(trans('users/lang.listado_de_usuarios'))
            ->setDescription(trans('users/lang.listado_de_usuarios'))
            ->setKeywords(trans('users/lang.listado_de_usuarios'))
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

        $sheet->getHeaderFooter()->setOddHeader(trans('users/lang.usuarios_excel'));
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('users/lang.identificador'),
            trans('users/lang._NOMBRE_USUARIO'),
            trans('users/lang._APELLIDOS_USUARIO'),
            trans('users/lang._EMAIL_USUARIO'),
            trans('users/lang._genero_sexusal'),
            trans('users/lang.usuario'),
            trans('users/lang.roles'),
            trans('users/lang._ACTIVAR_USUARIO_USUARIO_ESTA'),
            trans('users/lang._CONFIRMAR_USUARIO_USUARIO_ESTA'),
            trans('users/lang.fecha_registro')
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');

        $row++;

        // Ahora los registros
        $users = User::with('userProfile')->get();
        foreach ($users as $key => $value) {
            $fisrt_name = $value->userProfile->first_name;
            $last_name = $value->userProfile->last_name;
            $roles_usuario = $value->roles->pluck('name')->toArray();

            $valores = array(
                $value->id,
                $fisrt_name,
                $last_name,
                $value->email,
                ($value->userProfile->gender == 'male') ? trans('users/lang.hombre') : trans('users/lang.mujer'),
                $value->username,
                implode(", ", $roles_usuario),
                ($value->active == '1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no'),
                ($value->confirmed == '1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no'),
                $value->created_at->format('d/m/Y')
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

    protected function notificarAdminitradores(User $user)
    {
        $admins = User::withRole('admin')->get();
        foreach ($admins as $admin) {
            Notification::send($admin, new UserCreationNotification($user));
        }
    }

    public function getUserStats()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-users-list')) {
            app()->abort(403);
        }

        $total = User::count();
        $nuevos = User::where('created_at', '>=', new DateTime('-1 months'))->count();
        $activos = User::where('last_online_at', '>=', new DateTime('-1 hour'))->count();

        return [
            "total" => $total,
            "nuevos" => $nuevos,
            "activos" => $activos
        ];
    }
}
