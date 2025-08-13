<?php
// app/Filament/Resources/WarehouseResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Manajemen Toko'; // Kelompokkan menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\TextInput::make('city_id')->label('ID Kota RajaOngkir')->required(),
                Forms\Components\TextInput::make('city_name')->label('Nama Kota')->required(),
                Forms\Components\Toggle::make('is_default')
                    ->label('Jadikan Gudang Default')
                    ->helperText('Gudang ini akan digunakan jika wilayah pelanggan tidak tercakup.'),
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