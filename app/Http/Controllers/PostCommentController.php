<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostCommentRequest;
use App\Http\Requests\UpdatePostCommentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\PostComment;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PostCommentController extends Controller
{
    /**
     * Show all comments
     * @OA\Get (
     *     path="/api/post_comments",
     *     tags={"Post Comment"},
     *     security={{"token": {}}},
     *     summary="Get list of posts comments",
     *     description="Return list of posts",
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
            $post_comments = PostComment::all();

            return ApiResponse::success('Success', 200, $post_comments);
        } catch (Exception $e) {
            return ApiResponse::error('An error ocurred while trying to get the post comments: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new post 
     * @OA\Post (
     *     path="/api/post_comments",
     *     tags={"Post Comment"},
     *     security={{"token": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="user_id",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="post_id",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="comment",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "user_id":"1",
     *                     "post_id":"1",
     *                     "comment":"This is the first comment"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="CREATED",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="user_id", type="number", example="1"),
     *              @OA\Property(property="post_id", type="number", example="1"),
     *              @OA\Property(property="comment", type="string", example="This is the first comment"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *          )
     *      )
     * )
     */
    public function store(StorePostCommentRequest $request)
    {
        try {
            $post_comment = PostComment::create($request->all());
            
            return ApiResponse::success('The comment has been successfully created', 200, $post_comment);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation errors: ' . $e->getMessage(), 422);
        }
    }

    /**
     * Show post information
     * @OA\Get (
     *     path="/api/post_comments/{id}",
     *     tags={"Post Comment"},
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
            $post_comment = PostComment::findOrFail($id);

            return ApiResponse::success('Success', 200, $post_comment);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the comments: ' .  $e->getMessage(), 404);
        }
    }

    /**
     * Update post information
     * @OA\Patch (
     *     path="/api/post_comments/{id}",
     *     tags={"Post Comment"},
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
     *                          property="user_id",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="post_id",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="comment",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "user_id":"1",
     *                     "post_id":"1",
     *                     "comment":"This is the first comment"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="CREATED",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="user_id", type="number", example="1"),
     *              @OA\Property(property="post_id", type="number", example="1"),
     *              @OA\Property(property="comment", type="string", example="This is the first comment"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *          )
     *      )
     * )
     */
    public function update(UpdatePostCommentRequest $request, $id)
    {
        try {
            $post_comment = PostComment::findOrFail($id);
            $post_comment->update($request->all());

            return ApiResponse::success('The comment has been successfully updated', 200, $post_comment);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the comments: ' .  $e->getMessage(), 404);
        }
    }

    /**
     * Delete post information
     * @OA\Delete (
     *     path="/api/post_comments/{id}",
     *     tags={"Post Comment"},
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
            $post_comment = PostComment::findOrFail($id);
            $post_comment->delete();

            return ApiResponse::success('The comment has been successfully deleted', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the comments: ' .  $e->getMessage(), 404);
        }
    }
}
