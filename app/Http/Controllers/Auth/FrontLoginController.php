<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class FrontLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Con dos intentos bloqueamos el login
     *
     * @var string
     */
    protected $maxAttempts = 2;


    /**
     * Tiempo de bloqueo en minutos
     *
     * @var string
     */
    protected $decayMinutes = 2;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * El usuario será el username. Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Override the credentials to be sure that the user is active and confirmed
     *
     * @return string
     */
    public function credentials(Request $request)
    {
        // We control if user is active
        return array_merge($request->only($this->username(), 'password'), ['active' => 1], ['confirmed' => 1]);
    }


    /**
     * Sobreescribimos el formulario clasico de Laravel y su localización
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $page_title = trans("general/front_lang.login");
        return view('modules.auth.front_login', compact('page_title'));
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);
    }
}
