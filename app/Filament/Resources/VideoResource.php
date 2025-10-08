<?php

// app/Filament/Resources/VideoResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationGroup = 'Aplikasi';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Konten Video')
                    ->schema([
                        // Jika video di-host eksternal (misal: URL .mp4 dari CDN)
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video URL')
                            ->url() // Validasi sebagai URL
                            ->required(),

                        // Upload thumbnail kustom untuk video
                        Forms\Components\FileUpload::make('thumbnail_url')
                            ->label('Thumbnail Image')
                            ->image()
                            ->disk('public')
                            ->directory('video-thumbnails')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']) // Allow webp
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Pengguna & Teks')
                    ->schema([
                        Forms\Components\TextInput::make('user_name')
                            ->label('Username')
                            ->required()
                            ->placeholder('@username'),
                        
                        // Upload avatar pengguna
                        Forms\Components\FileUpload::make('user_avatar_url')
                            ->label('User Avatar')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('user-avatars')
                            ->imageEditor(), // Aktifkan editor gambar
                        
                        Forms\Components\Textarea::make('caption')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Statistik (Display Only)')
                    ->schema([
                        // Admin bisa mengatur nilai awal jika diperlukan
                        Forms\Components\TextInput::make('likes')->numeric()->default(0),
                        Forms\Components\TextInput::make('comments')->numeric()->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_url')->label('Thumbnail'),
                Tables\Columns\TextColumn::make('caption')->limit(50)->searchable(),
                Tables\Columns\TextColumn::make('user_name')->label('Username')->searchable(),
                Tables\Columns\TextColumn::make('likes')->sortable(),
                Tables\Columns\TextColumn::make('comments')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(), // Tambahkan view untuk melihat detail
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}