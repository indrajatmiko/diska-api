<?php
// app/Filament/Resources/WarehouseResource.php
namespace App\Filament\Resources;

use App\Services\KomerceService;
use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log; // <-- Tambahkan ini di atas file
use App\Models\Province;
use App\Http\Resources\CityResource; // <-- Import CityResource
use App\Models\City; // <-- Import City Model
use App\Models\District; // <-- Import District Model

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Manajemen Toko'; // Kelompokkan menu

    public static function form(Form $form): Form
    {
        $komerceService = new KomerceService();

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->columnSpanFull(),

                Select::make('province_for_city')
                    ->label('Pilih Provinsi Lokasi Gudang')
                    // --- AWAL PERUBAHAN ---
                    ->options(Province::orderBy('name')->pluck('name', 'id'))
                    // --- AKHIR PERUBAHAN ---
                    ->live()
                    ->searchable()
                    ->dehydrated(false)
                    ->afterStateUpdated(fn (Set $set) => $set('city_id', null)),

                Select::make('city_id')
                    ->label('Pilih Kota Lokasi Gudang')
                    // --- AWAL PERUBAHAN ---
                    ->options(function (Get $get): Collection {
                        $provinceId = $get('province_for_city');
                        if (!$provinceId) {
                            return collect();
                        }
                        // Ambil langsung dari database
                        return City::where('province_id', $provinceId)->orderBy('name')->pluck('name', 'id');
                    })
                    // --- AKHIR PERUBAHAN ---
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state, Select $component) {
                        if (blank($state)) {
                            $set('city_name', null);
                            $set('district_id', null); // <-- Reset district jika kota berubah
                            return;
                        }
                        $label = $component->getOptionLabel($state);
                        $set('city_name', $label);
                        $set('district_id', null); // <-- Reset district jika kota berubah
                    }),

Select::make('district_id')
                    ->label('Pilih Kecamatan Lokasi Gudang')
                    ->options(function (Get $get): \Illuminate\Support\Collection {
                        $cityId = $get('city_id');
                        if (!$cityId) return collect();
                        
                        // Cek apakah data kecamatan sudah di-cache di DB
                        $districts = District::where('city_id', $cityId)->orderBy('name')->pluck('name', 'id');
                        
                        // Jika tidak ada, panggil API sebagai fallback
                        if ($districts->isEmpty()) {
                            try {
                                $response = (new \App\Services\KomerceService())->getSubdistricts($cityId);
                                if ($response->successful()) {
                                    $data = $response->json()['data'] ?? [];
                                    return collect($data)->filter(fn ($item) => !empty($item['name']))->pluck('name', 'id');
                                }
                            } catch (\Exception $e) { return collect(); }
                        }
                        return $districts;
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state, Select $component) {
                        if (blank($state)) {
                            $set('district_name', null);
                            return;
                        }
                        $label = $component->getOptionLabel($state);
                        $set('district_name', $label);
                    }),
                
                Forms\Components\Hidden::make('city_name'),
                Forms\Components\Hidden::make('district_name'), // <-- Tambahkan ini
                Forms\Components\Toggle::make('is_default')->label('Jadikan Gudang Default'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('city_name'),
                Tables\Columns\IconColumn::make('is_default')->boolean()->label('Default'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
    
    // Daftarkan Relation Manager di sini
    public static function getRelations(): array
    {
        return [
            RelationManagers\CoveragesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}