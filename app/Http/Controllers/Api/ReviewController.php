<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReviewRequest; // Import Form Request
use App\Http\Resources\ReviewResource; // Import API Resource
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    /**
     * Menampilkan daftar ulasan untuk produk tertentu dengan paginasi.
     *
     * @param Product $product
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Product $product)
    {
        // Ambil ulasan melalui relasi yang sudah kita buat
        // Urutkan dari yang terbaru dan lakukan paginasi
        $reviews = $product->reviews()->latest('review_date')->paginate(10);

        return ReviewResource::collection($reviews);
    }

    /**
     * Menyimpan ulasan baru untuk produk tertentu.
     *
     * @param StoreReviewRequest $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreReviewRequest $request, Product $product): JsonResponse
    {
        // Ambil data yang sudah divalidasi oleh StoreReviewRequest
        $validatedData = $request->validated();
        
        // Buat ulasan baru yang secara otomatis terhubung dengan produk ini
        $product->reviews()->create([
            'username' => $validatedData['username'],
            'rating' => $validatedData['rating'],
            'description' => $validatedData['description'] ?? null,
            'review_date' => now(), // Set tanggal rating ke waktu saat ini
            // Jika Anda ingin menghubungkan ke user_id:
            // 'user_id' => $request->user()->id, 
        ]);

        return response()->json(['message' => 'Ulasan Anda berhasil dikirim.'], 201);
    }
}