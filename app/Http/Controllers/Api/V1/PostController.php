<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\V1\PostRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\V1\PostResource;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Documentation",
 *     description="API Documentation"
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local server"
 * )
 
 */


class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }


    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *    path="/api/v1/posts",
     *   summary="Get all posts",
     * description="Get all posts",
     * operationId="index",
     * tags={"Posts"},
     * security={{"bearerAuth": {}}},
     * @OA\Response(
     *   response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     *  type="array",
     * @OA\Items(ref="#/components/schemas/Post"))
     * ))
     */
    public function index()
    {
        //
        return PostResource::collection(Post::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param App\Http\Requests\V1\PostRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        if (!auth()->user()->can('create post')) {
            return response()->json(['error' =>
            'No tienes permisos para crear posts'], 403);
        }
        $request->validated();
        $user = Auth::user();
        $post = new Post();
        $post->user()->associate($user);
        $url_image = $this->upload($request->file('image'));
        $post->image = $url_image;
        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $res = $post->save();
        if ($res) {
            return response()->json(['message' => 'Post create succesfully'], 201);
        }
        return response()->json(['message' => 'Error to create post'], 500);
    }

    private function upload($image)
    {
        $path_info = pathinfo($image->getClientOriginalName());
        $post_path = 'images/post';
        $rename = uniqid() . '.' . $path_info['extension'];
        $image->move(public_path() . "/$post_path", $rename);
        return "$post_path/$rename";
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        if (!$post) {
            throw new NotFoundHttpException("Post no encontrado");
        }
        return response()->json(new PostResource($post), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        // Se supone que esta parte ya no hace falta, vamos a probarlo
        /*
        Validator::make($request->all(), [
            'title' => 'max:70',
            'description' => 'max:2000',
            'image' => 'image|max:1024'
        ])->validate();
*/
        if (!auth()->user()->can('edit post')) {
            return response()->json(['error' =>
            'No tienes permisos para modificar posts'], 403);
        }
        if (!$post) {
            throw new NotFoundHttpException("Post no encontrado");
        }

        if (Auth::id() !== $post->user_id) {
            return response()->json(['message' => 'You are not the owner of this post'], 403);
        }

        if (!empty($request->input('title'))) {
            $post->title = $request->input('title');
        }

        if (!empty($request->input('description'))) {
            $post->description = $request->input('description');
        }

        if (!empty($request->file('image'))) {
            $url_image = $this->upload($request->file('image'));
            $post->image = $url_image;
        }

        $res = $post->save();

        if ($res) {
            return response()->json(['message' => 'Post updated succesfully'], 200);
        }

        return response()->json(['message' => 'Error to update post'], 500);

        /*return response()->json(
        ['message' => $res ? 'Post updated successfully' : 'Error updating post'],
        $res ? 200 : 500
    );*/
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        if (!auth()->user()->can('delete post')) {
            return response()->json(['error' =>
            'No tienes permisos para borrar posts'], 403);
        }
        if (!$post) {
            throw new NotFoundHttpException("Post no encontrado");
        }

        if (Auth::id() !== $post->user_id) {
            return response()->json(['message' => 'You are not the owner of this post'], 403);
        }


        $res = $post->delete();
        if ($res) {
            //Elimina la imagen del post
            $this->deleteImage($post->image);
            return response()->json(['message' => 'Post deleted succesfully'], 200);
        }
        return response()->json(['message' => 'Error to delete post'], 500);
    }

    private function deleteImage($imagePath)
    {
        $fullPath = public_path($imagePath);
        if (file_exists($fullPath)) {
            @unlink($fullPath); // Elimina el archivo
        }
    }
}
