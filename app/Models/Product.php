<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // **[PASTIKAN BARIS INI ADA]**
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\ProductImage; // **[PASTIKAN BARIS INI ADA]** untuk relasi ke ProductImage
use App\Models\UserActivity;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Get all of the images for the Product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->oldest();
    }

    /**
     * Get the first image as the main image.
     */
    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->oldestOfMany();
    }

    /**
     * Sebuah produk memiliki banyak ulasan.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activities()
    {
        return $this->morphMany(UserActivity::class, 'subject');
    }
}