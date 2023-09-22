<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Comment;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    /**
     * Show all comments
     * @OA\Get (
     *     path="/api/comments",
     *     tags={"Comment"},
     *     security={{"token": {}}},
     *     summary="Get list of comments",
     *     description="Return list of comments",
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
            $comments = Comment::all();

            return ApiResponse::success('Success', 200, $comments);
        } catch (Exception $e) {
            return ApiResponse::error('An error ocurred while trying to get the comments: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new comment
     * @OA\Post (
     *     path="/api/comments",
     *     tags={"Comment"},
     *     security={{"token": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="user_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="post_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="comment",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "user_id":"1",
     *                     "post_id":"1",
     *                     "comment":"This is the new comment"
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
     *              @OA\Property(property="comment", type="string", example="This is the new comment"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        try {
            $comment = Comment::create($request->all());
            
            return ApiResponse::success('The comment has been successfully created', 200, $comment);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation errors: ' . $e->getMessage(), 422);
        }
    }

    /**
     * Show comment information
     * @OA\Get (
     *     path="/api/comments/{id}",
     *     tags={"Comment"},
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
            $comment = Comment::findOrFail($id);

            return ApiResponse::success('Success', 200, $comment);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the comment: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Update comment information
     * @OA\Patch (
     *     path="/api/comments/{id}",
     *     tags={"Comment"},
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
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="post_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="comment",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "user_id":"1",
     *                     "post_id":"1",
     *                     "comment":"This is the new comment"
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
     *              @OA\Property(property="comment", type="string", example="This is the new comment"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->update($request->all());

            return ApiResponse::success('The comment has been successfully updated', 200, $comment);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the commen: ' . $e->getMessage(), 404);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation errors: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Delete comment information
     * @OA\Delete (
     *     path="/api/comments/{id}",
     *     tags={"Comment"},
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
            $comment = Comment::findOrFail($id);
            $comment->delete();

            return ApiResponse::success('The comment has been successfully deleted', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the comment: ' . $e->getMessage(), 404);
        }
    }
}
