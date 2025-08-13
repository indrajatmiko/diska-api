<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Otorisasi sudah ditangani oleh middleware auth:sanctum di rute.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Validasi data utama
            'shipping_cost' => 'required|integer|min:0',
            'courier' => 'required|string|max:255',

            // Validasi alamat pengiriman
            'shipping_address' => 'required|array',
            'shipping_address.province' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:255',
            'shipping_address.subdistrict' => 'required|string|max:255',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.address_detail' => 'required|string|max:1000',

            // Validasi item-item yang dibeli
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id', // Pastikan produk ada di DB
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}