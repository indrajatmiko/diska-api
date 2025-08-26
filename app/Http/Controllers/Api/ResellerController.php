<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResellerResource;
use App\Models\Reseller;

class ResellerController extends Controller
{
    public function index()
    {
        $resellers = Reseller::latest()->get(); // Ambil data, urutkan dari yang terbaru
        return ResellerResource::collection($resellers);
    }
}