<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Reseller extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Membuat atribut virtual 'full_image_url' yang berisi URL lengkap ke gambar.
     */
    protected function fullImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_path
                ? Storage::disk('public')->url($this->image_path)
                : null,
        );
    }
}