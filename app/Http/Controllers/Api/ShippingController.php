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

    public function _komerce_getCities(Request $request)
    {
        $request->validate(['province_id' => 'required']);
        $response = $this->komerceService->getCities($request->province_id);
        return $response->json();
    }

    public function getSubdistricts(Request $request)
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
     * Menentukan gudang asal pengiriman berdasarkan provinsi tujuan pelanggan.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOriginWarehouse(Request $request)
    {
        // 1. Validasi input dari frontend
        $request->validate([
            'province_id' => 'required|string',
        ]);

        $provinceId = $request->province_id;
        $warehouse = null;

        // 2. Cari gudang berdasarkan cakupan provinsi
        // Kita cari di tabel WarehouseCoverage yang province_id-nya cocok
        $coverage = WarehouseCoverage::with('warehouse')
            ->where('province_id', $provinceId)
            ->first();

        if ($coverage) {
            // Jika ditemukan, gunakan gudang yang terhubung dengannya
            $warehouse = $coverage->warehouse;
        } else {
            // 3. Jika tidak ada cakupan spesifik, cari gudang default
            $warehouse = Warehouse::where('is_default', true)->first();
        }

        // 4. Jika gudang (baik spesifik maupun default) ditemukan
        if ($warehouse) {
            // Kembalikan respons dengan format yang disederhanakan
            return response()->json([
                'data' => [
                    'id' => $warehouse->id, // ID gudang untuk referensi internal jika perlu
                    'name' => $warehouse->name, // Nama gudang untuk display
                    'cityId' => $warehouse->city_id, // ID Kota asal untuk kalkulasi ongkir
                ]
            ]);
        }

        // 5. Kasus Error: Tidak ada gudang yang bisa ditentukan
        // Ini terjadi jika tidak ada cakupan & tidak ada gudang default yang di-set.
        return response()->json([
            'message' => 'Tidak dapat menentukan gudang asal pengiriman. Harap hubungi administrator.'
        ], 500); // 500 karena ini adalah kesalahan konfigurasi di server
    }
}