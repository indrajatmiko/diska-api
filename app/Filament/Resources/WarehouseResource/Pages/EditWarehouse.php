<?php

namespace App\Filament\Resources\WarehouseResource\Pages;

use App\Filament\Resources\WarehouseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\City;

class EditWarehouse extends EditRecord
{
    protected static string $resource = WarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Modifikasi data sebelum form diisi.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 1. Dapatkan ID Kota dari data yang ada di database.
        $cityId = $data['city_id'];

        // 2. Jika karena suatu alasan kota tidak ada, jangan lakukan apa-apa.
        if (blank($cityId)) {
            return $data;
        }

        // 3. Cari kota di database.
        // Kita gunakan 'with' untuk efisiensi, mengambil provinsi sekaligus.
        $city = City::with('province')->find($cityId);

        // 4. Jika kota ditemukan, temukan ID provinsinya.
        if ($city) {
            // 5. Tambahkan kunci 'province_for_city' ke dalam data array.
            // Ini akan secara otomatis memilih provinsi yang benar saat form dimuat.
            $data['province_for_city'] = $city->province_id;
        }

        // 6. Kembalikan data yang sudah dimodifikasi.
        return $data;
    }
}