<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3), // 3 kata untuk judul
            'description' => $this->faker->paragraph(4), // 4 kalimat untuk deskripsi
            'image_url' => 'https://picsum.photos/seed/' . $this->faker->unique()->word . '/700/400',
        ];
    }
}