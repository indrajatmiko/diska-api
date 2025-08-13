<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Kita akan menggunakan picsum.photos untuk gambar dummy yang acak
            'image_url' => 'https://picsum.photos/seed/' . $this->faker->unique()->word . '/700/700',
        ];
    }
}