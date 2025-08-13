<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Video; // Impor model

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Video::truncate(); // Kosongkan tabel sebelum seeding (opsional)

        $videos = [
            [
                'video_url' => 'https://videos.pexels.com/video-files/15465878/15465878-hd_1080_1920_30fps.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/v1/300/500',
                'user_name' => '@natureLover',
                'user_avatar_url' => 'https://i.pravatar.cc/150?u=natureLover',
                'caption' => 'Menikmati keindahan pagi ini! ☀️ #sunrise #nature',
                'likes' => '1.2M',
                'comments' => '4,5K',
            ],
            [
                'video_url' => 'https://videos.pexels.com/video-files/15439741/15439741-hd_1080_1920_30fps.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/v2/300/500',
                'user_name' => '@cityWalker',
                'user_avatar_url' => 'https://i.pravatar.cc/150?u=cityWalker',
                'caption' => 'Suasana malam di tengah kota 🏙️',
                'likes' => '890K',
                'comments' => '2,1K',
            ],
            [
                'video_url' => 'https://videos.pexels.com/video-files/27868391/12251123_1080_1922_30fps.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/v2/300/500',
                'user_name' => '@cityWalker',
                'user_avatar_url' => 'https://i.pravatar.cc/150?u=cityWalker',
                'caption' => 'Suasana malam di tengah kota 🏙️',
                'likes' => '890K',
                'comments' => '2,1K',
            ],
            [
                'video_url' => 'https://videos.pexels.com/video-files/32332927/13792456_1440_2560_30fps.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/v2/300/500',
                'user_name' => '@cityWalker',
                'user_avatar_url' => 'https://i.pravatar.cc/150?u=cityWalker',
                'caption' => 'Suasana malam di tengah kota 🏙️',
                'likes' => '890K',
                'comments' => '2,1K',
            ],
            // ... Tambahkan 2 video lainnya dari data dummy Anda
        ];

        foreach ($videos as $video) {
            Video::create($video);
        }

    }
}