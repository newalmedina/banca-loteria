<?php

namespace Clavel\Elearning\Controllers\User;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Clavel\Elearning\Models\Codigo;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Clavel\Elearning\Models\Municipio;
use Clavel\Elearning\Models\Provincia;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Clavel\Elearning\Models\Especialidad;
use Clavel\Elearning\Requests\UsersRequest;

class FrontUserController extends Controller
{
    public function getIndex(Request $request, $ajax = 0)
    {
        $page_title = trans("elearning::general/front_lang.registro");

        $codigo = Codigo::where("codigo", '=', $request->input("codigo"))->active()->first();
        $codigo_id = "";
        if (!empty($codigo)) {
            $codigo_id = md5($codigo->id.$codigo->codigo) ."|". $codigo->codigo;
        }

        //Obtengo la información del usuario para pasarsela al formulario
        $user = new User;
        $user_profiles=new UserProfile();
        $user->setRelation('user_profile', $user_profiles);
        $provincias = Provincia::all();
        $municipios = Municipio::all();
        $especialidades = Especialidad::all();

        $viewtemplate = ($ajax=='0') ? "front_form" : "front_form_ajax";

        return view(
            "elearning::users.".$viewtemplate,
            compact(
                'page_title',
                'user',
                'provincias',
                'municipios',
                'especialidades',
                'codigo_id'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function postIndex(UsersRequest $request)
    {
        $recaptcha = $request->get("g-recaptcha-response", "");

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'secret' => env('RECAPTCHA_SITE_KEY', ''),
                'response' => $recaptcha,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $output = curl_exec($ch);
        curl_close($ch);
        $captcha_success = json_decode($output);
        if (!$captcha_success->success) {
            // Eres un robot
            return redirect('usuarios/registro')
                ->with('error', trans('users/lang.errorediciion'));
        }

        $user = new User;

        // Obtenemos la data enviada por el usuario
        $user->username = empty($request->input('username')) ? $request->input('email') : $request->input('username');
        // $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->confirmed = (config("elearning.autentificacion.EMAIL_CONFIRMACION")) ? 0 : 1;
        $user->active = 1;

        try {
            DB::beginTransaction();
            // Guardamos el usuario
            $user->push();

            if ($user->id) {
                $user_profile = new UserProfile;

                $user_profile->first_name = $request->input('user_profile.first_name');
                $user_profile->last_name = $request->input('user_profile.last_name');
                $user_profile->confirmed = '1';
                //  $user_profile->gender = $request->input('user_profile.gender', 'male');
                $user_profile->user_lang = App::getLocale();

                $user_profile->nif = $request->input('nif');
                $user_profile->provincia_id = $request->input('provincia');
                $user_profile->municipio_id = $request->input('municipio');
                $user_profile->centro = $request->input('centro');
                //  $user_profile->especialidad_id = $request->input('especialidad');
                //  $user_profile->especialidad_otra = $request->input('especialidad_otra');

                $user_profile->consentimiento = $request->input('user_profile.consentimiento', 0);

                //  $birthdate = $request->input('birthdate', '');
                /* if (!empty($birthdate)) {
                     if (preg_match("/^\d{1,2}\/\d{1,2}\/(\d{2}|\d{4})$/", $birthdate)) {
                         $user_profile->birthdate = Carbon::createFromFormat("d/m/Y", $birthdate);
                     } elseif (preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $birthdate)) {
                         $user_profile->birthdate = $birthdate;
                     }
                 }
 */                $user->userProfile()->save($user_profile);

                $roles = Role::where("name", "=", "usuario-front")->first();
                $a_roles = array();
                $a_roles[] = $roles->id;

                // Vemos si viene con codigo y verificamos si existe y es valido
                $codigo_id = $request->input('codigo_id', '');
                $user->codigo_id = null;
                if (config("elearning.autentificacion.TIPO_REGISTRO")=='2') {
                    $errorCodigo = true;
                    // Es requerido el código
                    if (!empty($codigo_id)) {
                        // El codigo viene encriptado y concatenado para evitar phising
                        // md5($codigo->id.$codigo->codigo) un '|' y el código
                        $codigoParam = explode("|", $codigo_id);
                        if (sizeof($codigoParam)==2) {
                            // Buscamos el código en crudo
                            $codigo = Codigo::where('codigo', [$codigoParam[1]])->first();
                            // Verificamos que lo hemos encontrado y coincide con el md5
                            if (!empty($codigo) || md5($codigo->id.$codigo->codigo) == $codigoParam[0]) {
                                // Guardamos el código encontrado y le asignamos los roles que tiene
                                $user->codigo_id = $codigo->id;
                                if ($codigo->roles()->count() > 0) {
                                    $a_roles = array();
                                }
                                foreach ($codigo->roles as $role) {
                                    $a_roles[] = $role->id;
                                }

                                // Tenemos código y todo es correcto
                                $errorCodigo = false;
                            }
                        }
                    }

                    // Si hay error => Rollback y volvemos
                    if ($errorCodigo) {
                        DB::rollBack();
                        return redirect('usuarios/registro')
                            ->with('error', trans('users/lang.error_codigo'));
                    }
                }

                $user->save();
                $user->roles()->sync([]);
                $user->roles()->attach($a_roles);

                DB::commit();

                $payload = [
                    'to' => $user->email,
                    'senderName' => trans("general/front_custom_lang.email.sender_name"),
                    'css' => "",
                    'logo' => [
                        'path' => config('app.url') . trans("general/front_custom_lang.email.logo"),
                        'width' => trans("general/front_custom_lang.email.logo_width"),
                        'height' => trans("general/front_custom_lang.email.logo_height")
                    ],
                    'address' =>   trans("general/front_custom_lang.email.address"),
                    'reminder' =>  trans("general/front_custom_lang.email.reminder", [ 'url' => config('app.url')]),
                    /*'unsubscribe' =>  trans("general/front_custom_lang.email.unsubscribe",
                    [ 'url' => config('app.url')]),*/
                    //'password' =>   env('APP_URL') . "/password/reset",
                    /*'linkedin' =>  trans("general/front_custom_lang.email.linkedin"),
                    'twitter' =>  trans("general/front_custom_lang.email.twitter"),
                    'facebook' =>  trans("general/front_custom_lang.email.facebook"),
                    'youtube' =>  trans("general/front_custom_lang.email.youtube"),
                    */
                ];

                if (config("elearning.autentificacion.EMAIL_CONFIRMACION")) {
                    Mail::send('elearning::users.email', compact('user', 'payload'), function ($message) use ($user) {
                        $message
                            ->to($user->email, $user->email)
                            ->subject(trans('users/lang.email_account_confirmation') . env("APP_NAME"));
                    });
                }

                // Y Devolvemos una redirección a la acción show para mostrar el usuario
                return Redirect::route('usuarios.registro.saved', array("id" => $user->id));
            } else {
                DB::rollBack();

                // En caso de error regresa a la acción create con los datos y los errores encontrados
                return redirect('usuarios/registro')
                    ->withInput($request->except('password'))
                    ->withErrors($user->errors);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('usuarios/registro')
                ->with('error', trans('users/lang.errorediciion'));
        }
    }

    public function saved(Request $request)
    {
        $page_title = trans("elearning::general/front_lang.registro");
        $id = $request->input("id");
        $user = User::findOrFail($id);

        return view("elearning::users.front_form_saved", compact('page_title', 'user'));
    }

    public function checkCode(Request $request)
    {
        $codigo = Codigo::where("codigo", '=', $request->input("code"))->active()->first();

        if (empty($codigo)) {
            return "NOK";
        }

        $payload = ['id' => md5($codigo->id.$codigo->codigo), 'codigo' => $codigo->codigo];
        if ($codigo->ilimitado=='1') {
            return response()->json($payload);
        }
        return ($codigo->users()->count() > 0) ? "NOK" : response()->json($payload);
    }

    public function confirmar($id)
    {
        $user = User::where(DB::Raw("md5(id)"), $id)->first();

        $user->confirmed = '1';
        $user->save();

        return Redirect::route('usuarios.registro.confirmed');
    }

    public function sendConfirmarMail(Request $request)
    {
        $user = User::where('email', $request->input("email"))->first();

        $payload = [
            'to' => $user->email,
            'senderName' => trans("general/front_custom_lang.email.sender_name"),
            'css' => "",
            'logo' => [
                'path' => config('app.url') . trans("general/front_custom_lang.email.logo"),
                'width' => trans("general/front_custom_lang.email.logo_width"),
                'height' => trans("general/front_custom_lang.email.logo_height")
            ],
            'address' =>   trans("general/front_custom_lang.email.address"),
            'reminder' =>  trans("general/front_custom_lang.email.reminder", [ 'url' => config('app.url')]),
            /*'unsubscribe' =>  trans("general/front_custom_lang.email.unsubscribe", [ 'url' => config('app.url')]),*/
            //'password' =>   env('APP_URL') . "/password/reset",
            /*'linkedin' =>  trans("general/front_custom_lang.email.linkedin"),
            'twitter' =>  trans("general/front_custom_lang.email.twitter"),
            'facebook' =>  trans("general/front_custom_lang.email.facebook"),
            'youtube' =>  trans("general/front_custom_lang.email.youtube"),
            */

        ];


        if (is_null($user) || empty($user)) {
            echo trans("users/lang.no_exite_el_correo");
        } else {
            $response = Mail::send(
                'elearning::users.email',
                compact('user', 'payload'),
                function ($message) use ($user) {
                    $message
                        ->to($user->email, $user->email)
                        ->subject(trans('users/lang.email_account_confirmation').env("APP_NAME"));
                }
            );

            echo "OK";
        }
    }

    public function confirmed()
    {
        $page_title = trans("elearning::general/front_lang.registro");

        return view("elearning::users.front_form_confirmed", compact('page_title'));
    }

    public function updateFields($field, $id)
    {
        $municipios = [];
        $csalud = [];
        switch ($field) {
            case 'provincia':
                $municipios = DB::table('municipios')->where($field . "_id", $id)->get();
                break;
            case 'municipio':
                $csalud = DB::table('csalud')->where($field . "_id", $id)->get();
                break;
        }

        return view("elearning::users.ajax.select_options", compact('municipios', 'csalud'));
    }
}
