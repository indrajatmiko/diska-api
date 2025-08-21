<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'price' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'originalPrice' => $this->original_price ? 'Rp ' . number_format($this->original_price, 0, ',', '.') : null,
            
            // **[PERBAIKAN UTAMA]** Ambil URL dari relasi `mainImage`
            // `whenLoaded` memastikan relasi hanya diakses jika sudah di-load sebelumnya
            // untuk mencegah N+1 query problem. Kita akan handle loading di controller.
            // Opsi sederhana: langsung akses relasi.
            'imageUrl' => $this->mainImage->full_url ?? null,

            'rating' => $this->rating,
            'sold' => $this->sold,
            'freeShipping' => $this->free_shipping,
        ];
    }
}