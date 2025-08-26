<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Konten'; // Grup baru untuk konten

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Penanda Halaman (Key)')
                            ->helperText('Gunakan format slug (contoh: "bantuan", "tentang-kami").')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Halaman')
                            ->required(),
                    ])->columns(2),

                // =========================================================
                // ==> TAMBAHKAN SECTION BARU UNTUK PENGATURAN MENU <==
                // =========================================================
                Forms\Components\Section::make('Pengaturan Menu Profil')
                    ->schema([
                        Forms\Components\Toggle::make('show_in_menu')
                            ->label('Tampilkan halaman ini di menu profil'),
                        Forms\Components\TextInput::make('icon')
                            ->label('Nama Ikon (Material Icons)')
                            ->helperText('Terseedia: help info description warning favorite error check. Kosongkan jika tidak ada.'),
                    ])->columns(2),
                
                Forms\Components\RichEditor::make('content')
                    ->label('Konten / Deskripsi Halaman')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('Penanda (Key)')->searchable(),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
                
                // Tambahkan kolom ikon untuk melihat status menu
                Tables\Columns\IconColumn::make('show_in_menu')
                    ->label('Di Menu')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')->label('Terakhir Diperbarui')->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }    
}