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


class OrderController extends Controller
{
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
    public function store(OrderStoreRequest $request)
    {
        // Ambil data yang sudah divalidasi
        $validatedData = $request->validated();
        $items = $validatedData['items'];
        $shippingAddress = $validatedData['shipping_address'];
        $variant = ProductVariant::find($item['variant_id']);

        // Ambil ID produk untuk query
        $productIds = array_column($items, 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // ---- PERHITUNGAN ULANG DI BACKEND (SANGAT PENTING!) ----
        // Jangan pernah percaya total harga yang dikirim dari frontend.
        $subtotal = 0;
        foreach ($items as $item) {
            // Pastikan produk ada, jika tidak, batalkan (meski sudah divalidasi)
            if (!isset($products[$item['product_id']])) {
                return response()->json(['message' => 'Produk tidak valid ditemukan.'], 422);
            }
            $product = $products[$item['product_id']];
            $subtotal += $product->price * $item['quantity'];
        }

        $totalAmount = $subtotal + $validatedData['shipping_cost'];
        // --------------------------------------------------------

        try {
            $order = DB::transaction(function () use ($request, $validatedData, $shippingAddress, $totalAmount, $items, $products) {
                // 1. Buat entitas Order utama
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'status' => 'unpaid', // Status default
                    'total_amount' => $totalAmount,
                    'shipping_cost' => $validatedData['shipping_cost'],
                    'courier' => $validatedData['courier'],
                    'province' => $shippingAddress['province'],
                    'city' => $shippingAddress['city'],
                    'district' => $shippingAddress['district'],
                    'subdistrict' => $shippingAddress['subdistrict'],
                    'postal_code' => $shippingAddress['postal_code'],
                    'address_detail' => $shippingAddress['address_detail'],
                ]);
                
                // 2. Buat entitas OrderItem untuk setiap item
                $orderItems = [];
                foreach ($items as $item) {
                    $product = $products[$item['product_id']];
                    $orderItems[] = [
                        'order_id' => $order->id,
                        'product_id' => $variant->product_id, // Simpan juga product_id induknya
                        'price' => $variant->price, // <-- Gunakan harga dari varian
                        'quantity' => $item['quantity'],
                        'price' => $product->price, // Ambil harga dari DB, bukan dari request
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                OrderItem::insert($orderItems);

                return $order;
            });

            // Jika transaksi berhasil, kembalikan response sukses
            return new OrderCreationResource($order);

        } catch (\Throwable $th) {
            // Jika terjadi error selama transaksi
            return response()->json([
                'message' => 'Gagal membuat pesanan, silakan coba lagi.',
                'error' => $th->getMessage() // Hanya untuk development
            ], 500);
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
}