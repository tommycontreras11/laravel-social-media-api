<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Post;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
/**
     * Show all posts
     * @OA\Get (
     *     path="/api/posts",
     *     tags={"Post"},
     *     security={{"token": {}}},
     *     summary="Get list of posts",
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
            $posts = Post::all();
            
            return ApiResponse::success('Success', 200, $posts);
        } catch (Exception $e) {
            return ApiResponse::error('An error ocurred while trying to get the posts: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new post
     * @OA\Post (
     *     path="/api/posts",
     *     tags={"Post"},
     *     security={{"token": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="title",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="content",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "title":"This is the new post",
     *                     "content":"This is the content of the new post"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="CREATED",
     *      )
     * )
     */
    public function store(Request $request)
    {
        try {
            $post = Post::create(array_merge($request->all(), [
                'user_id' => auth()->user()->id
            ]));

            return ApiResponse::success('The post has been successfully created', 200, $post);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation errors: ' . $e->getMessage(), 422);
        }
    }

    /**
     * Show post information
     * @OA\Get (
     *     path="/api/posts/{id}",
     *     tags={"Post"},
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
    public function show(string $id)
    {
        try {
            $post = Post::findOrFail($id);

            return ApiResponse::success('Success', 200, $post);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the user: ' .  $e->getMessage(), 404);
        }
    }

/**
     * Update post information
     * @OA\Patch (
     *     path="/api/posts/{id}",
     *     tags={"Post"},
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
*                           @OA\Property(
     *                          property="title",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="content",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "title":"This is the new post",
     *                     "content":"This is the content of the new post"
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
    public function update(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->update($request->all());

            return ApiResponse::success('The post has been successfully updated', 200, $post);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the user: ' .  $e->getMessage(), 404);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation errors: ' . $e->getMessage(), 422);
        }
    }

    /**
     * Delete post information
     * @OA\Delete (
     *     path="/api/posts/{id}",
     *     tags={"Post"},
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
            $post = Post::findOrFail($id);
            $post->delete();

            return ApiResponse::success('The post has been successfully deleted', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('An error ocurred while trying to get the user: ' .  $e->getMessage(), 404);
        }
    }
}
