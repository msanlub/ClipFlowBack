<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\V1\PostRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\JsonResponse; // Import the missing JsonResponse class
use App\Http\Resources\V1\PostResource; // Import the missing PostResource class
use Illuminate\Support\Facades\Validator; // Import the missing Validator class

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }
    /**
     * Display a listing of the resource.
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


    /**
     * Upload image to storage.
     */
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
    public function show(Post $post)
    {
        //
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Validator::make($request->all(), [
            'title' => 'max:191',
            'image' => 'image|max:1024',
            'description' => 'max:2000',
        ])->validate();
        if (Auth::id() !== $post->user->id) {
            return response()->json(['message' => 'You don\'t have permissions'], 403);
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
            return response()->json(['message' => 'Post update succesfully']);
        }
        return response()->json(['message' => 'Error to update post'], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
        $res = $post->delete();
        if ($res) {
            return response()->json(['message' => 'Post delete succesfully']);
        }
        return response()->json(['message' => 'Error to update post'], 500);
    }
}
