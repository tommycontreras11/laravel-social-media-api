<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceFull;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Show all users
     * @OA\Get (
     *     path="/api/users",
     *     tags={"User"},
     *     security={{"token": {}}},
     *     summary="Get list of users",
     *     description="Return list of users",
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     * )
     */
    public function index()
    {
        try {
            return ApiResponse::success('Success', 200, UserResource::collection(User::all()));
        } catch (Exception $e) {
            return ApiResponse::error('An error ocurred while trying to get the users: ' .  $e->getMessage(), 500);
        }
    }

    /**
     * Show user information
     * @OA\Get (
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     security={{"token": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return ApiResponse::success('Success', 200, new UserResourceFull($user));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the user: ' .  $e->getMessage(), 404);
        }
    }

    /**
     * Update user information
     * @OA\Patch (
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     security={{"token": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
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
     *          response=200,
     *          description="CREATED",
     *      )
     * )
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($request->all());

            return ApiResponse::success('Success', 200, new UserResourceFull($user));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the user: ' .  $e->getMessage(), 404);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation errors: ' . $e->getMessage(), 422);
        } 
    }

    /**
     * Delete user information
     * @OA\Delete (
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     security={{"token": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="NO CONTENT"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return ApiResponse::success('The user has been successfully deleted', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the user: ' .  $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error: ' . $e->getMessage(), 422);
        } 
    }
}
