<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get; // Import Get

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Manajemen Toko';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Voucher')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Voucher')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Voucher')
                            ->default(true)
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Aturan & Nilai')
                    ->schema([
                        Forms\Components\Radio::make('type')
                        ->label('Jenis & Tipe Voucher')
                        ->options([
                            'product_fixed' => 'Diskon Produk (Rp)',
                            'product_percentage' => 'Diskon Produk (%)',
                            'shipping_fixed' => 'Subsidi Ongkir (Rp)',
                        ])
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('value')
                        ->label('Nilai Diskon')
                        ->required()
                        ->numeric()
                        // Tampilkan prefix/suffix berdasarkan tipe yang dipilih
                        ->prefix(fn (Get $get) => in_array($get('type'), ['product_fixed', 'shipping_fixed']) ? 'Rp' : null)
                        ->suffix(fn (Get $get) => $get('type') === 'product_percentage' ? '%' : null),
                    Forms\Components\TextInput::make('min_purchase')
                        ->label('Minimal Pembelian (Subtotal Produk)')
                        ->helperText('Hanya berlaku untuk total harga produk, sebelum ongkir.')
                        ->numeric()
                        ->default(0)
                        ->prefix('Rp'),
                    ])->columns(3),
                Forms\Components\Section::make('Periode Berlaku')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Tanggal Berakhir')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable(),
                Tables\Columns\TextColumn::make('description')->limit(30),
                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->formatStateUsing(fn (Voucher $record): string => 
                        $record->type === 'fixed' ? 'Rp ' . number_format($record->value) : "{$record->value}%"
                    ),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Aktif'),
                Tables\Columns\TextColumn::make('end_date')->dateTime()->label('Berakhir Pada'),
            ])
            ->filters([])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }    
}