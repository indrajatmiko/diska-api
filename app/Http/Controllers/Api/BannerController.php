<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerDetailResource;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit');

        $query = Banner::latest();

        if ($limit) {
            $banners = $query->take($limit)->get();
        } else {
            $banners = $query->get();
        }

        return BannerResource::collection($banners);
    }

    public function show(Banner $banner)
    {
        return new BannerResource($banner);
    }

    /**
     * **[BARU]** Display a listing of related banners.
     * Menampilkan daftar banner terkait, mengecualikan banner saat ini.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Banner $banner
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function related(Request $request, Banner $banner)
    {
        // Ambil query parameter 'limit', default ke 4 jika tidak ada
        $limit = $request->query('limit', 4);

        // 1. Mulai query ke model Banner
        // 2. `where('id', '!=', $banner->id)` adalah kuncinya.
        //    Ini akan mengecualikan banner yang sedang dilihat dari hasil.
        $relatedBanners = Banner::where('id', '!=', $banner->id)
            ->latest() // Urutkan dari yang terbaru
            ->take($limit) // Ambil sejumlah 'limit'
            ->get();

        // Gunakan BannerResource yang sama dengan daftar utama
        return BannerResource::collection($relatedBanners);
    }
}