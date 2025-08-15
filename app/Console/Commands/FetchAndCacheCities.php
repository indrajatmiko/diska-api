<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Province;
use App\Services\KomerceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchAndCacheCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-and-cache-cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all cities from Komerce API for every province in the database and cache them locally.';

    /**
     * Execute the console command.
     */
    public function handle(KomerceService $komerceService)
    {
        $this->info('Memulai proses pengambilan data kota...');

        // Ambil semua provinsi dari database lokal
        $provinces = Province::all();
        if ($provinces->isEmpty()) {
            $this->error('Tabel provinsi kosong. Harap jalankan ProvinceSeeder terlebih dahulu.');
            return 1;
        }

        // Buat progress bar agar proses terlihat
        $progressBar = $this->output->createProgressBar($provinces->count());
        $progressBar->start();

        foreach ($provinces as $province) {
            try {
                $response = $komerceService->getCities($province->id);

                if ($response->successful()) {
                    $citiesData = $response->json()['data'] ?? [];

                    foreach ($citiesData as $cityData) {
                        if (empty($cityData['id']) || empty($cityData['name'])) {
                            continue; // Lewati data yang tidak valid
                        }

                        // Gunakan updateOrCreate untuk keamanan
                        City::updateOrCreate(
                            ['id' => $cityData['id']],
                            [
                                'province_id' => $province->id,
                                'name' => $cityData['name'],
                            ]
                        );
                    }
                } else {
                    $this->warn("\nGagal mengambil data untuk provinsi: {$province->name} (ID: {$province->id})");
                    Log::warning("Failed to fetch cities for province ID {$province->id}. Status: " . $response->status());
                }

            } catch (\Exception $e) {
                $this->error("\nTerjadi error saat memproses provinsi: {$province->name}. Pesan: " . $e->getMessage());
                Log::error("Exception while fetching cities for province ID {$province->id}: " . $e->getMessage());
            }

            // Majukan progress bar
            $progressBar->advance();
            // Beri jeda sedikit agar tidak membanjiri API
            sleep(1); 
        }

        $progressBar->finish();
        $this->info("\n\nProses selesai. Semua data kota telah berhasil diambil dan disimpan.");

        return 0;
    }
}