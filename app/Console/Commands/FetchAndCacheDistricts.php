<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Services\KomerceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchAndCacheDistricts extends Command
{
    /**
     * DATA YANG SUDAH DIAMBIL DARI KOMERCE API
     * 
     * php artisan app:fetch-and-cache-districts 5 10 11 12 18
     * Mengambil data kota untuk provinsi: JAWA BARAT, DKI JAKARTA, BANTEN, JAWA TENGAH, JAWA TIMUR
     * 8 kepulauan riau
     * 25 riau
     * 26 sumatera selatan
     * 30 lampung
     * 19 jogyakarta
     * 16 sumatera utara
     * 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21
    */

    /**
     * Signature command, menerima satu atau lebih ID provinsi.
     * Contoh: php artisan app:fetch-and-cache-districts 5 10 11
     *
     * @var string
     */
    protected $signature = 'app:fetch-and-cache-districts {provinces* : The list of province IDs to fetch districts for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all districts from Komerce API for cities within specified provinces and cache them locally.';

    /**
     * Execute the console command.
     */
    public function handle(KomerceService $komerceService)
    {
        $provinceIds = $this->argument('provinces');
        if (empty($provinceIds)) {
            $this->error('Harap masukkan setidaknya satu ID Provinsi. Contoh: php artisan app:fetch-and-cache-districts 5');
            return 1;
        }

        // Validasi apakah provinsi yang dimasukkan ada di database
        $provinces = Province::whereIn('id', $provinceIds)->get();
        if ($provinces->count() !== count($provinceIds)) {
            $this->error('Satu atau lebih ID Provinsi tidak ditemukan di database.');
            return 1;
        }
        
        $this->info('Mengambil data kota untuk provinsi: ' . $provinces->pluck('name')->join(', '));

        // Ambil semua kota dari provinsi yang dipilih
        $cities = City::whereIn('province_id', $provinceIds)->get();
        if ($cities->isEmpty()) {
            $this->error('Tidak ada kota yang ditemukan untuk provinsi yang dipilih. Harap jalankan app:fetch-and-cache-cities terlebih dahulu.');
            return 1;
        }

        $this->info("Ditemukan {$cities->count()} kota. Memulai proses pengambilan data kecamatan...");

        $progressBar = $this->output->createProgressBar($cities->count());
        $progressBar->start();

        foreach ($cities as $city) {
            try {
                $response = $komerceService->getDistricts($city->id);

                if ($response->successful()) {
                    $districtsData = $response->json()['data'] ?? [];

                    foreach ($districtsData as $districtData) {
                        if (empty($districtData['id']) || empty($districtData['name'])) {
                            continue;
                        }
                        District::updateOrCreate(
                            ['id' => $districtData['id']],
                            [
                                'city_id' => $city->id,
                                'name' => $districtData['name'],
                            ]
                        );
                    }
                } else {
                    $this->warn("\n[Gagal] Kota: {$city->name} (ID: {$city->id}). Status: " . $response->status());
                    Log::warning("Failed to fetch districts for city ID {$city->id}.");
                }

            } catch (\Exception $e) {
                $this->error("\n[Error] Kota: {$city->name}. Pesan: " . $e->getMessage());
                Log::error("Exception while fetching districts for city ID {$city->id}: " . $e->getMessage());
            }

            $progressBar->advance();
            // Jeda 500ms agar tidak membanjiri API
            usleep(500000); 
        }

        $progressBar->finish();
        $this->info("\n\nProses selesai. Semua data kecamatan untuk provinsi yang dipilih telah berhasil diambil.");

        return 0;
    }
}