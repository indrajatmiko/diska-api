<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Aplikasi';
    protected static ?int $navigationSort = 4; // Urutan setelah Products

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Ulasan')
                    ->schema([
                        // =========================================================
                        // ==> TAMBAHKAN DROPDOWN PRODUK DI SINI <==
                        // =========================================================
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name') // Hubungkan ke relasi 'product' & tampilkan 'name'
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\DateTimePicker::make('review_date')
                            // ... (tidak ada perubahan)
                            ->label('Tanggal Rating')
                            ->required()
                            ->default(now()),

                        Forms\Components\Radio::make('rating')
                            // ... (tidak ada perubahan)
                            ->label('Rating Bintang')
                            ->options([
                                '1' => '1 Bintang', '2' => '2 Bintang', '3' => '3 Bintang', '4' => '4 Bintang', '5' => '5 Bintang',
                            ])
                            ->columns(5)
                            ->required(),
                        
                        Forms\Components\Textarea::make('description')
                            // ... (tidak ada perubahan)
                            ->label('Deskripsi Ulasan')
                            ->rows(5)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // =========================================================
                // ==> TAMBAHKAN KOLOM NAMA PRODUK DI SINI <==
                // =========================================================
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('rating')
                    // ... (tidak ada perubahan)
                    ->label('Rating')
                    ->formatStateUsing(fn (string $state): string => "{$state} ★")
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('review_date')
                    // ... (tidak ada perubahan)
                    ->label('Tanggal Rating')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
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
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }    
}