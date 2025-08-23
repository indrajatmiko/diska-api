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
        // PERBAIKAN: Gunakan base URL yang lebih tinggi (level v1)
        $this->baseUrl = 'https://rajaongkir.komerce.id/api/v1'; 
    }

    /**
     * Metode internal ini tidak perlu diubah.
     */
    protected function makeRequest(string $method, string $endpoint, array $data = [])
    {
        $request = Http::withHeaders([
            'key' => $this->apiKey,
        ])->acceptJson()->asForm();

        $fullUrl = $this->baseUrl . $endpoint;

        if ($method === 'GET') {
            return $request->get($fullUrl);
        }

        if ($method === 'POST') {
            return $request->post($fullUrl, $data);
        }
        
        return null;
    }

    /**
     * Mengambil semua provinsi.
     */
    public function getProvinces()
    {
        // PERBAIKAN: Sertakan path '/destination' di sini
        return $this->makeRequest('GET', '/destination/province');
    }

    /**
     * Mengambil kota berdasarkan ID Provinsi.
     */
    public function getCities(string $provinceId)
    {
        // PERBAIKAN: Sertakan path '/destination' di sini
        $endpoint = "/destination/city/{$provinceId}";
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Mengambil kecamatan berdasarkan ID Kota.
     */
    public function getDistricts(string $cityId) // Anda mungkin menamai ini getDistricts
    {
        // PERBAIKAN: Sertakan path '/destination' di sini
        $endpoint = "/destination/district/{$cityId}";
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Menghitung ongkos kirim.
     */
    public function getShippingCost(array $data)
    {
        // =========================================================
        // ==> PERBAIKAN UTAMA DAN PALING PENTING ADA DI SINI <==
        // =========================================================
        // BENAR: Menggunakan path '/calculate/district/domestic-cost' yang benar
        return $this->makeRequest('POST', '/calculate/district/domestic-cost', $data);
    }
}