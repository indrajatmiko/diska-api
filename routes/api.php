<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\VoucherController;

// Rute publik (tidak perlu login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/videos', [VideoController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/reviews', [ReviewController::class, 'index']);
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/banners/{banner}', [BannerController::class, 'show']);
Route::get('/banners/{banner}/related', [BannerController::class, 'related']);
Route::get('/provinces', [ShippingController::class, 'getProvinces']);
Route::get('/cities', [ShippingController::class, 'getCities']);
Route::get('/districts', [ShippingController::class, 'getDistricts']);
Route::get('/vouchers', [VoucherController::class, 'index']);

// Rute yang dilindungi (harus login dan membawa token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Anda bisa mendapatkan data user yang sedang login di sini
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Letakkan semua rute API yang butuh login di sini
    Route::post('/orders', [OrderController::class, 'store']);
    // Endpoint untuk riwayat pesanan pengguna
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/summary', [OrderController::class, 'summary']);
    Route::post('/cost', [ShippingController::class, 'getCost']);
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);
    Route::post('/vouchers/check', [VoucherController::class, 'check']);

    // API PINTAR UNTUK MENENTUKAN GUDANG ASAL
    Route::get('/origin-warehouse', [ShippingController::class, 'getOriginWarehouse']);
    
    Route::post('/cost', [ShippingController::class, 'getCost']);
    Route::get('/warehouses', [WarehouseController::class, 'index']);
    Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show']);
    
});