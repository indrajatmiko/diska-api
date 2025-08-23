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
        // =========================================================
        // ==> PERBAIKAN #1: Eager load relasi yang benar untuk efisiensi <==
        // =========================================================
        // Kita butuh item, produk dari item, dan gambar utama dari produk.
        $this->loadMissing('items.product.mainImage');
        
        $firstItem = $this->items->first();
        $totalItems = $this->items->count();

        // Buat ringkasan item untuk ditampilkan
        $itemsSummary = '';
        if ($firstItem && $firstItem->product) {
            $itemsSummary = $firstItem->product->name;
            if ($totalItems > 1) {
                $itemsSummary .= ' dan ' . ($totalItems - 1) . ' item lainnya';
            }
        }
        
        // =========================================================
        // ==> PERBAIKAN #2: Gunakan accessor 'full_url' <==
        // =========================================================
        // LAMA: optional($firstItem->product->images->first())->image_url
        // BARU: Menggunakan relasi 'mainImage' dan accessor 'full_url' yang sudah ada
        $mainImageUrl = optional($firstItem)->product->mainImage->full_url ?? null;
        
        return [
            'orderId' => 'INV-' . $this->created_at->format('Ymd') . $this->id,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->getLabel(),
            ],
            'date' => $this->created_at->format('d M Y, H:i'),
            'totalAmount' => $this->total_amount,
            'itemsSummary' => $itemsSummary,
            'mainItemImage' => $mainImageUrl,
        ];
    }
}