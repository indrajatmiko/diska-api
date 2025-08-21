<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductVariantResource; // Import untuk ProductVariantResource
use App\Models\ProductVariant; // Import untuk model ProductVariant

class ProductController extends Controller
{
    public function index()
    {
        // **[PERBAIKAN]** Gunakan `with('mainImage')` untuk Eager Loading
        $products = Product::with('mainImage')->latest()->paginate(10);
        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        // **[PERBAIKAN]** Gunakan `load('images')` untuk memuat relasi
        $product->load(['images', 'reviews', 'variants']);
        return new ProductDetailResource($product);
    }
}