<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Format output agar konsisten dan mudah digunakan di frontend
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city_id' => $this->city_id,
        ];
    }
}