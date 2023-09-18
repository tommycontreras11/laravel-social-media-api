<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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

    public function create(Request $request)
    {
        
    }
}
