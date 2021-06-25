<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiController extends Controller
{
    const APITOKEN = "API_ACCESS_TOKEN";

    public function __construct()
    {
    }

    /**
     * Recupera el token actual de la conexión y lo regenera si tenemos configurado que se
     * debe regenerar cada vez
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken(Request $request)
    {
        if (env("REGENERATE_TOKEN_EVERY_TIME", false)) {
            // Destruimos el token anterior
            $token = $request->user()->token();
            $token->revoke();

            // Lo volvemos a crear
            $token = auth()->user()->createToken(self::APITOKEN)->accessToken;
            return $token;
        } else {
            return $request->bearerToken();
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $expires_at = Carbon::now()->addWeeks(1);

        return response()->json([
            'token' => $token,
            'expires_in' => Carbon::parse($expires_at)->toDateTimeString()
        ], Response::HTTP_OK);
    }


    protected function respondWithCode($code)
    {
        $message = "";

        switch ($code) {
            case Response::HTTP_UNAUTHORIZED: // 401
                $message = "Unauthorized";
                break;
            case Response::HTTP_FORBIDDEN: // 403
                $message = "Authentication is valid, but the user has not permissions to accomplish the operation";
                break;
            case Response::HTTP_NOT_FOUND: // 404
                $message = "Not Found";
                break;
            case Response::HTTP_UNPROCESSABLE_ENTITY: // 422
                $message = "Input data is not valid. We will define a more appropiate schema model";
                break;
            case Response::HTTP_INTERNAL_SERVER_ERROR: // 500
                $message = "Internal Server Error";
                break;
        }
        return response()->json([
            'code' => $code,
            'message' => $message
        ], $code);
    }

    protected function respondWithCustom($status, $code, $message)
    {
        return response()->json([
            'code' => $code,
            'message' => $message
        ], $status);
    }

    /**
     * get access token from header
     * */
    protected function getBearerToken($headers)
    {
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
