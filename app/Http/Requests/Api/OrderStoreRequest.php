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
            'shipping_address.district' => 'required|string|max:255',
            'shipping_address.sub_district' => 'required|string|max:255',
            'shipping_address.postal_code' => 'max:5',
            'shipping_address.address_detail' => 'required|string|max:1000',
            'product_voucher_code' => 'nullable|string|exists:vouchers,code',
            'shipping_voucher_code' => 'nullable|string|exists:vouchers,code',
            // Validasi item-item yang dibeli
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|integer|exists:product_variants,id',
            'items.*.product_id' => 'prohibited', // Larang pengiriman product_id
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}