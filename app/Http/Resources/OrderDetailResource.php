<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Voucher;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Kalkulasi Summary Pembayaran
        $subtotal = $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $productDiscount = 0;
        if ($this->product_voucher_code) {
            $voucher = Voucher::where('code', $this->product_voucher_code)->first();
            if ($voucher) {
                if ($voucher->type === 'product_fixed') $productDiscount = $voucher->value;
                if ($voucher->type === 'product_percentage') $productDiscount = floor(($voucher->value / 100) * $subtotal);
            }
        }

        $shippingDiscount = 0;
        if ($this->shipping_voucher_code) {
            $voucher = Voucher::where('code', $this->shipping_voucher_code)->first();
            if ($voucher) {
                $shippingDiscount = min($voucher->value, $this->shipping_cost);
            }
        }

        return [
            'orderId' => 'INV-' . $this->created_at->format('Ymd') . $this->id,
            'status' => ['value' => $this->status->value, 'label' => $this->status->getLabel()],
            'date' => $this->created_at->format('d M Y, H:i'),
            'shippingAddress' => [
                'recipientName' => $this->user->name, // Asumsi nama penerima = nama user
                'phoneNumber' => $this->user->phone_number, // Asumsi no telp = no telp user
                'address' => "{$this->address_detail}, {$this->sub_district}, {$this->district}, {$this->city}, {$this->province}, {$this->postal_code}",
            ],
            'shippingInfo' => [
                'courier' => strtoupper($this->courier),
                'service' => $this->shipping_service,
                'trackingNumber' => $this->tracking_number,
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'paymentSummary' => [
                'subtotal' => $subtotal,
                'shippingCost' => $this->shipping_cost,
                'productDiscount' => $productDiscount,
                'shippingDiscount' => $shippingDiscount,
                'totalAmount' => $this->total_amount,
            ],
        ];
    }
}