<?php
namespace App\Filament\Widgets;
use App\Models\ApiLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopApiEndpoints extends BaseWidget
{
    protected static ?int $sort = 2; // Urutan di dasbor
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->recordKey('route')    
            ->query(
                ApiLog::query()
                    ->select('route', DB::raw('count(*) as count'), DB::raw('avg(duration_ms) as avg_duration'))
                    ->groupBy('route')
                    ->orderByDesc('count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('route')->label('Endpoint'),
                Tables\Columns\TextColumn::make('count')->label('Jumlah Panggilan')->sortable(),
                Tables\Columns\TextColumn::make('avg_duration')->label('Rata-rata Durasi (ms)')->sortable(),
            ]);
    }
}