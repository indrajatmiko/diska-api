<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    // Menonaktifkan auto-increment karena kita menggunakan ID dari RajaOngkir
    public $incrementing = false;
    
    // Izinkan mass assignment untuk semua kolom
    protected $guarded = [];

    /**
     * Mendefinisikan relasi bahwa satu provinsi memiliki banyak kota.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}