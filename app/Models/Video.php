<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserActivity;

class Video extends Model
{
    /** @use HasFactory<\Database\Factories\VideoFactory> */
    use HasFactory;

    protected $fillable = [
        'video_url',
        // tambahkan field lain jika perlu, misal:
        'thumbnail_url', 'user_name', 'user_avatar_url', 'caption', 'likes', 'comments'
    ];

    public function activities()
    {
        return $this->morphMany(UserActivity::class, 'subject');
    }
}
