<?php

namespace App\Exceptions;

/*
Revisar:

https://github.com/squareboat/sneaker
https://packagist.org/packages/abrigham/laravel-email-exceptions
https://stackoverflow.com/questions/33163893/laravel-5-send-errors-to-email
https://github.com/understand/understand-laravel - Ver el cliente del servicio
*/

use Exception;
use Throwable;
use Illuminate\Http\Response;
use App\Mail\ExceptionOccured;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        // log errors only in production mode and it's not http exception
        // if (env('APP_ENV') == 'production' && !$this->isHttpException($exception)) {
        //     if ($this->shouldReport($exception)) {
        //         $this->sendEmail($exception); // sends an email
        //     }
        // }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Personalizamos el error en función de donde estamos
        if ($this->isHttpException($exception)) {
            // Is front, admin or API?
            $type = "front";
            if ($request->is('admin/*')) {
                $type = "admin";
            } elseif ($request->is('api/*')) {
                $type = "api";
            }

            $status_code = $exception->getStatusCode();
            if ($type != "api") {
                // Vemos si tenemos customizadas las respuestas de error
                if (view()->exists($type.'.errors.'.$status_code)) {
                    /* 403 permission denied */
                    /* 404 not found */
                    /* 500 internal error */
                    return response()->view($type.'.errors.'.$status_code, [], $status_code);
                }
            } else {
                // detect instance
                if ($exception instanceof UnauthorizedHttpException) {
                    $data = "Unauthorized";

                    return response()->json([
                            'code' => $status_code,
                            'message' => $data
                        ], $status_code);
                }
            }
        } else {
            if ($request->is('api/*')) {
                if ($exception instanceof AuthorizationException || $exception instanceof AuthenticationException) {
                    $data = "Unauthorized";
                    return response()->json([
                            'code' => Response::HTTP_UNAUTHORIZED,
                            'message' => $data
                        ], Response::HTTP_UNAUTHORIZED);
                } else {
                    $data = "Error:".$exception->getMessage();
                    return response()->json([
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => $data
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                if ($exception instanceof AuthorizationException) {
                    app()->abort(403);
                }
            }
        }


        return parent::render($request, $exception);
    }

    /**
     * Sends an email to the developer about the exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function sendEmail(Throwable $exception)
    {
        try {
            $request= request();
            $data = [];

            $data['route'] = (Route::currentRouteAction()) ? Route::currentRouteAction() : "n/a";
            $data['action'] = (Route::currentRouteAction()) ? Route::currentRouteAction() : "n/a";
            $data['path'] = ($request->fullUrl()) ?  $request->fullUrl() : "n/a";
            //$data['user'] = (Auth::check()) ? Auth::user()->username : 'no login';
            $data['user'] = '?'; // Esto da error en produccion

            $data['method']    = $request->getMethod();
            $data['uri']    = $request->getUri();
            $data['ip']    = $request->getClientIp();
            $data['referer']    = $request->server('HTTP_REFERER');
            $data['isSecure']    = ($request->isSecure() ? 'True' : 'False');
            $data['isAjax']    = ($request->ajax() ? 'True' : 'False');
            $data['userAgent']    = $request->server('HTTP_USER_AGENT');
            $data['content']    =  nl2br(htmlentities($request->getContent()));



            $data['file']    = $exception->getFile();
            $data['code']    = $exception->getCode();
            $data['line']    = $exception->getLine();
            $data['message'] = $exception->getMessage();
            $data['trace']   = $exception->getTraceAsString();


            Mail::to('info@aduxia.com')
                 ->send(new ExceptionOccured($data));
        } catch (Exception $ex) {
            dd($ex);
        }
    }
}
