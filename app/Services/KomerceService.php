<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KomerceService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.komerce.api_key');
        $this->baseUrl = 'https://rajaongkir.komerce.id/api/v1/destination'; // Base URL dari dokumentasi
    }

    protected function makeRequest(string $method, string $endpoint, array $data = [])
    {
        $request = Http::withHeaders([
            'key' => $this->apiKey,
        ])->acceptJson();

        if ($method === 'GET') {
            return $request->get($this->baseUrl . $endpoint, $data);
        }

        if ($method === 'POST') {
            return $request->post($this->baseUrl . $endpoint, $data);
        }
        
        return null;
    }

    public function getProvinces()
    {
        return $this->makeRequest('GET', '/province');
    }

    /**
     * Mengambil kota berdasarkan ID Provinsi.
     * ID Provinsi menjadi bagian dari URL.
     */
    public function getCities(string $provinceId)
    {
        // BENAR: Menyusun endpoint dengan ID provinsi di dalamnya.
        // Hasil: /city/1
        $endpoint = "/city/{$provinceId}";
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Mengambil kecamatan berdasarkan ID Kota.
     * ID Kota menjadi bagian dari URL.
     */
    public function getSubdistricts(string $cityId)
    {
        // BENAR: Menyusun endpoint dengan ID kota di dalamnya.
        // Hasil: /district/149
        $endpoint = "/district/{$cityId}";
        return $this->makeRequest('GET', $endpoint);
    }

    public function getShippingCost(array $data)
    {
        // Data harus berisi: origin, destination, weight, courier
        return $this->makeRequest('POST', '/domestic-cost', $data);
    }
}