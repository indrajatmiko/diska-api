<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Varian (e.g., S, Merah)')
                    ->required()
                    ->maxLength(255),
                // Letakkan harga bersebelahan untuk UI yang lebih baik
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Harga Jual')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                    
                    // =========================================================
                    // ==> TAMBAHKAN INPUT BARU DI SINI <==
                    // =========================================================
                    Forms\Components\TextInput::make('original_price')
                        ->label('Harga Asli (Opsional)')
                        ->helperText('Isi untuk menampilkan harga coret.')
                        ->numeric()
                        ->prefix('Rp'),
                ]),
                Forms\Components\TextInput::make('sku')
                    ->label('SKU (Stock Keeping Unit)')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('stock')
                    ->label('Stok')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('price')->money('IDR')->label('Harga Jual'),
                Tables\Columns\TextColumn::make('original_price')
                    ->money('IDR')
                    ->label('Harga Asli')
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('sku'),
                Tables\Columns\TextColumn::make('stock'),
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