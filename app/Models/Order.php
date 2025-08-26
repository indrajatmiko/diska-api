<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\OrderStatus;

class Order extends Model
{
    use HasFactory;

    use HasFactory;

    // Jika Anda menggunakan $guarded, tidak perlu diubah.
    // Jika menggunakan $fillable, tambahkan field baru.
    protected $fillable = [
        'user_id',
        'status',
        'total_amount',
        'shipping_cost',
        'courier',
        'shipping_service',
        'tracking_number',
        'product_voucher_code',  // <-- TAMBAHKAN INI
        'shipping_voucher_code', // <-- TAMBAHKAN INI
        'province',
        'city',
        'district',
        'subdistrict',
        'postal_code',
        'address_detail',
    ];
    
    protected $casts = [
        'shipping_address' => 'array', // Otomatis cast JSON ke array
        'status' => OrderStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}