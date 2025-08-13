<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel sebelum seeding untuk menghindari duplikasi
        Product::query()->delete();
        ProductImage::query()->delete();

        // Membuat 20 produk menggunakan factory
        Product::factory(20)->create()->each(function ($product) {
            // Untuk setiap produk yang dibuat, buat 4 gambar terkait
            ProductImage::factory(4)->create([
                'product_id' => $product->id,
            ]);
        });
    }
}