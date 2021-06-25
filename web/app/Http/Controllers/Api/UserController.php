<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PasswordResets;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends ApiController
{
    const APITOKEN = "API_ACCESS_TOKEN";

    /**
     * The password token repository.
     *
     * @var \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */

    protected $tokens;


    /**
     * The number of seconds a token should last.
     *
     * @var int
     */
    protected $expires;


    public function __construct()
    {
        $this->expires = 60 * 5; // 5 minutos
    }


    /**
     * @OA\Get(
     *     path="/api/v1/user/profile",
     *     tags={"User"},
     *     summary="Retrieve the user's profile",
     *     description="Retrieve the user's profile",
     *     operationId="profile-get",
     *     security={{"Authorization":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Authentication token",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    public function getProfile(Request $request)
    {
        try {
            $user_data = [];
            $user =  auth()->user();
            $user_profile = $user->userProfile;
            $user_data['id'] = $user->id;
            $user_data['name'] = $user_profile->first_name;
            $user_data['surname'] = $user_profile->last_name;
            $user_data['email'] = $user->email;


            $headers = ['X-Authorization-Token' => "{$this->getToken($request)}"];

            return response()->json(
                $user_data,
                Response::HTTP_OK,
                $headers
            );
        } catch (\Exception $e) {
            // Something went wrong with JWT Auth.
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Patch(
     *     path="/api/v1/user/profile/{id}",
     *     tags={"User"},
     *     summary="Update the user's profile",
     *     description="Update the user's profile",
     *     operationId="profile-patch",
     *     security={{"Authorization":{}}},
     *     @OA\RequestBody(
     *         description="User's credentials",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  allOf={
     *                      @OA\Schema(ref="#/components/schemas/User"),
     *                      @OA\Schema(
     *                          required={"password"},
     *                          @OA\Property(
     *                              property="password",
     *                              type="string",
     *                              format="password",
     *                              description="User's password to confirm his identity",
     *                          ),
     *                          @OA\Property(
     *                              property="new_password",
     *                              type="string",
     *                              format="password",
     *                          ),
     *                      )
     *                  }
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication token",
     *         @OA\Header(header="X-Authorization-Token", ref="#/components/headers/X-Authorization-Token"),
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/User"),
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:255',
            'surname' => 'required|min:3|max:255',
            'password' => 'required|min:6',
            'new_password' => 'min:6|nullable',
        ]);

        if ($validator->fails()) {
            return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            // Id actual
            $idprofile = auth()->user()->getAuthIdentifier();

            // Creamos un nuevo objeto para nuestro nuevo usuario
            $user = User::with('userProfile')->find($idprofile);

            // Si el usuario no existe entonces lanzamos un error 404 :(
            if (is_null($user)) {
                return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
            }

            // Verificamos que el password antiguo es valido
            $password = $request->get('password', '');
            $user = auth()->user();
            if (!Hash::check($password, $user->password)) {
                return $this->respondWithCode(Response::HTTP_FORBIDDEN);
            }

            $user->userProfile->first_name = $request->get('name', '');
            $user->userProfile->last_name = $request->get('surname', '');
            if (!empty($request->get('new_password', ''))) {
                $user->password = Hash::make($request->get('new_password'));
            }
            $user->push();

            // All good so return the token
            $user_data = [];
            $user_profile = $user->userProfile;
            $user_data['id'] = $user->id;
            $user_data['name'] = $user_profile->first_name;
            $user_data['surname'] = $user_profile->last_name;
            $user_data['email'] = $user->email;

            $headers = ['X-Authorization-Token' => "{$this->getToken($request)}"];
            return response()->json(
                $user_data,
                Response::HTTP_OK,
                $headers
            );
        } catch (\Exception $e) {
            // Something went wrong.
            //return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            return $this->respondWithCustom(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage()
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/password/email",
     *     tags={"User"},
     *     summary="Require the reset password mail",
     *     description="Using the user's email, the system will send an email with a 5 digit
     *              code to reset the password.",
     *     operationId="password_email",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 example={"email": "info@aduxia.com"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Empty response",
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     */
    public function postEmail(Request $request): JsonResponse
    {
        $credentials = $request->only('email');
        $rules = [
            'email' => 'required|string|email|max:255',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Comprobamos que existe el mail introducido
        $user = User::where("email", '=', $request->only('email'))->first();

        if (is_null($user)) {
            //Si no existe devolvemos un error
            return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
        }

        try {
            // Generar token y código envío
            $app = Password::broker();
            $token_email = $app->createToken($user);
            $password_reset = PasswordResets::where("email", '=', $user->email)->first();
            $codigo_envio = rand(10000, 99999);
            $password_reset->code = $codigo_envio;
            $password_reset->save();

            // Enviar mail
            Mail::send(
                'modules.contacto.email',
                compact('codigo_envio'),
                function ($message) use ($user) {
                    $message
                        ->to($user->email)
                        ->subject('Reset password code' . env("PROJECT_NAME"));
                }
            );
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/password/token",
     *     tags={"User"},
     *     summary="Get token to reset password.",
     *     description="Get token to reset password.",
     *     operationId="password_token",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="code",
     *                     type="string"
     *                 ),
     *                 example={"code": "12345"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email token",
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"token"},
     *                  @OA\Property(
     *                      property="token",
     *                      type="string"
     *                  )
     *              )
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")

     * )
     */
    public function getPasswordToken(Request $request): JsonResponse
    {
        $credentials = $request->only('code');
        $rules = [
            'code' => 'required|string|max:255',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $codigo = $credentials['code'];
            $password_reset = PasswordResets::where("code", '=', $codigo)->first();

            if (is_null($password_reset)) {
                return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($this->codeExpired($password_reset->created_at)) {
                return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
            }

            $user = User::where("email", '=', $password_reset->email)->first();

            if (is_null($user)) {
                return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
            }

            $app = Password::broker();
            $token_email = $app->createToken($user);

            // All good so return the token
            //return $this->respondWithToken($token);
            $data = [];

            $data['token'] = $token_email;


            return response()->json(
                $data,
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->respondWithCustom(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage()
            );

            //return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/v1/user/password/reset",
     *     tags={"User"},
     *     summary="Reset password.",
     *     description="Reset password.",
     *     operationId="password_reset",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email","password","password_confirmation","token"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="token",
     *                     type="string"
     *                 ),
     *                 example={
     *                          "email": "info@aduxia.com",
     *                          "password": "password",
     *                          "password_confirmation": "password",
     *                          "token": "d5c0c1f3bad74d08495fad37cb00ed2213aa83740b4947d2cc6c85ab12030eb8"
     *                  }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Empty response",
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")

     * )
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');
        $rules = [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|max:255',
            'password_confirmation' => 'required|string|max:255',
            'token' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($credentials['password'] != $credentials['password_confirmation']) {
            return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $email = $credentials['email'];
            $password = $credentials['password'];
            $token = $credentials['token'];
            $password_reset = PasswordResets::where("email", '=', $email)->first();

            if (is_null($password_reset)) {
                return $this->respondWithCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($this->codeExpired($password_reset->created_at)) {
                return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
            }

            $user = User::where("email", '=', $email)->first();

            if (is_null($user)) {
                return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
            }

            $app = Password::broker();
            if (!$app->tokenExists($user, $token)) {
                return $this->respondWithCode(Response::HTTP_UNAUTHORIZED);
            }

            $user->password = Hash::make($password);
            $user->save();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->respondWithCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    protected function codeExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }
}
