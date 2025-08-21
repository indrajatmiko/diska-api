<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Review extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Pastikan Laravel menangani kolom tanggal dengan benar
    protected $casts = [
        'review_date' => 'datetime',
    ];

   /**
     * Sebuah ulasan dimiliki oleh satu produk.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}