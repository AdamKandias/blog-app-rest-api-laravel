<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CommentController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Commentator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $comment = CommentController::getById($request->id);
            if ($comment instanceof ModelNotFoundException) {
                return response()->json(['code' => Response::HTTP_NOT_FOUND, 'error' => 'Comment not found'], Response::HTTP_NOT_FOUND);
            }
        } catch (ModelNotFoundException) {
            return response()->json(['code' => Response::HTTP_NOT_FOUND, 'error' => 'Comment not found'], Response::HTTP_NOT_FOUND);
        }
        
        if (Auth::user()->id != $comment->user_id) {
            return response()->json(["message" => "data not found!"]);
        }

        return $next($request);
    }
}
