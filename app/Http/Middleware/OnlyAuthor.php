<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PostController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OnlyAuthor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $post = PostController::getById($request->id);
        } catch (ModelNotFoundException) {
            return response()->json(['code' => Response::HTTP_NOT_FOUND, 'error' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }
        
        if (Auth::user()->id != $post->author) {
            return response()->json(["message" => "data not found!"]);
        }

        return $next($request);
    }
}
