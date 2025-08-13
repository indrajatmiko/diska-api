<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
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
            
            // **[PERBAIKAN UTAMA]** Ambil SEMUA URL gambar dari relasi `images`
            // Kita hanya ingin array of strings (URL), bukan seluruh objek gambar.
            'images' => $this->images->pluck('image_url'),

            'rating' => $this->rating,
            'sold' => $this->sold, // Anda mungkin ingin menambahkan 'reviews' di sini juga
            'description' => $this->description,
            'weight' => $this->weight,
            'freeShipping' => $this->free_shipping,
            // Anda bisa menambahkan data varian di sini jika ada
            'variants' => [
              'size' => ['S', 'M', 'L', 'XL'] // Contoh data varian statis
            ]
        ];
    }
}