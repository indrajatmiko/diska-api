<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'username' => $this->username,
            'rating' => (int) $this->rating, // Pastikan tipe data integer
            'description' => $this->description,
            'review_date' => $this->review_date->toIso8601String(), // Format tanggal standar API
        ];
    }
}