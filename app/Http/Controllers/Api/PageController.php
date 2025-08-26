<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageMenuResource; // <-- Import resource baru
use App\Http\Resources\PageResource;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Mengambil daftar halaman yang ditandai untuk ditampilkan di menu.
     */
    public function menu()
    {
        $menuPages = Page::where('show_in_menu', true)
            ->orderBy('title') // Urutkan berdasarkan judul
            ->get();

        return PageMenuResource::collection($menuPages);
    }

    /**
     * Menampilkan konten detail satu halaman.
     */
    public function show(string $key)
    {
        $page = Page::where('key', $key)->firstOrFail();
        return new PageResource($page);
    }
}