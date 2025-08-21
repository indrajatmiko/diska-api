<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductVariantResource;

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
            'images' => $this->images->pluck('full_url'),

            'rating' => $this->rating,
            'sold' => $this->sold, // Anda mungkin ingin menambahkan 'reviews' di sini juga
            'description' => $this->description,
            'weight' => $this->weight,
            'freeShipping' => $this->free_shipping,
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}