<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderCreationResource extends JsonResource
{
    public static $wrap = null; // Hapus "data": {} wrapper

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'Order created successfully',
            'orderId' => 'INV-' . $this->created_at->format('Ymd') . $this->id,
            'totalAmount' => $this->total_amount,
        ];
    }

    public function withResponse($request, $response)
    {
        // Atur status code ke 201 Created
        $response->setStatusCode(201);
    }
}