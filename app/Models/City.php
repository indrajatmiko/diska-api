<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasFactory;

    // Menonaktifkan auto-increment
    public $incrementing = false;

    // Izinkan mass assignment
    protected $guarded = [];

    /**
     * Mendefinisikan relasi bahwa sebuah kota milik satu provinsi.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }
}