<?php

// app/Filament/Resources/OrderResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product; // Import Product model
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Enums\OrderStatus;
use Filament\Forms\Components\Textarea;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Manajemen Toko';
    protected static ?int $navigationSort = 0; // Letakkan di paling atas sidebar

    // Nonaktifkan tombol "New Order" di halaman index
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    // BLOK UTAMA
                    Section::make('Detail Pesanan')
                        ->schema([
                            // Order ID & User, tidak bisa diubah
                            TextInput::make('id')->label('Order ID')->disabled(),
                            Select::make('user_id')
                                ->relationship('user', 'name')
                                ->label('Pelanggan')
                                ->disabled(),
                            
                            // Status Pesanan (INI YANG BISA DIUBAH ADMIN)
                            Select::make('status')
                                ->options(OrderStatus::class) // <-- Jadi sesederhana ini
                                ->required(),
                            
                            TextInput::make('courier')->label('Kurir')->disabled(),
                            TextInput::make('product_voucher_code')
                                ->label('Voucher Produk')
                                ->placeholder('-') // Tampilkan strip jika kosong
                                ->disabled(),
                            TextInput::make('shipping_voucher_code')
                                ->label('Voucher Ongkir')
                                ->placeholder('-') // Tampilkan strip jika kosong
                                ->disabled(),
                            TextInput::make('total_amount')->label('Total Bayar')->numeric()->prefix('Rp')->disabled(),
                            TextInput::make('shipping_cost')->label('Ongkos Kirim')->numeric()->prefix('Rp')->disabled(),
                        ])->columnSpan(2),

                    // BLOK SISI KANAN
                    Section::make('Alamat Pengiriman')
                        ->schema([
                            // Ganti KeyValue dengan TextInput individual
                            TextInput::make('province')->label('Provinsi')->disabled(),
                            TextInput::make('city')->label('Kota/Kabupaten')->disabled(),
                            TextInput::make('district')->label('Kecamatan')->disabled(),
                            TextInput::make('subdistrict')->label('Kelurahan/Desa')->disabled(),
                            TextInput::make('postal_code')->label('Kode Pos')->disabled(),
                            // Gunakan Textarea untuk alamat detail agar lebih luas
                            Textarea::make('address_detail')
                                ->label('Alamat Lengkap')
                                ->rows(4) // Beri beberapa baris
                                ->disabled(),
                        ])->columnSpan(1),
                ]),

                // BLOK ITEM PESANAN
                Section::make('Item Pesanan')
                    ->schema([
                        // Tampilkan item yang dipesan dalam format tabel (read-only)
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->label('Produk')
                                    ->disabled(),
                                TextInput::make('quantity')->label('Jumlah')->disabled(),
                                TextInput::make('price')->label('Harga Satuan')->numeric()->prefix('Rp')->disabled(),
                            ])
                            ->columns(3)
                            ->deletable(false) // tidak bisa hapus item
                            ->addable(false) // tidak bisa tambah item
                            ->label(''),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order ID')->searchable(),
                // Tampilkan nama user melalui relasi
                TextColumn::make('user.name')->label('Pelanggan')->searchable(),
                
                // Gunakan BadgeColumn untuk status agar lebih visual
                BadgeColumn::make('status')
                    ->searchable(),

                TextColumn::make('total_amount')->label('Total Bayar')->money('IDR')->sortable(),
                TextColumn::make('created_at')->label('Tanggal Pesan')->dateTime()->sortable(),
            ])
            ->filters([
                // Filter pesanan berdasarkan status
                SelectFilter::make('status')
                    ->options(OrderStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Admin bisa edit (untuk ubah status)
                Tables\Actions\ViewAction::make(), // Dan juga melihat detail
            ])
            ->bulkActions([
                // Bulk delete tidak disarankan untuk pesanan
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // Hanya aktifkan halaman index dan edit
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // 'create' => Pages\CreateOrder::route('/create'), // JANGAN AKTIFKAN
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}