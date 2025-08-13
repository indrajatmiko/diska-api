<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'videoUrl' => $this->video_url,
            'thumbnailUrl' => $this->thumbnail_url,
            'user' => $this->user_name,
            'avatar' => $this->user_avatar_url,
            'caption' => $this->caption,
            'likes' => $this->likes,
            'comments' => $this->comments,
        ];
    }
}
