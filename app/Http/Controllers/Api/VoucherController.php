<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Menampilkan daftar semua voucher yang valid dan aktif.
     */
    public function index()
    {
        $now = now();

        // 2. Ambil voucher dari database dengan kondisi:
        // - Aktif (is_active = true)
        // - Sudah mulai berlaku (start_date <= sekarang)
        // - Belum kedaluwarsa (end_date >= sekarang)
        $vouchers = Voucher::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('end_date', 'asc') // Urutkan berdasarkan yang paling cepat kedaluwarsa
            ->get();

        // 3. Kembalikan data menggunakan Resource Collection
        // Ini akan otomatis membungkus hasilnya dalam kunci "data"
        return VoucherResource::collection($vouchers);
    }
    public function check(Request $request)
    {
        $request->validate([
            'product_code' => 'nullable|string', // Voucher produk, boleh kosong
            'shipping_code' => 'nullable|string', // Voucher ongkir, boleh kosong
            'subtotal' => 'required|integer|min:0', // Subtotal produk
            'shipping_cost' => 'required|integer|min:0', // Ongkos kirim
        ]);

        $productCode = $request->product_code;
        $shippingCode = $request->shipping_code;

        $productVoucher = $productCode ? $this->validateVoucher($productCode, 'product', $request->subtotal) : null;
        $shippingVoucher = $shippingCode ? $this->validateVoucher($shippingCode, 'shipping', $request->subtotal) : null;

        // Cek jika ada error dari hasil validasi
        if (isset($productVoucher['error']) || isset($shippingVoucher['error'])) {
            $errors = [];
            if (isset($productVoucher['error'])) $errors['product_voucher'] = $productVoucher['error'];
            if (isset($shippingVoucher['error'])) $errors['shipping_voucher'] = $shippingVoucher['error'];
            return response()->json(['message' => 'Satu atau lebih voucher tidak valid.', 'errors' => $errors], 422);
        }

        // Kalkulasi diskon
        $productDiscount = 0;
        if ($productVoucher) {
            if ($productVoucher->type === 'product_fixed') {
                $productDiscount = $productVoucher->value;
            } elseif ($productVoucher->type === 'product_percentage') {
                $productDiscount = floor(($productVoucher->value / 100) * $request->subtotal);
            }
        }
        $productDiscount = min($productDiscount, $request->subtotal); // Diskon produk tidak boleh melebihi subtotal

        $shippingDiscount = 0;
        if ($shippingVoucher) {
            // Diskon ongkir tidak boleh melebihi ongkir aktual
            $shippingDiscount = min($shippingVoucher->value, $request->shipping_cost);
        }

        return response()->json([
            'message' => 'Voucher berhasil divalidasi.',
            'data' => [
                'productDiscount' => $productDiscount,
                'shippingDiscount' => $shippingDiscount,
                'totalDiscount' => $productDiscount + $shippingDiscount,
                'productVoucherDetails' => $productVoucher, // Kirim detail jika ada
                'shippingVoucherDetails' => $shippingVoucher, // Kirim detail jika ada
            ]
        ]);
    }

    /**
     * Helper function untuk memvalidasi satu voucher.
     * @param string $code
     * @param string $voucherCategory 'product' atau 'shipping'
     * @param int $subtotal
     * @return Voucher|array
     */
    private function validateVoucher(string $code, string $voucherCategory, int $subtotal)
    {
        $voucher = Voucher::where('code', $code)->first();
        $now = now();

        if (!$voucher) return ['error' => 'Kode tidak ditemukan.'];
        
        // Cek tipe voucher
        if ($voucherCategory === 'product' && !in_array($voucher->type, ['product_fixed', 'product_percentage'])) {
            return ['error' => 'Kode ini bukan untuk diskon produk.'];
        }
        if ($voucherCategory === 'shipping' && $voucher->type !== 'shipping_fixed') {
            return ['error' => 'Kode ini bukan untuk subsidi ongkir.'];
        }

        if (!$voucher->is_active) return ['error' => 'Voucher sudah tidak aktif.'];
        if ($now->isBefore($voucher->start_date)) return ['error' => 'Voucher belum dapat digunakan.'];
        if ($now->isAfter($voucher->end_date)) return ['error' => 'Voucher telah kedaluwarsa.'];
        if ($subtotal < $voucher->min_purchase) return ['error' => 'Pembelian tidak memenuhi syarat minimal.'];

        return $voucher;
    }
}