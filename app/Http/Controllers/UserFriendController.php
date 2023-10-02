<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserFriendRequest;
use App\Http\Requests\UpdateUserFriendRequest;
use App\Http\Resources\UserFriendResource;
use App\Http\Resources\UserFriendResourceFull;
use App\Http\Responses\ApiResponse;
use App\Models\UserFriend;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserFriendController extends Controller
{
    /**
     * Show all user friends
     * @OA\Get (
     *     path="/api/user_friends",
     *     tags={"User Friend"},
     *     security={{"token": {}}},
     *     summary="Get list of user friends",
     *     description="Return list of user friends",
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
            $user_friends = UserFriend::all();

            return ApiResponse::success('Success', 200, UserFriendResource::collection($user_friends));
        } catch (Exception $e) {
            return ApiResponse::error('An error ocurred while trying to get the user friends: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new user friend
     * @OA\Post (
     *     path="/api/user_friends",
     *     tags={"User Friend"},
     *     security={{"token": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="target_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="type",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "target_id":3,
     *                     "type":"School"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="CREATED",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="target_id", type="number", example="3"),
     *              @OA\Property(property="type", type="string", example="School"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *          )
     *      )
     * )
     */
    public function store(StoreUserFriendRequest $request)
    {
        try {
            $user_friend = UserFriend::create(array_merge($request->all(), [
                'source_id' => auth()->user()->id,
                'status' => 'New'
            ]));

            return ApiResponse::success('Friend request sent', 200, new UserFriendResource($user_friend));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the target id: ' . $e->getMessage(), 404);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation errors: ' . $e->getMessage(), 422);
        }
    }

    /**
     * Show user friends information
     * @OA\Get (
     *     path="/api/user_friends/{id}",
     *     tags={"User Friend"},
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
            $user_friend = UserFriend::with(['source_friend', 'target_friend'])->findOrFail($id);
            
            return ApiResponse::success('Success', 200, new UserFriendResourceFull($user_friend));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the user friends: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Update user friend
     * @OA\Patch (
     *     path="/api/user_friends/{id}",
     *     tags={"User Friend"},
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
     *                          property="target_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="type",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "target_id":3,
     *                     "type":"School"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="CREATED",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="target_id", type="number", example="3"),
     *              @OA\Property(property="type", type="string", example="School"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *          )
     *      )
     * )
     */
    public function update(UpdateUserFriendRequest $request, $id)
    {
        try {
            $user_friend = UserFriend::findOrFail($id);
            
            $user_friend->update(array_merge($request->all(), [
                'source_id' => auth()->user()->id,
                'status' => 'New'
            ]));
            
            return ApiResponse::success('The user friend has been successfully updated', 200, new UserFriendResource($user_friend));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the user friend: ' . $e->getMessage(), 404);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation errors: ' . $e->getMessage(), 422);
        }
    }
    
    /**
     * Delete user friend information
     * @OA\Delete (
     *     path="/api/user_friends/{id}",
     *     tags={"User Friend"},
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
            $user_friend = UserFriend::findOrFail($id);

            $user_friend->delete();

            return ApiResponse::success('The user friend has been successfully deleted', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the user friend: ' . $e->getMessage(), 404);
        }
    }
}
