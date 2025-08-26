<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResellerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'imageUrl' => $this->full_image_url, // Menggunakan accessor
            'description' => $this->description,
            'whatsappNumber' => $this->whatsapp_number,
            'lastUpdatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}