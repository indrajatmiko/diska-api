<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KomerceService; // <-- Import service kita
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\WarehouseCoverage;
use App\Models\Province; // <-- 1. Tambahkan import untuk model Province
use App\Http\Resources\ProvinceResource; // <-- 2. Tambahkan import untuk Resource
use App\Http\Resources\CityResource; // <-- Import CityResource
use App\Models\City; // <-- Import City Model
use App\Http\Resources\DistrictResource; // <-- Import DistrictResource
use App\Models\District; // <-- Import District Model
use App\Http\Resources\OriginWarehouseResource; // <-- Import OriginWarehouseResource

class ShippingController extends Controller
{
    protected KomerceService $komerceService;

    public function __construct(KomerceService $komerceService)
    {
        $this->komerceService = $komerceService;
    }

    public function _komerce_getProvinces()
    {
        $response = $this->komerceService->getProvinces();
        return $response->json();
    }

    public function getProvinces()
    {
        $provinces = Province::orderBy('name')->get();
        return ProvinceResource::collection($provinces);
    }

    public function getCities(Request $request)
    {
        $request->validate(['province_id' => 'required|exists:provinces,id']);

        $cities = City::where('province_id', $request->province_id)
            ->orderBy('name')
            ->get();
            
        return CityResource::collection($cities);
    }

    /**
     * Mengambil daftar kecamatan berdasarkan ID Kota/Kabupaten dari database lokal.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function getDistricts(Request $request)
    {
        // 3. Validasi input: pastikan city_id ada dan valid
        $request->validate(['city_id' => 'required|exists:cities,id']);

        // 4. Ambil semua kecamatan yang city_id-nya cocok, urutkan berdasarkan nama
        $districts = District::where('city_id', $request->city_id)
            ->orderBy('name')
            ->get();

        // (Opsional) Jika tidak ada data kecamatan untuk kota tersebut di cache
        if ($districts->isEmpty()) {
            // Kita bisa mengembalikan array kosong dengan pesan yang jelas
            return response()->json([
                'data' => [],
                'meta' => [
                    'message' => 'Data kecamatan untuk kota ini belum tersedia di cache lokal.'
                ]
            ]);
        }
            
        // 5. Kembalikan data menggunakan Resource Collection
        // Ini akan otomatis membungkus hasilnya dalam kunci "data"
        return DistrictResource::collection($districts);
    }

    public function _komerce_getCities(Request $request)
    {
        $request->validate(['province_id' => 'required']);
        $response = $this->komerceService->getCities($request->province_id);
        return $response->json();
    }

    public function _komerce_getSubdistricts(Request $request)
    {
        $request->validate(['city_id' => 'required']);
        $response = $this->komerceService->getSubdistricts($request->city_id);
        return $response->json();
    }

    public function getCost(Request $request)
    {
        $validatedData = $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'weight' => 'required|integer',
            'courier' => 'required|string',
        ]);

        $response = $this->komerceService->getShippingCost($validatedData);
        // Anda bisa melakukan simplifikasi respons di sini jika perlu
        return $response->json();
    }

        /**
     * Menentukan gudang pengiriman yang optimal berdasarkan lokasi tujuan pelanggan.
     * Logika: Cek cakupan Kota -> Cek cakupan Provinsi -> Gunakan Default.
     */
    public function getOriginWarehouse(Request $request)
    {
        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
        ]);

        $cityId = $validated['city_id'];
        $provinceId = $validated['province_id'];
        $warehouse = null;

        // 1. Cek cakupan level KOTA (paling spesifik)
        $warehouse = Warehouse::whereHas('coverages', function ($query) use ($cityId) {
            $query->where('coverage_type', 'city')->where('coverage_id', $cityId);
        })->first();

        // 2. Jika tidak ditemukan, cek cakupan level PROVINSI
        if (!$warehouse) {
            $warehouse = Warehouse::whereHas('coverages', function ($query) use ($provinceId) {
                $query->where('coverage_type', 'province')->where('coverage_id', $provinceId);
            })->first();
        }

        // 3. Jika masih tidak ditemukan, cari GUDANG DEFAULT
        if (!$warehouse) {
            $warehouse = Warehouse::where('is_default', true)->first();
        }

        // 4. Jika gudang berhasil ditentukan
        if ($warehouse) {
            return new OriginWarehouseResource($warehouse);
        }

        // 5. Kasus Error: Tidak ada gudang yang bisa ditentukan (kesalahan konfigurasi)
        return response()->json([
            'message' => 'Tidak dapat menentukan gudang asal pengiriman. Harap hubungi administrator.'
        ], 500);
    }
}