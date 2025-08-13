<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Muat relasi yang dibutuhkan untuk menghindari N+1 problem
        $this->loadMissing('items.product.images');
        
        $firstItem = $this->items->first();
        $totalItems = $this->items->count();

        // Buat ringkasan item untuk ditampilkan
        $itemsSummary = '';
        if ($firstItem) {
            $itemsSummary = $firstItem->product->name;
            if ($totalItems > 1) {
                $itemsSummary .= ' dan ' . ($totalItems - 1) . ' item lainnya';
            }
        }
        
        // Dapatkan gambar utama dari item pertama
        $mainImageUrl = $firstItem ? optional($firstItem->product->images->first())->image_url : null;
        
        return [
            'orderId' => 'INV-' . $this->id,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->getLabel(), // Mengambil label dari Enum
            ],
            'date' => $this->created_at->format('d M Y, H:i'),
            'totalAmount' => $this->total_amount,
            'itemsSummary' => $itemsSummary,
            'mainItemImage' => $mainImageUrl,
        ];
    }
}