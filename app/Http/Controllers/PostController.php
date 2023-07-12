<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostController extends Controller
{
    public static function getById($id)
    {
        try {
            return Post::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException();
        }
    }

    public function index()
    {
        return PostResource::collection(Post::with(['writer:id,username', 'comments:id,post_id,user_id,comment'])->get());
    }

    public function show($id)
    {
        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException) {
            return response()->json(['code' => Response::HTTP_NOT_FOUND, 'error' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }
        $post->loadMissing(['writer:id,username', 'comments:id,post_id,user_id,comment']);
        return new PostResource($post);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => ['required', 'max:255'],
            'content' => ['required', 'min:100']
        ]);
        
        $validatedData['author'] = Auth::user()->id;

        if ($request->file('image')) {
            $validatedData['image'] = $request->validate(['image' => ['image', 'mimes:jpeg,png,jpg', 'max:1024']]);
            $file = uniqid().'.'.$request->file('image')->getClientOriginalExtension();
            $validatedData['image'] = $file;

            Storage::putFileAs('image', $request->file('image'), $file);
        }

        $post = Post::create($validatedData);
        return new PostResource($post->loadMissing('writer:id,username'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => ['required', 'max:255'],
            'content' => ['required', 'min:100']
        ]);

        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException) {
            return response()->json(['code' => Response::HTTP_NOT_FOUND, 'error' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }

        if ($post->title != $request->title && $post->content != $request->content) {

            $post->title = $request->title;
            $post->content = $request->content;
     
            $post->save();
        }

        return new PostResource($post->loadMissing('writer:id,username'));
    }

    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException) {
            return response()->json(['code' => Response::HTTP_NOT_FOUND, 'error' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }
        $post->delete();

        return new PostResource($post->loadMissing('writer:id,username'));
    }
}
