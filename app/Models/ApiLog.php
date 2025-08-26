<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    /**
     * @var bool
     * Menonaktifkan 'updated_at' karena kita tidak membutuhkannya.
     */
    public $timestamps = false;

    /**
     * @var array
     * Izinkan semua atribut untuk diisi secara massal.
     */
    protected $guarded = [];
}