<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->numberBetween(50000, 300000);
        $hasDiscount = $this->faker->boolean(70); // 70% kemungkinan produk punya diskon

        return [
            'name' => $this->faker->words(4, true), // Menghasilkan 4 kata acak
            'description' => $this->faker->paragraph(5), // 5 kalimat paragraf
            'price' => $price,
            'original_price' => $hasDiscount ? $price + $this->faker->numberBetween(20000, 50000) : null,
            'weight' => $this->faker->numberBetween(100, 1500), // Berat antara 100g - 1.5kg
            'rating' => $this->faker->randomFloat(1, 4, 5), // Angka desimal antara 4.0 - 5.0
            'sold' => $this->faker->numberBetween(10, 2000), // Terjual antara 10 - 2000
            'free_shipping' => $this->faker->boolean(50), // 50% kemungkinan gratis ongkir
        ];
    }
}