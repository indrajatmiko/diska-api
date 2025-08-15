<?php

namespace App\Filament\Resources\WarehouseResource\RelationManagers;

use App\Models\City;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class CoveragesRelationManager extends RelationManager
{
    protected static string $relationship = 'coverages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Radio::make('coverage_type')
                    ->label('Tipe Cakupan')
                    ->options([
                        'province' => 'Per Beberapa Provinsi', // <-- Ubah label agar lebih jelas
                        'city' => 'Per Beberapa Kota',
                    ])
                    ->required()
                    ->live()
                    ->inline(),

                Forms\Components\Select::make('province_id_selector')
                    ->label('Provinsi')
                    // =========================================================
                    // ==> PERUBAHAN UTAMA #1: TAMBAHKAN multiple() <==
                    // =========================================================
                    ->multiple() 
                    ->options(function (RelationManager $livewire): \Illuminate\Support\Collection {
                        $warehouseId = $livewire->getOwnerRecord()->id;
                        $existingProvinceIds = \App\Models\WarehouseCoverage::where('warehouse_id', $warehouseId)
                            ->where('coverage_type', 'province')
                            ->pluck('coverage_id')
                            ->all();
                        return Province::whereNotIn('id', $existingProvinceIds)
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->live()
                    ->visible(fn (Get $get): bool => in_array($get('coverage_type'), ['province', 'city'])),

                Forms\Components\Select::make('city_id_selector')
                    ->label('Kota/Kabupaten')
                    ->multiple()
                    ->options(function (Get $get, RelationManager $livewire): \Illuminate\Support\Collection {
                        // Logika ini tidak berubah dan sudah benar
                        $provinceId = $get('province_id_selector');
                        if (!$provinceId || !is_array($provinceId) || count($provinceId) !== 1) {
                            // Hanya tampilkan kota jika TEPAT SATU provinsi dipilih
                            // untuk menghindari kebingungan UI
                            return collect();
                        }
                        $warehouseId = $livewire->getOwnerRecord()->id;
                        $existingCityIds = \App\Models\WarehouseCoverage::where('warehouse_id', $warehouseId)
                            ->where('coverage_type', 'city')
                            ->pluck('coverage_id')
                            ->all();
                        return City::where('province_id', $provinceId[0])
                            ->whereNotIn('id', $existingCityIds)
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->visible(fn (Get $get): bool => $get('coverage_type') === 'city'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('coverage_name')
            ->columns([
                Tables\Columns\TextColumn::make('coverage_type')->label('Tipe'),
                Tables\Columns\TextColumn::make('coverage_name')->label('Wilayah Cakupan'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Cakupan Baru')
                    ->using(function (array $data, RelationManager $livewire): void {
                        $warehouse = $livewire->getOwnerRecord();
                        
                        if ($data['coverage_type'] === 'province') {
                            // =========================================================
                            // ==> PERUBAHAN UTAMA #2: TANGANI ARRAY PROVINSI <==
                            // =========================================================
                            // Logika sekarang harus menangani array, bukan satu ID
                            $provinceIds = $data['province_id_selector'];
                            if (empty($provinceIds)) return;

                            foreach ($provinceIds as $provinceId) {
                                $province = Province::find($provinceId);
                                if ($province) {
                                    $warehouse->coverages()->create([
                                        'coverage_type' => 'province',
                                        'coverage_id' => $province->id,
                                        'coverage_name' => $province->name,
                                    ]);
                                }
                            }

                        } elseif ($data['coverage_type'] === 'city') {
                            // Logika ini tidak berubah dan sudah benar
                            $cityIds = $data['city_id_selector'];
                            if (empty($cityIds)) return;

                            foreach ($cityIds as $cityId) {
                                $city = City::find($cityId);
                                if ($city) {
                                    $warehouse->coverages()->create([
                                        'coverage_type' => 'city',
                                        'coverage_id' => $city->id,
                                        'coverage_name' => $city->name,
                                    ]);
                                }
                            }
                        }

                        Notification::make()
                            ->title('Cakupan wilayah berhasil disimpan')
                            ->success()
                            ->send();
                    })
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}