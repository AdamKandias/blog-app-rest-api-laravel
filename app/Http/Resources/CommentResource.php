<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            "post_id" => $this->post->id,
            "comment" => $this->comment,
            "commentator" => $this->commentator,
            "created_at" => date("H:i, d-m-Y", strtotime($this->created_at)),
            "updated_at" => date("H:i, d-m-Y", strtotime($this->updated_at))
        ];
    }
}
