<?php

namespace App\Filament\Widgets;

use App\Models\ApiLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder; // <-- Pastikan ini di-import

class TopApiEndpoints extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            // =========================================================
            // ==> PERBAIKAN UTAMA ADA DI DALAM QUERY INI <==
            // =========================================================
            ->query(
                // Kita akan menggunakan Eloquent Builder, bukan model langsung
                // untuk memberikan fleksibilitas lebih
                function (Builder $query) {
                    return $query->from((new ApiLog())->getTable())
                        ->selectRaw('route, count(*) as count, avg(duration_ms) as avg_duration')
                        ->groupBy('route')
                        ->orderByDesc('count')
                        ->limit(10);
                }
            )
            ->columns([
                Tables\Columns\TextColumn::make('route')->label('Endpoint'),
                Tables\Columns\TextColumn::make('count')->label('Jumlah Panggilan'),
                Tables\Columns\TextColumn::make('avg_duration')
                    ->label('Rata-rata Durasi (ms)')
                    // Format angka agar lebih rapi
                    ->formatStateUsing(fn (string $state): string => number_format((float) $state, 2)),
            ])
            // Kita tidak perlu paginasi karena kita hanya mengambil 10 teratas
            ->paginated(false);
    }
}