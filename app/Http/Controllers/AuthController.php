<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="Login",
     *     operationId="login",
     *     security={{"token": {}}},
     * @OA\RequestBody(
     *    required=true,
     *     @OA\MediaType(mediaType="multipart/form-data",
     *       @OA\Schema( required={"email","password"},
     *                  @OA\Property(property="email", type="string", description="Email Usuario", example="aufderhar.nyasia@example.net"),
     *                  @OA\Property(property="password", type="string", description="Password", example="test1234"),
     *       ),
     *     ),
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *   @OA\Response(
     *      response=403,
     *      description="Forbidden"
     *   )
     *)
     **/
    public function login(Request $request)
    {
        $validate = $request->validate([
            'email' => ['required', 'email', 'string'],
            'password' => ['required', 'string']
        ]);

        $password = User::where('email', $request->email)->value('password');

        $validatePassword = Hash::check($request->password, $password); 
        if(!$validatePassword) 
        {
            return ApiResponse::error('Sorry, the password is incorrect', 400);
        }

        if (!auth()->attempt($validate)) {
            return response(['status' => 'error', 'message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $user = auth()->user();

        $token = auth()->user()->createToken(env('TOKEN_SECRET'))->accessToken;

        return ApiResponse::success('Success', 200, [
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Create a new user
     * @OA\Post (
     *     path="/api/auth/register",
     *     tags={"Auth"},
     *     description="Sign in",
     *     operationId="register",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="username",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="first_name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="last_name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="telephone",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="age",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "first_name":"Tommy",
     *                     "last_name":"Grullón Contreras",
     *                     "username":"Tommy11",
     *                     "email":"tommy@gmail.com",
     *                     "telephone":"829-754-6150",
     *                     "age":20,
     *                     "password":"Hola1234"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="CREATED",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="first_name", type="string", example="Tommy"),
     *              @OA\Property(property="last_name", type="string", example="Grullón Contreras"),
     *              @OA\Property(property="username", type="string", example="Tommy11"),
     *              @OA\Property(property="email", type="string", example="tommy@gmail.com"),
     *              @OA\Property(property="telephone", type="string", example="829-754-6150"),
     *              @OA\Property(property="age", type="number", example=20),
     *              @OA\Property(property="password", type="string", example="Hola1234"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *          )
     *      )
     * )
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'username' => ['required', 'string', 'unique:users,username'],  
                'telephone' => ['string'],                
                'age' => ['required', 'integer'],              
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:6']
            ]);

            $user = User::create(array_merge(
                $request->all(),
                ['password' => Hash::make($request->password, [
                    'rounds' => 6
                ])]
            ));

            return ApiResponse::success('The user has been successfully created', 200, new UserResource($user));
        } catch (Exception $e) {
            return ApiResponse::error('Error: ' . $e->getMessage(), 422);
        } 
    }

    /**
     * @OA\Get(
     *     path="/api/auth/profile",
     *     tags={"Auth"},
     *     summary="Profile",
     *     operationId="Profile",
     *     security={{"token": {}}},
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *   @OA\Response(
     *      response=403,
     *      description="Forbidden"
     *   )
     *)
     **/
    public function profile() 
    {
        return ApiResponse::success('Success', 200, new UserResource(auth()->user()));
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Auth"},
     *     summary="Logout",
     *     operationId="Logout",
     *     security={{"token": {}}},
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *   @OA\Response(
     *      response=403,
     *      description="Forbidden"
     *   )
     *)
     **/
    public function logout(Request $request) 
    {
        $request->user()->token()->revoke();
        return ApiResponse::success('Successfully logged out', 200);
    }
}
