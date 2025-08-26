<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Tambahkan casting
    protected $casts = [
        'show_in_menu' => 'boolean',
    ];
}