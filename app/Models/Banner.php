<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use App\Models\UserActivity;

class Banner extends Model
{
    /** @use HasFactory<\Database\Factories\BannerFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Membuat atribut virtual 'full_image_url' yang berisi URL lengkap ke gambar.
     */
    protected function fullImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_url
                ? Storage::disk('public')->url($this->image_url)
                : null, // Berikan null jika karena suatu alasan tidak ada gambar
        );
    }
    
    public function activities()
    {
        return $this->morphMany(UserActivity::class, 'subject');
    }
}
