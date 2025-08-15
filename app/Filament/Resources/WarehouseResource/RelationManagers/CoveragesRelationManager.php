<?php
// app/Filament/Resources/WarehouseResource/RelationManagers/CoveragesRelationManager.php

namespace App\Filament\Resources\WarehouseResource\RelationManagers;

use App\Models\City;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Radio;

// Pastikan use statement ini ada
use Filament\Forms\Components\Component;

class CoveragesRelationManager extends RelationManager
{
    protected static string $relationship = 'coverages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // ... (Select untuk coverage_type, province_id_selector, city_id_selector tidak berubah) ...

                Radio::make('coverage_type')
                    ->label('Tipe Cakupan')
                    ->options([
                        'province' => 'Per Provinsi',
                        'city' => 'Per Kota',
                    ])
                    ->required()
                    ->live()
                    ->inline(),
                Forms\Components\Select::make('province_id_selector')
                    ->label('Provinsi')
                    ->options(Province::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->visible(fn (Get $get): bool => in_array($get('coverage_type'), ['province', 'city']))
                    ->dehydrated(false),
                Forms\Components\Select::make('city_id_selector')
                    ->label('Kota/Kabupaten')
                    ->options(function (Get $get): \Illuminate\Support\Collection {
                        $provinceId = $get('province_id_selector');
                        if (!$provinceId) return collect();
                        return City::where('province_id', $provinceId)->orderBy('name')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->visible(fn (Get $get): bool => $get('coverage_type') === 'city')
                    ->dehydrated(false),
            
                Forms\Components\Hidden::make('coverage_id')
                    ->dehydrateStateUsing(function (Get $get): ?string {
                        if ($get('coverage_type') === 'province') {
                            return $get('province_id_selector');
                        }
                        if ($get('coverage_type') === 'city') {
                            return $get('city_id_selector');
                        }
                        return null;
                    }),
            
                Forms\Components\Hidden::make('coverage_name')
                    // =========================================================
                    // ==> PERBAIKAN UTAMA DAN FINAL ADA DI SINI <==
                    // =========================================================
                    ->dehydrateStateUsing(function (Get $get): ?string {
                        if ($get('coverage_type') === 'province') {
                            $provinceId = $get('province_id_selector');
                            // Ambil nama langsung dari database
                            return Province::find($provinceId)?->name;
                        }
                        if ($get('coverage_type') === 'city') {
                            $cityId = $get('city_id_selector');
                            // Ambil nama langsung dari database
                            return City::find($cityId)?->name;
                        }
                        return null;
                    }),
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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}