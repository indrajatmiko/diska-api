<?php
namespace App\Filament\Resources\WarehouseResource\RelationManagers;

use App\Services\KomerceService; // <-- Gunakan Service kita
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CoveragesRelationManager extends RelationManager
{
    protected static string $relationship = 'coverages';

    public function form(Form $form): Form
    {
        // Panggil API Komerce untuk mendapatkan daftar provinsi
        $komerceService = new KomerceService();
        $provincesResponse = $komerceService->getProvinces();
        $provinces = [];
        if ($provincesResponse->successful()) {
            foreach ($provincesResponse->json()['data'] as $province) {
                // Key adalah province_id, Value adalah province_name
                $provinces[$province['province_id']] = $province['province'];
            }
        }

        return $form
            ->schema([
                Forms\Components\Select::make('province_id')
                    ->label('Provinsi yang Dicakup')
                    ->options($provinces) // <-- Isi pilihan dari API
                    ->searchable() // Aktifkan pencarian
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('province_id')
            ->columns([
                Tables\Columns\TextColumn::make('province_id'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}