<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api
 */
class AuthController extends ApiController
{

    /**
     * @OA\Post(
     *     path="/api/v1/auth/signin",
     *     tags={"Authentication"},
     *     summary="Signin",
     *     description="User's credentials",
     *     operationId="signin",
     *     @OA\RequestBody(
     *         description="User's credentials",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email","password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password"
     *                 ),
     *                 example={"email": "info@aduxia.com", "password": "secret"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication token",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  allOf={
     *                      @OA\Schema(ref="#/components/schemas/User"),
     *                      @OA\Schema(
     *                          required={"token"},
     *                          @OA\Property(
     *                              property="token",
     *                              type="string"
     *                          )
     *                      )
     *                  }
     *              )
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */


    public function signin(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $rules = [
            'email' => 'required|string|email|max:255',
            'password' => 'required|min:5',
        ];

        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
        }

        try {
            // Attempt to verify the credentials and create a token for the user
            if (!auth()->attempt($credentials)) {
                return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
            }

            // Verificamos que el usuario sea un paciente
            if (!auth()->user()->hasRole(['admin', 'usuario-api'])) {
                return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
            }

            $token = auth()->user()->createToken(self::APITOKEN)->accessToken;
        } catch (\Exception $e) {
            // Something went wrong.
            return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // All good so return the token
        //return $this->respondWithToken($token);
        $user_data = [];
        $user =  auth()->user();
        $user_profile =$user->userProfile;
        $user_data['id']=$user->id;
        $user_data['name']=$user_profile->first_name;
        $user_data['surname']=$user_profile->last_name;
        $user_data['email']=$user->email;
        $user_data['province']=$user_profile->province;
        $user_data['zip_code']=$user_profile->cp;
        $user_data['token']=$token;

        $headers = ['X-Authorization-Token' => "{$token}"];
        return response()->json(
            $user_data,
            Response::HTTP_OK,
            $headers
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/signout",
     *     tags={"Authentication"},
     *     summary="Signout",
     *     description="Invalidate authentication token",
     *     operationId="signout",
     *     security={{"Authorization":{}}},
     *     @OA\Response(
     *         response=204,
     *         description="Empty response",
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    public function signout(Request $request)
    {
        // Invalidate the token
        try {
            // Get Token from the request header key "Authentication"
            $token = $request->user()->token();
            $token->revoke();

            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            // something went wrong whilst attempting to encode the token
            return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/auth/need-reset-password",
     *     tags={"Authentication"},
     *     summary="Retrieve if the user needs to reset his password",
     *     description="Retrieve if the user needs to reset his password",
     *     operationId="need-reset-password-get",
     *     security={{"Authorization":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"need_reset_password"},
     *                  @OA\Property(
     *                      property="need_reset_password",
     *                      type="boolean"
     *                  )
     *              )
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    public function needresetpassword(Request $request)
    {
        // Invalidate the token
        try {
            $user =  auth()->user();

            $user_data = [];
            $user_data['need_reset_password']=(bool)$user->need_reset_password;

            $headers = ['X-Authorization-Token' => "{$this->getToken($request)}"];
            return response()->json(
                $user_data,
                Response::HTTP_OK,
                $headers
            );
        } catch (\Exception $e) {
            // something went wrong whilst attempting to encode the token
            return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/need-reset-password",
     *     tags={"Authentication"},
     *     summary="Reset password.",
     *     description="The user can change his password.",
     *     operationId="need-reset-password-post",
     *     security={{"Authorization":{}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"password","password"},
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="new_password",
     *                     type="string"
     *                 ),
     *                 example={"password": "password", "new_password": "new_password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Empty response",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token")
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    public function changePassword(Request $request): JsonResponse
    {
        $credentials = $request->only('password', 'new_password');
        $rules = [
            'password' => 'required|string|max:255',
            'new_password' => 'required|string|max:255'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
        }


        try {
            $password = $credentials['password'];
            $new_password = $credentials['new_password'];

            $user = auth()->user();
            if (!Hash::check($password, $user->password)) {
                return $this->respondWithCode(Response::HTTP_FORBIDDEN);
            }

            $user->password = Hash::make($new_password);
            $user->need_reset_password = false;
            $user->save();

            $headers = ['X-Authorization-Token' => "{$this->getToken($request)}"];
            return response()->json(
                null,
                Response::HTTP_NO_CONTENT,
                $headers
            );
        } catch (\Exception $e) {
            return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Head(
     *     path="/api/v1/auth/verify-authorization-token",
     *     tags={"Authentication"},
     *     summary="Verify authorization token",
     *     description="Verify authorization token",
     *     operationId="verify-authorization-token",
     *     security={{"Authorization":{}}},
     *     @OA\Response(
     *         response=204,
     *         description="Empty response. The request does not update the token,
     *          it just verifies if the token is valid.",
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    /**
     * Verify the bearer token. Remember, the Unauthorized is do it by Handler app/Exceptions/Handler.php
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyToken(Request $request): JsonResponse
    {
        try {
            return $this->respondWithCode(Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
