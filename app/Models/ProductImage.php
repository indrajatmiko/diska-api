<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory; // **[PASTIKAN BARIS INI ADA]**
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory; // **[PASTIKAN BARIS INI ADA]**

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = []; // Izinkan semua field diisi untuk kemudahan seeding

    /**
     * Get the product that owns the image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function fullUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_url
                ? Storage::disk('public')->url($this->image_url)
                : null,
        );
    }
}