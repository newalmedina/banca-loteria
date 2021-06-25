<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontRegisterUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Mail;
use PDOException;

class FrontRegisterController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = trans("auth/front_register.registro");

        //return view('modules.auth.front_register', compact('page_title'));
        $user = new User;
        $user_profiles = new UserProfile();
        $user->setRelation('user_profile', $user_profiles);

        $form_data = array(
            'route' => array('register'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );

        return view(
            "modules.auth.front_register",
            compact(
                'page_title',
                'user',
                'form_data'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(FrontRegisterUserRequest $request)
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
            return redirect()->back()
                ->withInput()
                ->with('error', trans("auth/front_register.no_robot"));
        }


        $user = new User;

        // Obtenemos los datos enviada por el usuario
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->confirmed = false;
        $user->active = false;

        try {
            DB::beginTransaction();
            // Guardamos el usuario
            $user->push();

            if ($user->id) {
                $user_profile = new UserProfile;

                $user_profile->first_name = $request->input('first_name');
                $user_profile->last_name = $request->input('last_name');
                $user_profile->gender = 'male';
                $user_profile->user_lang = App::getLocale();
                $user_profile->confirmed = $request->input('terms');

                $user->userProfile()->save($user_profile);

                $roles = Role::where("name", "=", "usuario-front")->first();
                $a_roles = array();
                $a_roles[] = $roles->id;

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
                    'password' =>   env('APP_URL') . "/admin/password/reset",
                    /*
                    'linkedin' =>  trans("general/front_custom_lang.email.linkedin"),
                    'twitter' =>  trans("general/front_custom_lang.email.twitter"),
                    'facebook' =>  trans("general/front_custom_lang.email.facebook"),
                    'youtube' =>  trans("general/front_custom_lang.email.youtube"),
                    */


                ];


                Mail::send(
                    'modules.auth.front_register_email',
                    compact('user', 'payload'),
                    function ($message) use ($user) {
                        $message
                            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                            ->to($user->email, $user->email)
                            ->subject(trans('auth/front_register.email_account_confirmation'). env("APP_NAME"));
                    }
                );

                // Y Devolvemos una redirección a la acción show para mostrar el usuario
                return redirect()->route('register.saved', array("id" => $user->id));
            } else {
                // En caso de error regresa a la acción create con los datos y los errores encontrados
                return redirect()->back()
                    ->withInput($request->except('password'))
                    ->withErrors($user->errors);
            }
        } catch (PDOException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', 'Error en el registro de usuario ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', 'Error en el registro de usuario ' . $e->getMessage());
        }
    }

    public function saved(\Illuminate\Http\Request $request)
    {
        $page_title = trans("auth/front_register.register");
        $id =$request->input("id");
        $user = User::findOrFail($id);

        return view(
            'modules.auth.front_form_saved',
            compact('page_title', 'user')
        );
    }

    public function sendConfirmMail(\Illuminate\Http\Request $request)
    {
        $user = User::where('email', $request->input("email"))->first();

        if (empty($user)) {
            echo trans("auth/front_register.confirmacion_email_fill");
        } else {
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
                'password' =>   env('APP_URL') . "/admin/password/reset",
                /*'linkedin' =>  trans("general/front_custom_lang.email.linkedin"),
                'twitter' =>  trans("general/front_custom_lang.email.twitter"),
                'facebook' =>  trans("general/front_custom_lang.email.facebook"),
                'youtube' =>  trans("general/front_custom_lang.email.youtube"),*/


            ];


            Mail::send(
                'modules.auth.front_register_email',
                compact('user', 'payload'),
                function ($message) use ($user) {
                    $message
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                        ->to($user->email, $user->email)
                        ->subject(trans('auth/front_register.email_account_confirmation'). env("APP_NAME"));
                }
            );

            echo "OK";
        }
    }

    public function confirm($id)
    {
        $user = User::where(DB::Raw("md5(id)"), $id)->first();

        $user->confirmed = true;
        $user->save();

        return redirect()->route('register.confirmed');
    }

    public function confirmed()
    {
        $page_title = trans("auth/front_register.register");

        return view(
            'modules.auth.front_form_confirmed',
            compact('page_title')
        );
    }
}
