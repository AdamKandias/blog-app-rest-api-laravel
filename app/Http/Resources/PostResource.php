<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "image" => $this->image,
            "content" => $this->content,
            "writer" => $this->writer,
            "created_at" => date("H:i, d-m-Y", strtotime($this->created_at)),
            "updated_at" => date("H:i, d-m-Y", strtotime($this->updated_at)),
            "comments" => $this->whenLoaded("comments", function(){
                return collect($this->comments)->each(function($comment) {
                    $comment->commentator;
                    return $comment; 
                });
            }),
            "total_comments" => $this->whenLoaded("comments", function(){
                return count($this->comments); 
            }),
        ];
    }
}
