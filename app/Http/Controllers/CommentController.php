<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentController extends Controller
{
    public static function getById($id)
    {
        try {
            return Comment::findOrFail($id);
        } catch (ModelNotFoundException) {
            return new ModelNotFoundException();
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'post_id' => ['required', 'exists:posts,id'],
            'comment' => ['required']
        ]);

        $validatedData['user_id'] = Auth::user()->id;

        $comment = Comment::create($validatedData);

        return new CommentResource($comment->loadMissing(['commentator:id,username', 'post:id']));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'comment' => ['required']
        ]);

        try {
            $comment = $this->getById($id);
        } catch (ModelNotFoundException) {
            return response()->json(['code' => Response::HTTP_NOT_FOUND, 'error' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }

        if ($comment->comment != $request->comment) {

            $comment->comment = $request->comment;
     
            $comment->save();
        }

        return new CommentResource($comment->loadMissing(['commentator:id,username', 'post:id']));
    }

    public function destroy($id)
    {
        try {
            $comment = $this->getById($id);
        } catch (ModelNotFoundException) {
            return response()->json(['code' => Response::HTTP_NOT_FOUND, 'error' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }

        $comment->delete();

        return new CommentResource($comment->loadMissing(['commentator:id,username', 'post:id']));
    }
}
