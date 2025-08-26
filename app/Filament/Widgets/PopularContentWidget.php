<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Banner;
use App\Models\Video;
use App\Models\UserActivity;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Tabs\Tab;

class PopularContentWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Konten Paling Populer (Berdasarkan Jumlah Dilihat)';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => null)
            ->heading('Konten Paling Populer')
            ->tabs([
                'Produk' => $this->buildProductTab(),
                'Banner' => $this->buildBannerTab(),
                'Video' => $this->buildVideoTab(),
            ])
            ->defaultTab('Produk')
            ->paginated(false);
    }

    /**
     * Membangun konfigurasi tabel untuk tab Produk.
     */
    protected function buildProductTab(): Tab
    {
        return Tab::make() // Sekarang PHP tahu apa itu 'Tab'
            ->query(
                Product::query()
                    ->withCount(['activities as views_count' => function (Builder $query) {
                        $query->where('event_type', 'product_viewed');
                    }])
                    ->orderByDesc('views_count')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Produk'),
                Tables\Columns\TextColumn::make('views_count')->label('Jumlah Dilihat')->sortable(),
            ]);
    }

    /**
     * Membangun konfigurasi tabel untuk tab Banner.
     */
    protected function buildBannerTab(): Tab
    {
        return Tab::make() // Sekarang PHP tahu apa itu 'Tab'
            ->query(
                Banner::query()
                    ->withCount(['activities as views_count' => function (Builder $query) {
                        $query->where('event_type', 'banner_viewed');
                    }])
                    ->orderByDesc('views_count')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul Banner'),
                Tables\Columns\TextColumn::make('views_count')->label('Jumlah Dilihat')->sortable(),
            ]);
    }

    /**
     * Membangun konfigurasi tabel untuk tab Video.
     */
    protected function buildVideoTab(): Tab
    {
        return Tab::make() // Sekarang PHP tahu apa itu 'Tab'
            ->query(
                Video::query()
                    ->withCount(['activities as views_count' => function (Builder $query) {
                        $query->where('event_type', 'video_viewed');
                    }])
                    ->orderByDesc('views_count')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('caption')->label('Caption Video')->limit(50),
                Tables\Columns\TextColumn::make('views_count')->label('Jumlah Dilihat')->sortable(),
            ]);
    }
}