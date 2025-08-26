<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderStoreRequest; // <-- Import Form Request
use App\Http\Resources\OrderCreationResource; // <-- Resource untuk response
use App\Http\Resources\OrderListResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Import DB Facade
use App\Enums\OrderStatus;
use App\Models\ProductVariant;
use App\Models\Voucher;
use App\Http\Resources\OrderDetailResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Tambahkan baris ini


class OrderController extends Controller
{
    use AuthorizesRequests;

    /**
     * Menampilkan riwayat pesanan untuk pengguna yang sedang login.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // 1. Dapatkan pengguna yang sedang login dari request
        $user = $request->user();

        // 2. Query dasar untuk mengambil pesanan HANYA milik pengguna ini
        $ordersQuery = Order::query()->where('user_id', $user->id);

        // 3. (Opsional) Filter berdasarkan status jika ada di query parameter
        // Contoh: /api/orders?status=shipped
        $ordersQuery->when($request->status, function ($query, $status) {
            return $query->where('status', $status);
        });

        // 4. Urutkan dari yang terbaru dan lakukan paginasi
        $orders = $ordersQuery->latest()->paginate(10);

        // 5. Kembalikan data menggunakan API Resource untuk transformasi
        return OrderListResource::collection($orders);
    }
    
    /**
     * Menyimpan pesanan baru ke database.
     *
     * @param OrderStoreRequest $request
     * @return OrderCreationResource|\Illuminate\Http\JsonResponse
     */
    public function store(OrderStoreRequest $request) // Gunakan OrderStoreRequest yang sudah ada
    {
        $validatedData = $request->validated();
        $items = $validatedData['items'];
        $shippingAddress = $validatedData['shipping_address'];

        // --- Kalkulasi Ulang di Backend (Keamanan) ---
        $subtotal = 0;
        $variantIds = array_column($items, 'variant_id');
        $variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        foreach ($items as $itemData) {
            $variant = $variants[$itemData['variant_id']] ?? null;
            if (!$variant || $variant->stock < $itemData['quantity']) {
                return response()->json(['message' => 'Stok produk tidak mencukupi atau produk tidak valid.'], 422);
            }
            $subtotal += $variant->price * $itemData['quantity'];
        }

        // --- Validasi & Kalkulasi Voucher ---
        $productDiscount = 0;
        $shippingDiscount = 0;
        // (Asumsikan Anda sudah memiliki helper 'validateVoucher' di controller ini)
        if (!empty($validatedData['product_voucher_code'])) {
            $productVoucher = $this->validateVoucher($validatedData['product_voucher_code'], 'product', $subtotal);
            if (!$productVoucher instanceof Voucher) { // Jika validasi gagal
                return response()->json(['message' => 'Voucher produk tidak valid.', 'errors' => ['product_voucher_code' => $productVoucher['error']]], 422);
            }
            if ($productVoucher->type === 'product_fixed') $productDiscount = $productVoucher->value;
            if ($productVoucher->type === 'product_percentage') $productDiscount = floor(($productVoucher->value / 100) * $subtotal);
        }

        if (!empty($validatedData['shipping_voucher_code'])) {
            $shippingVoucher = $this->validateVoucher($validatedData['shipping_voucher_code'], 'shipping', $subtotal);
            if (!$shippingVoucher instanceof Voucher) { // Jika validasi gagal
                return response()->json(['message' => 'Voucher ongkir tidak valid.', 'errors' => ['shipping_voucher_code' => $shippingVoucher['error']]], 422);
            }
            $shippingDiscount = min($shippingVoucher->value, $validatedData['shipping_cost']);
        }
        
        $totalAmount = ($subtotal - $productDiscount) + ($validatedData['shipping_cost'] - $shippingDiscount);

        try {
            $order = DB::transaction(function () use ($request, $validatedData, $shippingAddress, $totalAmount, $items, $variants) {
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'status' => 'unpaid',
                    'total_amount' => $totalAmount,
                    'shipping_cost' => $validatedData['shipping_cost'],
                    'courier' => $validatedData['courier'],
                    'product_voucher_code' => $validatedData['product_voucher_code'] ?? null,
                    'shipping_voucher_code' => $validatedData['shipping_voucher_code'] ?? null,
                    'province' => $shippingAddress['province'],
                    'city' => $shippingAddress['city'],
                    'district' => $shippingAddress['district'],
                    'subdistrict' => $shippingAddress['sub_district'] ?? null,
                    'postal_code' => $shippingAddress['postal_code'] ?? '00000',
                    'address_detail' => $shippingAddress['address_detail'],
                ]);

                // =========================================================
                // ==> INTI PERBAIKAN: Perulangan foreach yang benar ada di sini <==
                // =========================================================
                foreach ($items as $item) { // Variabel $item didefinisikan DI SINI
                    $variant = $variants[$item['variant_id']];
                    
                    // Simpan Order Item
                    $order->items()->create([
                        'product_id' => $variant->product_id,
                        'product_variant_id' => $variant->id, // Simpan juga ID variannya
                        'quantity' => $item['quantity'],
                        'price' => $variant->price,
                    ]);
                    
                    // Kurangi Stok
                    $variant->decrement('stock', $item['quantity']);
                }

                return $order;
            });

            return new OrderCreationResource($order);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Gagal membuat pesanan, silakan coba lagi.', 'error' => $th->getMessage()], 500);
        }
    }

        /**
     * Memberikan ringkasan jumlah pesanan berdasarkan status untuk pengguna yang login.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary(Request $request)
    {
        // 1. Dapatkan ID pengguna yang sedang login
        $userId = $request->user()->id;

        // 2. Buat daftar semua status yang mungkin dari Enum dengan nilai default 0
        // Ini memastikan semua kunci status ada di respons, bahkan jika jumlahnya 0.
        $allStatuses = array_map(fn($case) => $case->value, OrderStatus::cases());
        $summary = array_fill_keys($allStatuses, 0);

        // 3. Lakukan query yang efisien untuk menghitung pesanan per status
        $counts = Order::query()
            ->where('user_id', $userId)
            // Pilih kolom status dan hitung total untuk setiap grup
            ->select('status', DB::raw('count(*) as total'))
            // Kelompokkan hasil berdasarkan status
            ->groupBy('status')
            // Ubah hasil menjadi array asosiatif [status => total]
            ->pluck('total', 'status')
            ->all();

        // 4. Gabungkan hasil dari database ke dalam array summary kita
        // Ini akan menimpa nilai 0 dengan jumlah yang sebenarnya jika ada.
        $summary = array_merge($summary, $counts);
        
        // 5. Kembalikan respons dalam format yang diminta
        return response()->json([
            'data' => $summary
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

    public function show($orderId)
    {
        // Hapus prefix "INV-" jika ada
        $numericId = substr($orderId, 12);

        // Cari order berdasarkan ID numerik
        $order = Order::findOrFail($numericId);

        // Otorisasi: Pastikan user hanya bisa melihat order miliknya
        $this->authorize('view', $order);

        // Eager load semua relasi yang dibutuhkan untuk efisiensi
        $order->load([
            'user', 
            'items.product.mainImage', 
            'items.variant'
        ]);

        return new OrderDetailResource($order);
    }
}