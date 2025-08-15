<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Ganti nama kolom 'id' menjadi 'city_id' agar konsisten
            // dengan respons API sebelumnya (jika frontend sudah terbiasa)
            'city_id' => $this->id,
            'city_name' => $this->name,
            'province_id' => $this->province_id,
        ];
    }
}