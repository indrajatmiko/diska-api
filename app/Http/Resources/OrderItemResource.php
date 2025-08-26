<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'productId' => $this->product->id,
            'variantId' => $this->variant->id,
            'name' => $this->product->name,
            'variantName' => $this->variant->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'imageUrl' => $this->product->mainImage->full_url ?? null,
        ];
    }
}