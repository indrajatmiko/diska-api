<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Services\ActivityLoggerService;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ActivityLoggerService $activityLogger)
    {
        // Ambil parameter `limit` dari URL, default-nya null (ambil semua)
        $limit = $request->query('limit');
        
        if ($limit) {
            // Jika ada limit, gunakan paginate
            $videos = Video::inRandomOrder()->paginate($limit);
        } else {
            // Jika tidak ada limit, ambil semua
            $videos = Video::inRandomOrder()->get();
        }
        
        // $activityLogger->log('video_viewed', $videos);
        // Gunakan resource untuk memformat koleksi data
        return VideoResource::collection($videos);
    }
}